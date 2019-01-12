<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Controller;

use Eccube\Application;
use Plugin\ExternalPaymentGateway\Controller\Util\CommonUtil;
use Plugin\ExternalPaymentGateway\Controller\Util\PaymentUtil;
use Plugin\ExternalPaymentGateway\Controller\Util\PluginUtil;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller to handle result reception
 */
class PaymentRecvController
{

    public $app;

    /** @var \Eccube\Entity\BaseInfo */
    public $BaseInfo;

    /**
     * Edit config
     *
     * @param Application $app
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function index(Application $app, Request $request, $id = null)
    {
        $this->app = $app;
        $this->BaseInfo = $app['eccube.repository.base_info']->get();

        if ($request->getMethod() === 'GET') {

            // GETパラメータを取得
            $requestData = $request->query->all();
            if (!empty($requestData)) {

                // 初期値セット
                $objUtil = new PaymentUtil($this->app);
                $RecvExtension = $objUtil->getRecvData();

                // パラメータのチェック
                $chkRet = $this->checkRequestData($RecvExtension);
                if (false === $chkRet) {

                    // エラーコードがセットされているか
                    if($RecvExtension->getErrorCode()) {

                        // エラーメール送信がONになっているか
                        if($RecvExtension->getSendFlg() == 1){
                            // エラーメール送信
                            $this->sendErrorMail($RecvExtension);
                        }
                    }
                } else {
                // 成功

                    // plg_external_order_payment を更新
                    $this->doRecvExternalCredit($RecvExtension->getExternalOrderPayment());
                    $Order = $RecvExtension->getOrder();
                    // ステータスを新規受付に更新
                    $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_new']));
                    $this->app['orm.em']->persist($Order);
                    $this->app['orm.em']->flush();

                    // オーダー完了メール送信
                    $this->sendOrderMail($Order);

                }
            }
        }

        print("SuccessOK");
        exit;

// for debug end
        //    return $app['view']->render('ExternalPaymentGateway/View/test.twig', array(
        //                'form' => $form->createView(),
        //    ));

    }

    /**
     * external complete
     *
     * @param Application $app
     * @return type
     */
    public function complete(Application $app)
    {
        $this->app = $app;

        // GET パラメータを取得
        $requestData = $this->app['request']->query->all();

        //戻りURLの引数
        $tpl_uniqid = (isset($requestData["id"])) ? $requestData["id"] : null ;

        $externalOrderPaymentRepo = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment');
        $externalOrderPaymentRepo->setConfig($this->app['config']['ExternalPaymentGateway']['const']);

        // idからデータ取得(memo01の取得)
        $arrOrderData = $externalOrderPaymentRepo->getOrderForCredit($tpl_uniqid);

        //不正ユニークIDチェック
        if((!isset($arrOrderData[0]["id"])) || 
           (!isset($arrOrderData[0]["memo01"]))){

            $error_message  = "決済エラーが発生しました。決済に失敗している可能性がございますので、詳細についてはサイト管理者にご確認下さい。";
            $error_title   = "エラー";

            return $this->app['view']->render('error.twig', array('error_message' => $error_message ,'error_title' => $error_title ,));
        }
        // 完了画面表示
        return $app->redirect($app->url('shopping_complete'));

    }

