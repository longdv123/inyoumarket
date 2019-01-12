<?php

namespace Plugin\ContactList\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactReplyController extends AbstractController
{
    public function __construct()
    {
    }

    public function index(Application $app, Request $request, $contact_id)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();
        $plg_contacts = $app['contact_list.repository.contact_list']->find($contact_id);
        if (is_null($plg_contacts)) {
            throw new NotFoundHttpException();
        }
        $builder = $app['form.factory']->createBuilder('contact_reply');

        $form = $builder->getForm();

        $form->handleRequest($request);
        if ('POST' === $request->getMethod()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    $builder->setAttribute('freeze', true);     // <=効かない（たぶんコアシステムのどこかにバグがある）
                    $form = $builder->getForm();
                    $form->handleRequest($request);
                    return $app->render('ContactList/View/admin/contact_reply_confirm.twig', array(
                        'form' => $form->createView(),
                        'id' => $contact_id,
                        'plg_contacts' => $plg_contacts,
                        'data' => $form->getData(),
                    ));
                case 'complete':

                    $data = $form->getData();
                    // メール送信
                    $body = $app->renderView('ContactList/View/admin/reply_mail.twig', array(
                        'data' => $data,
                        'plg_contacts' => $plg_contacts,
                    ));

                    // 問い合わせ者にメール送信
                    $message = \Swift_Message::newInstance()
                                             ->setSubject($data['subject'] . sprintf(">[お問い合わせ番号：%d]",$contact_id))
                                             ->setFrom(array($BaseInfo->getEmail02() => $BaseInfo->getShopName()))
                                             ->setTo(array($plg_contacts['email']))
                                             ->setBcc($BaseInfo->getEmail02())
                                             ->setReplyTo($BaseInfo->getEmail02())
                                             ->setReturnPath($BaseInfo->getEmail04())
                                             ->setBody($body);

                    $app->mail($message);

                    $ContactReply = new \Plugin\ContactList\Entity\ContactReply();
                    // エンティティを更新
                    $ContactReply
                        ->setInsTime(new \DateTime())
                        ->setReplyContactId($plg_contacts)
                        ->setReplyMember($app->user())
                        ->setReplySubject($data['subject'])
                        ->setReplyContents($data['contents'])
                        ->setReplyMemo($data['memo']);
                    // DB更新
                    $app['orm.em']->persist( $ContactReply );
                    $app['orm.em']->flush( $ContactReply );
                    return $app->redirect($app->url('admin_contacts_edit',array('contact_id' => $contact_id, 'page_no' => $request->get('page_no'))));
                case 'back':
                    return $app->redirect($app->url('admin_contacts_edit',array('contact_id' => $contact_id, 'page_no' => $request->get('page_no'))));
                default:
                    break;
            }
        }
        return $app->render('ContactList/View/admin/contact_reply.twig', array(
            'form' => $form->createView(),
            'id' => $contact_id,
            'plg_contacts' => $plg_contacts,
        ));
    }
}