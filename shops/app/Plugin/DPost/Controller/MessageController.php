<?php

/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractController
{
    public function message(Application $app, Request $request) {

        $this->app = $app;

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $app['dpost.service.dpostservice'];

        $regist_id = $request->get('regist_id');

        // 新着情報
        $title = "新着のお知らせ";
        $message = "新着のお知らせがあります";
        $open_url = "";

        $PostQueueList = $DpostService->getPushMessage($regist_id);

        if(!empty($PostQueueList)) {

            /**
             * 新着情報通知がある場合
             *
             */

            /* @var $PostQueue \Plugin\DPost\Entity\PostQueue */
            $PostQueue = $PostQueueList[0];

            $news_id = $PostQueue->getNewsId();

            /* @var $News \Eccube\Entity\News */
            $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
            $softDeleteFilter->setExcludes(array(
                    'Eccube\Entity\News'
            ));
            $News = $DpostService->getNews($news_id);

            if(!empty($News)) {

                $title = $News->getTitle();
                $message = $News->getComment();
                $open_url = $this->app['url_generator']->generate('dpost_redirect')."?regist_id=".$regist_id."&news_id=".$news_id."&mode=1";

                // PostQueueを更新
                $PostQueue->setQueueStatus(1); // メッセージ到達
                $PostQueue->setUpdateDate(new \DateTime());

                $em = $this->app['orm.em'];
                $em->persist($PostQueue);
                $em->flush();

            }

        } else {

            /**
             * 新着情報通知がない場合、入荷通知
             *
             */

            $PostQueueList = $DpostService->getProductPushMessage($regist_id);

            if(!empty($PostQueueList)) {

                /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
                $ProductPostQueue = $PostQueueList[0];

                $product_id = $ProductPostQueue->getProductId();

                // メッセージ生成
                $title = "入荷のお知らせ";
                $message = $ProductPostQueue->getProduct()->getName()."が入荷されました。";
                $open_url = $this->app['url_generator']->generate('dpost_redirect')."?regist_id=".$regist_id."&product_id=".$product_id."&mode=2";

                // ProductPostQueueを更新
                $ProductPostQueue->setQueueStatus(3);
                $ProductPostQueue->setUpdateDate(new \DateTime());

                $em = $this->app['orm.em'];
                $em->persist($ProductPostQueue);
                $em->flush();
            }

        }

        $arrResult = array();
        $arrResult['title'] = urlencode($title);
        $arrResult['body'] = urlencode($message);
        $arrResult['open_url'] = $open_url;
        $arrResult['img_name'] = "log-192x192.png";

        $response = new Response(json_encode($arrResult));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function redirectPage(Application $app, Request $request) {

        $this->app = $app;

        $mode = $request->get('mode');
        $news_id = $request->get('news_id');
        $regist_id = $request->get('regist_id');
        $product_id = $request->get('product_id');

        switch ($mode) {
            case 2:
                // 入荷通知の場合
                return $this->redirectPageProduct($regist_id, $product_id);
                break;
            case 1:
            default:
                // 新着情報通知
                return $this->redirectPageNews($regist_id, $news_id);
                break;
        }
    }

    private function redirectPageNews($regist_id, $news_id) {

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $this->app['dpost.service.dpostservice'];

        $PostQueueList = $DpostService->getPostQueue($regist_id, $news_id);

        if(!empty($PostQueueList)) {

            $em = $this->app['orm.em'];
            $em->getConnection()->beginTransaction();

            /* @var $PostQueue \Plugin\DPost\Entity\PostQueue */
            $PostQueue = $PostQueueList[0];

            /* @var $NewsEx \Plugin\DPost\Entity\NewsEx */
            $NewsEx = $DpostService->getNewsEx($news_id);

            // PostQueue更新
            $PostQueue->setQueueStatus(2); // クリック済み
            $PostQueue->setUpdateDate(new \DateTime());
            $em->persist($PostQueue);
            $em->flush();

            // NewsExの更新
            $clickCount = $DpostService->getClickCount($news_id);
            $NewsEx->setClick($clickCount);
            $em->persist($NewsEx);
            $em->flush();

            $em->getConnection()->commit();

            // News取得
            /* @var $News \Eccube\Entity\News */
            $News = $DpostService->getNews($news_id);

            // リダイレクトURL取得
            $url = $News->getUrl();
            if(!empty($url)) {
                // 指定ページへ
                return $this->app->redirect($News->getUrl());

            } else {
                // TOPページへ
                return $this->app->redirect($this->app['url_generator']->generate('homepage'));
            }

        }

        // TOPページへ
        return $this->app->redirect($this->app['url_generator']->generate('homepage'));
    }

    private function redirectPageProduct($regist_id, $product_id) {

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $this->app['dpost.service.dpostservice'];

        $PostQueueList = $DpostService->getProductPostQueueSend($regist_id, $product_id);

        if(!empty($PostQueueList)) {

            $em = $this->app['orm.em'];
            $em->getConnection()->beginTransaction();

            /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
            $ProductPostQueue = $PostQueueList[0];

            /* @var $ProductPush \Plugin\DPost\Entity\ProductPush */
            $ProductPush = $DpostService->getProductPush($product_id);

            // PostQueue更新
            $ProductPostQueue->setQueueStatus(4); // クリック済み
            $ProductPostQueue->setUpdateDate(new \DateTime());
            $em->persist($ProductPostQueue);
            $em->flush();

            // ProductPushの更新
            $clickCount = $DpostService->getProductClickCount($product_id);
            $ProductPush->setClick($clickCount);
            $em->persist($ProductPush);
            $em->flush();

            $em->getConnection()->commit();


            // リダイレクト
            return $this->app->redirect($this->app['url_generator']->generate('product_detail', array('id'=>$product_id)));
        }

        if(empty($product_id)) {
            // TOPページへ
            return $this->app->redirect($this->app['url_generator']->generate('homepage'));
        } else {
            return $this->app->redirect($this->app['url_generator']->generate('product_detail', array('id'=>$product_id)));
        }

    }

}