    /**
     * Check Get Parameters
     * @param RecvExtension
     * @return bool 
     */
    function checkRequestData($RecvExtension)
    {

        // 会社名をここで取得しておく
        $companyName = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_COMPANY_NAME'];

        $addErrorMsg = "----------------------------------\n";
        $addErrorMsg.= "上記エラーの為、注文が確定できていません。\n\n";
        $addErrorMsg.= "また、クレジット決済が成功している可能性がございますので、\n";
        $addErrorMsg.= "集計システムにて決済状況の確認をお願い致します。\n";
        $addErrorMsg.= "成功している場合はお客様にご購入の意思を確認頂き、\n";
        $addErrorMsg.= "購入の意思がない場合は返金処理を行ってください。\n";

        // 必須パラメータのチェック
        if(Util\CommonUtil::isBlank($RecvExtension->getParam("clientip")) || 
           Util\CommonUtil::isBlank($RecvExtension->getParam("sendid")) || 
           !Util\CommonUtil::isInt($RecvExtension->getParam("sendid")) || 
           Util\CommonUtil::isBlank($RecvExtension->getParam("money")) || 
           !Util\CommonUtil::isInt($RecvExtension->getParam("money")) || 
           Util\CommonUtil::isBlank($RecvExtension->getParam("rel")) || 
           Util\CommonUtil::isBlank($RecvExtension->getParam("cont"))
        ){
            $RecvExtension->setErrorCode(101);
            $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(引数不正)");
            $mailBody = "不正な決済通知が送られました。\n";
            $mailBody .= $addErrorMsg;
            $RecvExtension->setMailBody($mailBody);
            return false;
        }

        // 決済失敗か
        if($RecvExtension->getParam("rel") != "yes") {
            $RecvExtension->setErrorCode(102);
            $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(引数不正)");
            $mailBody = "決済処理が正常に完了しませんでした。\n";
            $mailBody .= "詳細については".$companyName."の管理画面からご確認下さい。\n";
            $RecvExtension->setMailBody($mailBody);
            return false;
        }

        // リモートIPチェック
        $ipCheckflg = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_IPCHECK_FLG'];
        if($ipCheckflg) {
            $ipArrow = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_IP_ARROW'];
            $ipDatas = array();
            $ipDatas = explode(',', $ipArrow);

            $checkIpFlg = false;
            $remoteAddr = $RecvExtension->getRemoteIp();
            foreach ($ipDatas as $data) {
                if ($this->checkIpNet($remoteAddr ,$data)) {
                    $checkIpFlg = true;
                    break;
                }
            }

            if(!$checkIpFlg){

                $RecvExtension->setErrorCode(201);
                $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(リクエストIP不正)");
                $mailBody = "不正にアクセスされている可能性がございます。\n";
                $mailBody .= $companyName."にご確認下さい。\n";
                $mailBody .= "remoteip:".$remoteAddr."\n";
                $RecvExtension->setMailBody($mailBody);
                return false;
            }
        }

        $externalOrderPaymentRepo = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment');
        $externalOrderPaymentRepo->setConfig($this->app['config']['ExternalPaymentGateway']['const']);

        //一時受注テーブルの読込
        //決済方法確認(スピード決済 or 都度決済)
        if($RecvExtension->getParam("oneclick") == "yes") {

            //決済タイプチェック(スピード決済)
            if($RecvExtension->getParam("cont") == "no"){
                $RecvExtension->setErrorCode(104);
                $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(決済タイプ不正)");
                $mailBody = "不正な決済通知が送られました。\n";
                $mailBody .= "スピード決済ではない可能性がございます。\n";
                $mailBody .= $companyName."にご確認下さい。\n";
                $RecvExtension->setMailBody($mailBody);
                return false;
            }

            //引数にoptionが存在するかどうか
            if((!Util\CommonUtil::isBlank($RecvExtension->getParam("option"))) &&
               (Util\CommonUtil::isInt($RecvExtension->getParam("option")))) {

                // オーダーID取得
                $externalOrderId = $externalOrderPaymentRepo->getOrderIdForRecv($RecvExtension->getParam("option"), $RecvExtension->getParam("sendid"));

            }
            else{
                //引数チェック
                $RecvExtension->setErrorCode(101);
                $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(引数不正)");
                $mailBody = "不正な決済通知が送られました。\n";
                $mailBody .= $addErrorMsg;
                $RecvExtension->setMailBody($mailBody);
                return false;
            }
            // OrderIdを設定(option)
            $RecvExtension->setOrderId($RecvExtension->getParam("option"));
        } else {

            //決済タイプチェック(都度決済)
            if($RecvExtension->getParam("cont") == "yes"){
                $RecvExtension->setErrorCode(103);
                $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(決済タイプ不正)");
                $mailBody = "不正な決済通知が送られました。\n";
                $mailBody .= "都度決済ではない可能性がございます。\n";
                $mailBody .= $companyName."にご確認下さい。\n";
                $RecvExtension->setMailBody($mailBody);
                return false;
            }

            //都度決済
            $externalOrderId = $externalOrderPaymentRepo->getOrderIdForRecv($RecvExtension->getParam("sendid"));

            // OrderIdを設定(sendid)
            $RecvExtension->setOrderId($RecvExtension->getParam("sendid"));
        }


        $externalOrder = null;
        if (isset($externalOrderId[0])) {
            $externalOrder = $externalOrderPaymentRepo->findRecord($externalOrderId[0]);
        }

        //注文ID不正
        if (is_null($externalOrder)) {

            $RecvExtension->setErrorCode(301);
            $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(注文ID不正)");
            $mailBody =  "注文データが確認できませんでした。\n";
            $mailBody .= $companyName."にご確認下さい。\n";
            $mailBody .=  "sendid:".$RecvExtension->getOrderId()."\n";
            $mailBody .=  $addErrorMsg;
            $RecvExtension->setMailBody($mailBody);
            return false;
        }

        //クライアントIP不正
        if($externalOrder->getMemo07() != $RecvExtension->getParam("clientip")){

            $RecvExtension->setErrorCode(302);
            $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(クライアントIP不正)");
            $mailBody =  "不正な決済通知が送られました。\n";
            $mailBody .= "ECCUBEまたは".$companyName."に登録された、クライアントIP(clientip)が異なっています。\n";
            $mailBody .= "sendid:".$RecvExtension->getOrderId()."\n";
            $mailBody .= "clientip:".$RecvExtension->getParam("clientip")."\n";
            $mailBody .= $addErrorMsg;
            $RecvExtension->setMailBody($mailBody);
            return false;
        }

        // 登録済みの受注情報を取得
        $Order = $this->app['eccube.repository.order']->findOneBy(array('id' => $externalOrder->getId()));

        //決済金額不正チェック
        if($Order->getTotal() != $RecvExtension->getParam("money")){
            $RecvExtension->setErrorCode(303);
            $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(決済金額不正)");
            $mailBody =  "金額の異なる決済通知が送られてきました。\n";
            $mailBody .= $companyName."にご確認下さい。\n";
            $mailBody .= "sendid:".$RecvExtension->getOrderId()."\n";
            $mailBody .= "money:".$Order->getTotal()."/".$RecvExtension->getParam("money")."\n";
            $mailBody .= $addErrorMsg;
            $RecvExtension->setMailBody($mailBody);
            return false;
        }

        //完了済みチェック
        if($Order->getOrderStatus()->getId() == $this->app['config']['order_new']){
            $RecvExtension->setErrorCode(401);
            $RecvExtension->setErrorMsg("ERROR CODE:".$RecvExtension->getErrorCode()."(決済完了済み)");
            $mailBody =  "注文が決済完了済みになっています。\n";
            $mailBody .= $companyName."にご確認下さい。\n";
            $mailBody .= "sendid:".$RecvExtension->getOrderId()."\n";
            $mailBody .= $addErrorMsg;
            $RecvExtension->setMailBody($mailBody);
            return false;
        }

        // 各オーダー情報を設定
        $RecvExtension->setOrder($Order);
        $RecvExtension->setExternalOrderPayment($externalOrder);

        //エラーなし
        return true;
    }

