<?php

namespace Plugin\ContactList\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;

class ContactListController extends AbstractController {
    public function __construct() {
    }

    public function index( Application $app, Request $request, $page_no = null ) {
        $session = $request->getSession();

        $builder    = $app['form.factory']->createBuilder( 'contact_search' );
        $searchForm = $builder->getForm();

        $pagination = array();

        $disps       = $app['eccube.repository.master.disp']->findAll();
        $pageMaxis   = $app['eccube.repository.master.page_max']->findAll();
        $page_count  = $app['config']['default_page_count'];
        $page_status = null;
        $active      = false;
        if ( 'POST' === $request->getMethod() ) {

            $searchForm->handleRequest( $request );

            if ( $searchForm->isValid() ) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $app['contact_list.repository.contact_list']->getQueryBuilderBySearchDataForAdmin( $searchData );

                $page_no    = 1;
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count
                );

                // sessionのデータ保持
                $session->set( 'eccube.admin.contacts.search', $searchData );
            }
        } else {
            if ( is_null( $page_no ) ) {
                // sessionを削除
                $session->remove( 'eccube.admin.contacts.search' );
            } else {
                // pagingなどの処理
                $searchData = $session->get( 'eccube.admin.contacts.search' );
                if ( ! is_null( $searchData ) ) {
                    // 表示件数
                    $pcount = $request->get( 'page_count' );

                    $page_count = empty( $pcount ) ? $page_count : $pcount;

                    $qb = $app['contact_list.repository.contact_list']->getQueryBuilderBySearchDataForAdmin( $searchData );

                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count
                    );

                    // セッションから検索条件を復元
                    $searchForm->setData( $searchData );
                }
            }
        }

//dump($pagination);
        return $app->render( 'ContactList/View/admin/contact_list.twig', array(
            'searchForm'  => $searchForm->createView(),
            'pagination'  => $pagination,
            'disps'       => $disps,
            'pageMaxis'   => $pageMaxis,
            'page_no'     => $page_no,
            'page_status' => $page_status,
            'page_count'  => $page_count,
            'active'      => $active,
        ) );

    }

}
