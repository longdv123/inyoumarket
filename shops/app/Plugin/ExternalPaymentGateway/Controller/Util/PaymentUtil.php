<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Controller\Util;

/**
 * 決済モジュール用 汎用関数クラス
 */
class PaymentUtil
{

    private $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }


    /**
     * SSL決済からパラメータが返却時の初期設定を行う
     */
    function getRecvData()
    {

        $RecvExtension = new \Plugin\ExternalPaymentGateway\Controller\DataObj\RecvExtension();

        $BaseInfo = $this->app['eccube.repository.base_info']->get();

        // メールアドレスが設定されていない場合は送信しない
        $RecvExtension->setMailTo($BaseInfo->getEmail04());
        if (!$RecvExtension->getMailTo()) {
            $RecvExtension->setSendFlg(0);
        } else {
            // 設定に従う
            $sendFlg = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_ERRORMAIL_FLG'];
            $RecvExtension->setSendFlg($sendFlg);
        }

        $RecvExtension->setRemoteIp();

        //パラメータ取得
        $RecvExtension->setParam("clientip");
        $RecvExtension->setParam("cont");
        $RecvExtension->setParam("rel");
        $RecvExtension->setParam("money");
        $RecvExtension->setParam("sendid");
        $RecvExtension->setParam("oneclick");
        $RecvExtension->setParam("option");

        //初期化
        $RecvExtension->setErrorCode(0);
        $RecvExtension->setErrorMsg("");
        $RecvExtension->setMailBody("");

        return $RecvExtension;
    }


    /**
     * 支払方法情報を取得する
     *
     * @param integer $paymentId 支払いID
     * @return array 支払方法情報。決済モジュール管理対象である場合、内部識別コードを同時に設定する
     */
    function getPaymentInfo($paymentId)
    {
        $Payment = $this->app['orm.em']->getRepository('Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')
            ->findOneBy(array('id' => $paymentId));
        if (empty($Payment)) {
            return false;
        }
        $PaymentExtension = new \Plugin\ExternalPaymentGateway\Controller\DataObj\PaymentExtension();
        $PaymentExtension->setExternalPaymentMethod($Payment);
        return $PaymentExtension;
    }

    function getPaymentTypeConfig($payment_id)
    {
        $PaymentExtension = $this->getPaymentInfo($payment_id);
        return $PaymentExtension;
    }

    /**
     * 決済モジュールで利用出来る決済方式のクライアントIP一覧を取得する
     *
     * @return array クライアントIP
     */
    function getPaymentClientIp($clientIp)
    {
        $constExternalPG = $this->app['config']['ExternalPaymentGateway']['const'];

        return array(
            $constExternalPG['EXTERNAL_CREDIT_SSL_CODE'] => $clientIp,
            $constExternalPG['EXTERNAL_CREDIT_SPEED_CODE'] => $clientIp,
        );

    }

    /**
     * 決済モジュールで利用出来る決済方式の名前一覧を取得する
     *
     * @param 
     * @return array 支払方法
     */
    function getPaymentTypeNames()
    {
        $payments =  array(
            $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE'] => $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE_NAME'],
            $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE'] => $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE_NAME'],
        );
        return $payments;
    }

    function printLog($msg)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = print_r($msg, true);
        }
        $objMdl =& PG_MULPAY_Ex::getInstance();
        $objMdl->printLog($msg);
    }
}