    /**
     * IP帯域チェック
     * @param $ip, $cidr
     * @return bool
     */
    function checkIpNet($ip, $cidr) {

        $network = array();
        $network = explode('/', $cidr);
        if (isset($network[1])) {
            $host = 32 - $network[1];

            $net = ip2long($network[0]) >> $host << $host;
            $ip_net = ip2long($ip) >> $host << $host;
        } else {
            $net = $cidr;
            $ip_net = $ip;
        }

        return ($net === $ip_net) ? true : false;
    }


    /**
     * Send Error mail when call recv result
     * @param object $RecvExtension
     * @return type
     */
    function sendErrorMail(\Plugin\ExternalPaymentGateway\Controller\DataObj\RecvExtension $RecvExtension)
    {

        $mdlExternalcreditName = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_MODULE_NAME'];

        // subjectを設定
        $subject = $mdlExternalcreditName." 決済エラー ".$RecvExtension->getErrorMsg();

        // インスタンス作成
        $transport = $this->app['swiftmailer.transport'];

        $mailer = \Swift_Mailer::newInstance($transport);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($this->BaseInfo->getEmail03()))
            ->setTo(array($RecvExtension->getMailTo()))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($RecvExtension->getMailBody());

        // メール送信
        $mailer->send($message);

    }

    /**
     * Send order mail.
     *
     * @param $Order 受注情報
     */
    public function sendOrderMail(\Eccube\Entity\Order $Order)
    {

        $MailTemplate = $this->app['eccube.repository.mail_template']->find(1);

        $body = $this->app->renderView($MailTemplate->getFileName(), array(
            'header' => $MailTemplate->getHeader(),
            'footer' => $MailTemplate->getFooter(),
            'Order' => $Order,
        ));

        // インスタンス作成
        $transport = $this->app['swiftmailer.transport'];
        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] ' . $MailTemplate->getSubject())
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($Order->getEmail()))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        // メール送信
        $mailer->send($message);
    }

    /**
     * update plg_external_order_payment
     * @param object $externalOrder
     * @return type
     */
    function doRecvExternalCredit($externalOrder)
    {
        // 決済完了はmemo01にクライアントIPを保存する
        $externalOrder->setMemo01($externalOrder->getMemo07());
        $this->app['orm.em']->persist($externalOrder);
        $this->app['orm.em']->flush();
        return true;
    }

}
