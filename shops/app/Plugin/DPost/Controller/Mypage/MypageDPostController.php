<?php

/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Controller\Mypage;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Exception\CartException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MypageDPostController extends AbstractController
{

    /**
     * @param Application $app
     * @param Request     $request
     *
     * @return string
     */
    public function index(Application $app, Request $request)
    {
        $this->app = $app;

        $Customer = $app['user'];

        // 顧客ID取得
        $customer_id = $Customer->getId();

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $app['dpost.service.dpostservice'];

        /* @var $CustomerEx \Plugin\DPost\Entity\CustomerEx */
        $CustomerEx = $DpostService->getCustomerEx($customer_id);

        if(empty($CustomerEx)) {
            $CustomerEx = new \Plugin\DPost\Entity\CustomerEx();
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $app['form.factory']->createBuilder('dposttype', $CustomerEx)->getForm();

        if('POST' == $request->getMethod()) {

            $form->handleRequest($request);

            if($form->isValid()) {

                $formData = $form->getData();

                switch ($request->get('mode')) {
                    case 'setting':
                        // 設定

                        $CustomerEx->setCustomerId($customer_id);
                        $CustomerEx->setRegistId($request->get('regist_id'));

                        // registid 登録
                        $em = $this->app['orm.em'];
                        $em->persist($CustomerEx);
                        $em->flush();

                        break;
                    case 'delete':
                        // 設定削除
                        $em = $this->app['orm.em'];
                        $em->remove($CustomerEx);
                        $em->flush();

                        break;
                }

                return $this->app->redirect($this->app['url_generator']->generate('mypage_dpost'));
            }

            if($request->get('mode') == 'stop_check') {
                // 停止確認

                $arrResult = array();
                if($this->isStop($request->get('regist_id'))) {
                    $arrResult['ret'] = 1;
                } else {
                    $arrResult['ret'] = 0;
                }

                $response = new Response(json_encode($arrResult));
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }

        }


        // RegistId取得
        $regist_id = $DpostService->getRegistId($customer_id);

        return $app->render('DPost/Resource/template/default/Mypage/dpost.twig', array(
            'form' => $form->createView(),
            'regist_id' => $regist_id,
        ));
    }

    public function check(Application $app, Request $request)
    {
        $this->app = $app;

        if($request->get('mode') == 'stop_check' && $request->get('regist_id') != "") {
            // 停止確認

            $arrResult = array();
            if($this->isStop($request->get('regist_id'))) {
                $arrResult['ret'] = 1;
            } else {
                $arrResult['ret'] = 0;
            }

            $response = new Response(json_encode($arrResult));
            $response->headers->set('Content-Type', 'application/json');

        } else {

            $arrResult = array();
            $arrResult['ret'] = -1;

            $response = new Response(json_encode($arrResult));
            $response->headers->set('Content-Type', 'application/json');
        }
        return $response;
    }

    /**
     * 停止か削除か判定
     *
     * @param unknown $regist_id
     * @return boolean true:停止 false:削除
     */
    public function isStop($regist_id) {

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $this->app['dpost.service.dpostservice'];

        // 商品の通知ONがあるかチェック
        $registIdCount = $DpostService->getRegistIdCountProduct($regist_id);

        if($registIdCount > 0) {
            // 停止のみ
            return true;
        } else {
            // 削除可能
            return false;
        }

    }

}
