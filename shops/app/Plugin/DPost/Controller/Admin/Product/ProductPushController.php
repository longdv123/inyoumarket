<?php

/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Controller\Admin\Product;

use Eccube\Application;

/**
 * 入荷通知のコントローラクラス
 */
class ProductPushController
{
    /**
     * 一覧を表示する。
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app)
    {
        $this->app = $app;


        // 一覧取得
        $ProductPostQueues = $app['eccube.repository.productpostqueue']->getProductList();

        foreach ($ProductPostQueues as $ProductPostQueue) {

            $product_id = $ProductPostQueue['product_id'];
            $pcount = $ProductPostQueue['pcount'];

            /* @var $ProductPush \Plugin\DPost\Entity\ProductPush */
            $ProductPush = $app['eccube.repository.productpush']->findOneBy(array('product_id' => $product_id));

            if(empty($ProductPush)) {

                $Product = $app['eccube.repository.product']->find($product_id);

                // 新規作成
                $ProductPush = new \Plugin\DPost\Entity\ProductPush();
                $ProductPush->setProductId($product_id);
                $ProductPush->setWaitCount($pcount);
                $ProductPush->setPushCount(0);
                $ProductPush->setClick(0);
                $ProductPush->setProduct($Product);

                $em = $this->app['orm.em'];

                $em->persist($ProductPush);

                // 対象アップデート
                $ProductPostQueueList = $app['eccube.repository.productpostqueue']->findBy(array('product_id' => $product_id, 'queue_status' => 0));

                /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
                foreach ($ProductPostQueueList as $ProductPostQueue) {

                    $ProductPostQueue->setQueueStatus(1);
                    $ProductPostQueue->setUpdateDate(new \DateTime());
                    $em->persist($ProductPostQueue);
                }

                $em->flush();

            } else {

                // 更新
                $em = $this->app['orm.em'];

                $waitCount = $ProductPush->getWaitCount();

                $ProductPush->setWaitCount($waitCount + $pcount);

                $em->persist($ProductPush);

                // 対象アップデート
                $ProductPostQueueList = $app['eccube.repository.productpostqueue']->findBy(array('product_id' => $product_id, 'queue_status' => 0));

                /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
                foreach ($ProductPostQueueList as $ProductPostQueue) {

                    $ProductPostQueue->setQueueStatus(1);
                    $ProductPostQueue->setUpdateDate(new \DateTime());
                    $em->persist($ProductPostQueue);
                }

                $em->flush();
            }

        }

        $ProductPushList = $app['eccube.repository.productpush']->findBy(array());

//         \Doctrine\Common\Util\Debug::dump($ProductPostQueueList[0]);exit;

        $form = $app->form()->getForm();

        return $app->render('DPost/Resource/template/admin/Product/product_push.twig', array(
            'form' => $form->createView(),
            'ProductPushList' => $ProductPushList,
        ));
    }

    private function setErrorMsg() {
        $this->app->addError("API Keyが未設定です。プラグインの設定をご確認ください。", 'admin');
        return $this->app->redirect($this->app['url_generator']->generate('admin_content_news_ex'));
    }

    /**
     * 通知の送信を行う
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function push(Application $app, $id = null) {

        $this->app = $app;

        // APIキー取得
        /* @var $DpostSetting \Plugin\DPost\Entity\DpostSetting */
        $DpostSetting = $app['eccube.repository.dpostsetting']->findOneBy(array());

        if(empty($DpostSetting)) {
            return $this->setErrorMsg();
        }

        $apiKey = $DpostSetting->getApiKey();

        // 設定値チェック
        if(empty($apiKey)) {
            return $this->setErrorMsg();
        }


        // product_id
        $product_id = $id;

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $app['dpost.service.dpostservice'];

        // 送信
        // 顧客情報から送信対象を取得
        // 500人単位で送信を行う(MAX:1000)
        // MessageController にて通知メッセージ情報の返却を行う


        // 対象顧客取得
        $PostQueueList = $DpostService->getPushProductCustomer($product_id);

        if(count($PostQueueList) == 0) {
            // 送信しない
            $app->addInfo('送信可能な顧客が存在しません。', 'admin');
            return $this->app->redirect($this->app['url_generator']->generate('admin_product_push'));
        }


        $url = "https://android.googleapis.com/gcm/send";

        $header = array();
        $header[] = 'Content-Type: application/json';
        $header[] = "Authorization: key={$apiKey}";

        // 送信件数カウント
        $sendCount = 0;

        // 送信処理
        foreach ($PostQueueList as $arrSendGroup) {

            $sendId = implode("\",\"", $arrSendGroup['regist_id']);

            $data = "{\"registration_ids\":[\"". $sendId . "\"]}";

            // 通知
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_exec($ch);

            if (! curl_errno ( $ch )) {

                $sendCount += count($arrSendGroup['regist_id']);

                $em = $this->app['orm.em'];

                // 通知完了分をDBへ更新
                foreach ( $arrSendGroup['regist_id'] as $key => $val ) {

                    $regist_id = $val;
                    $product_id = $arrSendGroup['product_id'][$key];

                    /* @var $ProductPostQueue \Plugin\DPost\Entity\ProductPostQueue */
                    $ProductPostQueue = $this->app['eccube.repository.productpostqueue']->findOneBy(
                            array('regist_id' => $regist_id, 'product_id' => $product_id, 'queue_status' => 1));

                    $ProductPostQueue->setQueueStatus(2);
                    $ProductPostQueue->setUpdateDate(new \DateTime());

                    $em->persist($ProductPostQueue);
                }

                $em->flush();

            } else {
                // TODO: 送信失敗時処理
            }

        }

        // 送信件数を反映


        // 送信件数をNewsExへ反映
        /* @var $ProductPush \Plugin\DPost\Entity\ProductPush */
        $ProductPush = $DpostService->getProductPush($product_id);

        $em = $this->app['orm.em'];

        if(empty($ProductPush)) {
            $ProductPush = new \Plugin\DPost\Entity\ProductPush;

            $ProductPush->setProductId($product_id);
            $ProductPush->setWaitCount(0);
            $ProductPush->setPushCount($sendCount);
            $ProductPush->setClick(0);
        } else {
            $nowCount = $ProductPush->getPushCount();
            $ProductPush->setWaitCount(0);
            $ProductPush->setPushCount($nowCount + $sendCount);
        }

        $em->persist($ProductPush);
        $em->flush();


        $app->addSuccess('送信完了しました。', 'admin');
        return $this->app->redirect($this->app['url_generator']->generate('admin_product_push'));
    }

}