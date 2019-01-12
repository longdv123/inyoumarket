<?php

namespace Plugin\ContactList\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactEditController extends AbstractController
{
    public function __construct()
    {
    }

    public function index(Application $app, Request $request, $contact_id)
    {
        $plg_contacts = $app['contact_list.repository.contact_list']->find($contact_id);
        if (is_null($plg_contacts)) {
            throw new NotFoundHttpException();
        }
        $builder = $app['form.factory']->createBuilder('contact_edit', $plg_contacts);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            // 登録ボタン押下
            switch ($request->get('mode')) {
                case 'register':
                    if ($form->isValid()) {
                        $app['orm.em']->persist($plg_contacts);
                        $app['orm.em']->flush();
                        return $app->redirect($app->url('admin_contacts_page', array('page_no' => $request->get('page_no'))));
                    }
                    break;
                default:
                    break;
            }
        }

        return $app->render('ContactList/View/admin/contact_edit.twig', array(
            'form' => $form->createView(),
            'id' => $contact_id,
            'plg_contacts' => $plg_contacts,
        ));
    }
}