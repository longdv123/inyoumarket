<?php

namespace Plugin\ContactList\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfigController extends AbstractController
{
    public function __construct()
    {
    }

    public function index(Application $app, Request $request)
    {
        $builder = $app['form.factory']
            ->createBuilder('admin_contact_list_config');

        $ContactStatusesOrg = $app['contact_list.repository.contact_status']->findAll();
        $ContactStatuses = $ContactStatusesOrg;

        //追加用に空エンティティ追加
        $ContactStatus = new \Plugin\ContactList\Entity\ContactStatus();
        $ContactStatuses[] = $ContactStatus;
        $ContactStatuses[] = clone $ContactStatus;
        $ContactStatuses[] = clone $ContactStatus;

        $builder->get('contact_status')->setData($ContactStatuses);
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $updateId = array();
                foreach ($ContactStatuses as $ContactStatus) {
                    if ($ContactStatus->getId()) {
                        $updateId[$ContactStatus->getId()] = 1;
                        $app['orm.em']->persist($ContactStatus);
                    }
                }
                foreach ($ContactStatusesOrg as $ContactStatusOrg) {
                    if (!array_key_exists($ContactStatusOrg->getId(), $updateId)) {
                        $app['orm.em']->remove($ContactStatusOrg);
                    }
                }
                try {
                    $app['orm.em']->flush();
                    $app->addSuccess('保存しました', 'admin');
                    return $app->redirect($app->url('plugin_ContactList_config'));
                } catch (\Exception $e) {
                    // 外部キー制約などで削除できない場合に例外エラーになる
                    $app->addError('admin.register.failed', 'admin');
                }

            }
        }

        return $app->render('ContactList/View/admin/config.twig', array(
            'form' => $form->createView(),
        ));
    }
}