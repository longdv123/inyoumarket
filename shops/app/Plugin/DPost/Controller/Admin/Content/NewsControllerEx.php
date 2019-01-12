<?php

/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Controller\Admin\Content;

use Eccube\Application;

/**
 * 新着情報のコントローラクラス
 */
class NewsControllerEx
{
    /**
     * 新着情報一覧を表示する。
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app)
    {
        $this->app = $app;

        $NewsList = $app['eccube.repository.news']->findBy(array(), array('rank' => 'DESC'));

        $em = $app['orm.em'];

        /* @var $News \Eccube\Entity\News */
        foreach ($NewsList as $News) {

            // NewsEx存在チェック
            $NewsEx = $app['eccube.repository.newsex']->findOneBy(array('news_id' => $News->getId()));

            if(empty($NewsEx)) {
                $NewsEx = new \Plugin\DPost\Entity\NewsEx();

                $NewsEx->setNewsId($News->getId());
                $NewsEx->setPushStatus(0);
                $NewsEx->setPushCount(0);
                $NewsEx->setClick(0);
                $NewsEx->setEndNumber(0);
                $NewsEx->setNews($News);
                $em->persist($NewsEx);
            }

        }
        $em->flush();

        /* @var $NewsListEx \Plugin\DPost\Entity\NewsEx */
        $arrNewsListEx = $app['eccube.repository.newsex']->getNewsList();

        // 対象顧客取得
        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $app['dpost.service.dpostservice'];
        $pushCount = $DpostService->getPushCount();

        $form = $app->form()->getForm();

        return $app->render('DPost/Resource/template/admin/Content/news.twig', array(
            'form' => $form->createView(),
            'NewsList' => $arrNewsListEx,
            'pushCount' => $pushCount,
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


        // News ID
        $news_id = $id;

        /* @var $DpostService \Plugin\DPost\Service\DPostService */
        $DpostService = $app['dpost.service.dpostservice'];

        // 送信
        // 顧客情報から送信対象を取得
        // 500人単位で送信を行う(MAX:1000)
        // MessageController にて通知メッセージ情報の返却を行う


        // 対象顧客取得
        $PostQueueList = $DpostService->getPushCustomer($news_id);

        if(count($PostQueueList) == 0) {
            // 送信しない
            $app->addInfo('送信可能な顧客が存在しません。', 'admin');
            return $this->app->redirect($this->app['url_generator']->generate('admin_content_news_ex'));
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

                // 通知完了分をDBへ登録
                foreach ( $arrSendGroup['regist_id'] as $key => $val ) {

                    $regist_id = $val;
                    $customer_id = $arrSendGroup['customer_id'][$key];

                    $PostQueue = new \Plugin\DPost\Entity\PostQueue();
                    $PostQueue->setRegistId($regist_id);
                    $PostQueue->setNewsId($news_id);
                    $PostQueue->setCustomerId($customer_id);
                    $PostQueue->setQueueStatus(0);
                    $PostQueue->setCreateDate(new \DateTime());
                    $PostQueue->setUpdateDate(new \DateTime());

                    $em->persist($PostQueue);
                }

                $em->flush();

            } else {
                // TODO: 送信失敗時処理
            }

        }

        // 送信件数をNewsExへ反映
        /* @var $NewsEx \Plugin\DPost\Entity\NewsEx */
        $NewsEx = $DpostService->getNewsEx($news_id);

        $em = $this->app['orm.em'];

        // 件数設定
        $NewsEx->setPushCount($sendCount);
        // ステータスを送信済へ設定
        $NewsEx->setPushStatus(1);

        $em->persist($NewsEx);
        $em->flush();


        $app->addSuccess('送信完了しました。', 'admin');
        return $this->app->redirect($this->app['url_generator']->generate('admin_content_news_ex'));
    }

}