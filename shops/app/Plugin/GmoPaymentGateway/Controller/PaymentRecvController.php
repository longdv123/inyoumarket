<?php
/*
 * Copyright(c) 2015 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\GmoPaymentGateway\Controller;

use Eccube\Application;
use Plugin\GmoPaymentGateway\Controller\Util\PaymentUtil;
use Plugin\GmoPaymentGateway\Controller\Util\PluginUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eccube\Common\Constant;

/**
 * Controller to handle result reception
 */
class PaymentRecvController
{

    public $app;

    /**
     * Process for PaymentRecv, send email for admin in case have error.
     * @param Application $app
     * @param Request $request
     * @param type $id
     * @return exit
     */
    public function index(Application $app, Request $request, $id = null)
    {
        $this->app = $app;
        $responseCode = 0;

        $objMdl = &PluginUtil::getInstance($this->app);
        $objMdl->printLog('******* Data Receiver start *******');
        $objMdl->printLog(print_r($_REQUEST, true));
        $objMdl->printLog('******* Data Receiver end *******');
        $subData = $objMdl->getUserSettings();
        if ($request->getMethod() === 'POST') {
            $requestData = $request->request->all();
            // Does ShopID match between PaymentRecv and config of user ?
            if ($subData['ShopID'] === $requestData['ShopID']){
                $orderId = $this->getOrderId($requestData['OrderID']);
                $objUtil = new PaymentUtil($app);
                
                $objMdl->printLog('Receive OrderID = '.$orderId);

                // PaymentRecv not sent OrderId in request, send mail for admin
                if (empty($orderId)) {
                    $objMdl->printLog('!!!!!!! Request does not contain an OrderID. !!!!!!!');
                    $this->doNoOrder($requestData);
                    $responseCode = $this->sendResponse(false);

                    return new Response($responseCode,
                                        Response::HTTP_OK,
                                        array('content-type' => 'text/html'));
                }
                
                // Can not find order from OrderID in PaymentRecv, sent mail for admin
                $OrderExtension = $objUtil->getOrderPayData($orderId);
                if (false === $OrderExtension){
                    $objMdl->printLog('!!!!!!! Request contain an OrderID = '.$orderId.' that does not exist in Eccube. !!!!!!!');
                    $this->doNoOrder($requestData, $OrderExtension);
                    $responseCode = $this->sendResponse(false);

                    return new Response($responseCode,
                                        Response::HTTP_OK,
                                        array('content-type' => 'text/html'));
                }

                if (!isset($app['config']['GmoPaymentGateway']['const']['GMO_RECEIVE_WAIT_TIME'])) {
                    $sleep = 2;
                } else {
                    $sleep = $app['config']['GmoPaymentGateway']['const']['GMO_RECEIVE_WAIT_TIME'];
                }
                $status = $requestData['Status'];
                if (strcmp($status, 'AUTH') == 0 || strcmp($status, 'CHECK') == 0 || strcmp($status, 'CAPTURE') == 0) {
                    sleep($sleep);
                }

                $i = 0;
                $paymentData = array();
                do {
                    if ($i > 0) {
                        sleep(1);
                    }

                    $this->app['orm.em']->getConnection()->close();
                    $objUtil = new PaymentUtil($this->app);
                    $OrderExtension = $objUtil->getOrderPayData($orderId);
                    if ($OrderExtension !== false) {
                        $paymentData = $OrderExtension->getPaymentData();
                    }
                } while (++$i < 10 && !isset($paymentData['AccessID']));

                $res = $this->doReceive($requestData, $OrderExtension);
                $responseCode = $this->sendResponse($res);
            }else{
                $objMdl->printLog('!!!!!!! Request contain a ShopID = '.$requestData['ShopID'].'that does not match in Eccube. !!!!!!!');
                $this->doNoOrder($requestData);
                $responseCode = $this->sendResponse(false);
            }
        }

        return new Response(
            $responseCode,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );
    }

    /**
     * Process after check error of PaymentRecv, base on PayType of PaymentRecv.
     * @param $fromData
     * @param $OrderExtension
     */
    function doReceive(&$formData, $OrderExtension)
    {
        $objUtil = new PaymentUtil($this->app);
        $objMdl = &PluginUtil::getInstance($this->app);
        $GmoOrderPayment = $OrderExtension->getGmoOrderPayment();
        $memo05 = $GmoOrderPayment->getMemo05();
        $Order = $OrderExtension->getOrder();
        $paymentData = $OrderExtension->getPaymentData();
        $res = true;

        if (!isset($paymentData['AccessID'])) {
            return false;
        }

        if ($formData['AccessID'] != $paymentData['AccessID']) {
            $objMdl->printLog('!!!!!!! Request contain an AccessID = '.$formData['AccessID'].' that does not match with AccessID of order in Eccube. !!!!!!!');
            $this->doUnMatchAccessID($formData, $OrderExtension);

            // ログのみ
            $objUtil->setOrderPayData($GmoOrderPayment, $formData, true);

            return false;
        }

        // dtb_gmo_order_payment からではなく dtb_gmo_payment_method から
        // 取得すべき。GMO-PG決済以外に変更した場合、gmo_order_payment は
        // 変更前の状態のまま残っているため支払方法不一致を検知できない。
        // $memo03 = $GmoOrderPayment->getMemo03();
        $memo03 = "";
        $Payment = $Order->getPayment();
        if (!is_null($Payment)) {
            $repo = 'eccube.plugin.gmo_pg.repository.gmo_payment_method';
            $GmoPaymentMethod = $this->app[$repo]->find($Payment->getId());
            if (!is_null($GmoPaymentMethod)) {
                $memo03 = $GmoPaymentMethod->getMemo03();
            }
        }

        switch ($formData['PayType']) {
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_CVS']:
                if ($memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_CVS']) {
                    $objMdl->printLog('!!!!!!! Request contain an PayType = '.$formData['PayType'].' that does not match with PayType of order in Eccube. !!!!!!!');
                    $this->doUnMatchPayType($formData, $OrderExtension);
                    $res = false;
                } else {
                    $res = $this->doRecvCvs($formData, $Order);
                }
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_PAYEASY']:
                if ($memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_PAYEASY'] && $memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_ATM']) {
                    $objMdl->printLog('!!!!!!! Request contain an PayType = '.$formData['PayType'].' that does not match with PayType of order in Eccube. !!!!!!!');
                    $this->doUnMatchPayType($formData, $OrderExtension);
                    $res = false;
                } else {
                    $res = $this->doRecvPayEasy($formData, $Order);
                }
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_RAKUTEN_ID']:
                if ($memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_RAKUTEN_ID']) {
                    $objMdl->printLog('!!!!!!! Request contain an PayType = '.$formData['PayType'].' that does not match with PayType of order in Eccube. !!!!!!!');
                    $this->doUnMatchPayType($formData, $OrderExtension);
                    $res = false;
                } else {
                    $res = $this->doRecvRakutenId($formData, $OrderExtension);
                }
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_AU']:
                if ($memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_AU']) {
                    $objMdl->printLog('!!!!!!! Request contain an PayType = '.$formData['PayType'].' that does not match with PayType of order in Eccube. !!!!!!!');
                    $this->doUnMatchPayType($formData, $OrderExtension);
                    $res = false;
                } else {
                    $res = $this->doRecvAu($formData, $OrderExtension);
                }
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_DOCOMO']:
                if ($memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_DOCOMO']) {
                    $objMdl->printLog('!!!!!!! Request contain an PayType = '.$formData['PayType'].' that does not match with PayType of order in Eccube. !!!!!!!');
                    $this->doUnMatchPayType($formData, $OrderExtension);
                    $res = false;
                } else {
                    $res = $this->doRecvDocomo($formData, $OrderExtension);
                }
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_SB']:
                if ($memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_SB']) {
                    $objMdl->printLog('!!!!!!! Request contain an PayType = '.$formData['PayType'].' that does not match with PayType of order in Eccube. !!!!!!!');
                    $this->doUnMatchPayType($formData, $OrderExtension);
                    $res = false;
                } else {
                    $res = $this->doRecvSb($formData, $OrderExtension);
                }
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_CREDIT']:
            default:
                if ($memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_CREDIT'] 
                    && $memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_REGIST_CREDIT'] 
                    && $memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_CREDIT_CHECK'] 
                    && $memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_CREDIT_SAUTH']
                    && $memo03 != $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYID_TOKEN']
                ) {
                    $objMdl->printLog('!!!!!!! Request contain an PayType = '.$formData['PayType'].' that does not match with PayType of order in Eccube. !!!!!!!');
                    $this->doUnMatchPayType($formData, $OrderExtension);
                    $res = false;
                } else {                    
                    $res = $this->doRecvCredit($formData, $OrderExtension);
                }
                break;
        }

        if ($res) {
            unset($formData['ShopPass']);
            unset($formData['AccessPass']);
            $objUtil->setOrderPayData($GmoOrderPayment, $formData);
        } else {
            // ログのみ
            $objUtil->setOrderPayData($GmoOrderPayment, $formData, true);
        }

        return $res;
    }

    /**
     * Send mail in case not found orderId in PaymentRecv or DB or PaymentRecv have error.
     * @param $formData
     */
    function doNoOrder($formData)
    {   
        $objMdl = &PluginUtil::getInstance($this->app);

        $tplpath = 'GmoPaymentGateway/View/mail_template/recv_no_order.twig';
        $subject = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_MODULE_NAME'] . ' 不一致データ検出';

        $this->sendMail($tplpath, $subject, $formData);
    }

    /**
     * Send mail in case AccessID in PaymentRecv and Order is not match.
     * @param $formData
     * @param $OrderExtension
     */
    function doUnMatchAccessID(&$formData, $OrderExtension)
    {
        $tplpath = 'GmoPaymentGateway/View/mail_template/recv_unmatch_accessid.twig';
        $subject = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_MODULE_NAME'] . ' 取引ID不一致データ検出';
        $this->sendMail($tplpath, $subject, $formData, $OrderExtension);
    }

    /**
     * Send mail in case PayType in PaymentRecv and Order is not match.
     * @param $formData
     * @param $OrderExtension
     */
    function doUnMatchPayType(&$formData, $OrderExtension)
    {
        $tplpath = 'GmoPaymentGateway/View/mail_template/recv_unmatch_paytype.twig';
        $subject = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_MODULE_NAME'] . ' 支払い方法不一致データ検出';
        $this->sendMail($tplpath, $subject, $formData, $OrderExtension);
    }

    /**
     * Send mail when call recv result
     * @param string $templatePath
     * @param string $subject
     * @param array $formData
     * @param object $OrderExtension
     * @return type
     */
    function sendMail($templatePath, $subject, $formData, \Plugin\GmoPaymentGateway\Controller\DataObj\OrderExtension $OrderExtension = null)
    {
        $objMdl = &PluginUtil::getInstance($this->app);
        $objMdl->printLog('param_error:' . $subject . ' Param:' . print_r($formData, true));
        
        if (!Util\CommonUtil::isBlank($formData['ErrCode'])
            and !Util\CommonUtil::isBlank($formData['ErrInfo'])
        ) {
            return;
        }
        if ($formData['Status'] == 'PAYFAIL') {
            return;
        }
        
        switch ($formData['PayType']) {

            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_CVS']:
                $formData['pay_type'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYNAME_CVS'];
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_PAYEASY']:
                $formData['pay_type'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYNAME_PAYEASY'];
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_CREDIT']:
                $formData['pay_type'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYNAME_CREDIT'];
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_RAKUTEN_ID']:
                $formData['pay_type'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYNAME_RAKUTEN_ID'];
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_AU']:
                $formData['pay_type'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYNAME_AU'];
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_DOCOMO']:
                $formData['pay_type'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYNAME_DOCOMO'];
                break;
            case $this->app['config']['GmoPaymentGateway']['const']['MULPAY_PAYTYPE_SB']:
                $formData['pay_type'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAYNAME_SB'];
                break;
            default:
                $formData['pay_type'] = '不明な決済(PayType)';
                break;
        }

        $formData['order_id'] = $this->getOrderId($formData['OrderID']);

        $BaseInfo = $this->app['eccube.repository.base_info']->get();

        $orderData = array();
        $Order = new \Eccube\Entity\Order();
        if (!empty($OrderExtension)) {
            $Order = $OrderExtension->getOrder();
            $orderData = $OrderExtension->getPaymentData();
        }
        $body = $this->app->renderView($templatePath, array(
            'data' => $formData,
            'orderData' => $orderData,
            'order' => $Order,
        ));
        if (!empty($Order)) {
            $to = $Order->getEmail();
        } else {
            $to = $BaseInfo;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($BaseInfo->getEmail03())
            ->setTo($BaseInfo->getEmail02())
            ->setBcc($BaseInfo->getEmail01())
            ->setReturnPath($BaseInfo->getEmail04())
            ->setBody($body);
        $this->app->mail($message);
    }

    /**
     * Get order_id base on OrderID has timestamp
     * @param integer $OrderID
     * @return integer $orderId
     */
    function getOrderId($OrderID)
    {
        if (!empty($OrderID)) {
            list($order_id, $dummy) = explode('-', $OrderID);
            if (Util\CommonUtil::isBlank($order_id) && !Util\CommonUtil::isInt($order_id)) {
                return;
            } else {
                return $order_id;
            }
        }
    }

    /**
     * Update Order for CVS payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvCvs(&$formData, $Order)
    {
        $this->doRecvDefault($formData, $Order);
        return true;
    }

    /**
     * Update Order for PayEasy payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvPayEasy(&$formData, $Order)
    {
        $this->doRecvDefault($formData, $Order);
        return true;
    }

    /**
     * Update Order for PayEasy,CVS payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvDefault(&$formData, $Order)
    { 
        $orderStatus = null;
        switch ($formData['Status']) {
            case 'UNPROCESSED':
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_UNSETTLED'];
                break;
            case 'REQSUCCESS':
                $orderStatus = $this->app['config']['order_pay_wait'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_REQUEST_SUCCESS'];
                break;
            case 'PAYSUCCESS':
                $orderStatus = $this->app['config']['order_pre_end'];
                $Order->setPaymentDate(new \DateTime());
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_PAY_SUCCESS'];
                break;
            case 'PAYFAIL':
                $orderStatus = $this->app['config']['order_cancel'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_FAIL'];
                break;
            case 'EXPIRED':
                $orderStatus = $this->app['config']['order_cancel'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_EXPIRE'];
                break;
            default:
                return false;
        }

        if (!empty($orderStatus)) {
            $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($orderStatus));
            $this->app['orm.em']->persist($Order);
            $this->app['orm.em']->flush();
        }
        return true;
    }

    /**
     * Update Order for Credit payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvCredit(&$formData, $OrderExtension)
    {
        $paymentData = $OrderExtension->getPaymentData();
        $Order = $OrderExtension->getOrder();
        $orderStatus = null;
        switch ($formData['Status']) {
            case 'UNPROCESSED':
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_UNSETTLED'];
                break;
            case 'AUTHENTICATED':
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_UNSETTLED'];
                break;
            case 'CHECK':
                $orderStatus = $this->app['config']['order_new'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CHECK'];
                break;
            case 'CAPTURE':
                $orderStatus = $this->app['config']['order_new'];
                $Order->setPaymentDate(new \DateTime());
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CAPTURE'];
                break;
            case 'AUTH':
            case 'SAUTH':
                $orderStatus = $this->app['config']['order_new'];
                $const = 'PG_MULPAY_PAY_STATUS_' . $formData['Status'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const'][$const];
                break;
            case 'SALES':
            case 'VOID':
            case 'RETURN':
            case 'RETURNX':
                $orderStatus = null;
                $const = 'PG_MULPAY_PAY_STATUS_' . $formData['Status'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const'][$const];
                break;
            default:
                return false;
        }

        if (Util\CommonUtil::isBlank($formData['ErrCode']) &&
            !is_null($formData['Amount']) && $formData['Amount'] != '0') {
            $paymentTotal = (int)trim($formData['Amount']) + (int)trim($formData['Tax']);
            $Order->setPaymentTotal($paymentTotal);
        }

        if (!empty($orderStatus)) {
            if(array_key_exists('no_update_status_flg', $paymentData)){
                if($paymentData['no_update_status_flg'] != 1) {
                    $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($orderStatus));
                    $this->app['orm.em']->persist($Order);
                    $this->app['orm.em']->flush();
                }
            }
            else{
                $formData['no_update_status_flg'] = '0';
            }
        }
        return true;
    }
    
    /**
     * Update Order for RakutenID payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvRakutenId(&$formData, $OrderExtension)
    {
        $Order = $OrderExtension->getOrder();
        $orderStatus = null;
        switch ($formData['Status']) {
            //未決済
            case 'UNPROCESSED':
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_UNSETTLED'];
                break;
            //要求成功
            case 'REQSUCCESS':
                // 2017.10.23
                // 要求成功では注文ステータスを変更しない様に修正
                // $orderStatus = $this->app['config']['order_pay_wait'];
                // 2018.01.24
                // 要求成功では取引状態も変更しない様に修正
                // $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_REQUEST_SUCCESS'];
                break;
            case 'PAYFAIL':
                $orderStatus = $this->app['config']['order_cancel'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_FAIL'];
                break;
            case 'EXPIRED':
            case 'CANCEL':
                $orderStatus = $this->app['config']['order_cancel'];
                $const = 'PG_MULPAY_PAY_STATUS_' . $formData['Status'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const'][$const];
                break;
            case 'AUTH':
            case 'CAPTURE':
                if (empty($formData['CompletionDate'])) {
                    $orderStatus = $this->app['config']['order_new'];
                    $Order->setOrderDate(new \DateTime());
                }
                $Order->setPaymentDate(new \DateTime());
                $const = 'PG_MULPAY_PAY_STATUS_' . $formData['Status'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const'][$const];
                break;
            case 'PAYSTART':
            case 'REQSALES':
            case 'REQCANCEL':
            case 'REQCHANGE':  
            case 'SALES':
                $orderStatus = null;
                $const = 'PG_MULPAY_PAY_STATUS_' . $formData['Status'];
                $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const'][$const];
                break;
            default:
                return false;
        }
       
        if (Util\CommonUtil::isBlank($formData['ErrCode']) &&
            isset($formData['Amount']) && $formData['Amount'] != '0') {
            $paymentTotal = (int)trim($formData['Amount']) + (int)trim($formData['Tax']);
            $Order->setPaymentTotal($paymentTotal);
        }
        if (!empty($orderStatus)) {
            $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($orderStatus));
        }
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();

        if (!empty($orderStatus) &&
            $orderStatus == $this->app['config']['order_new']) {
            $this->sendOrderMail($Order);
        }

        return true;
    }

    /**
     * Update Order for Au payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvAu(&$formData, $OrderExtension)
    {
        $Order = $OrderExtension->getOrder();
        $orderStatus = null;

        switch ($formData['Status']) {
            // 未決済
        case 'UNPROCESSED':
            if ($formData['JobCd'] == 'CANCEL') {
                $orderStatus = $this->app['config']['order_cancel'];
            }
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_UNSETTLED'];
            break;
            // 要求成功
        case 'REQSUCCESS':
            if ($formData['JobCd'] == 'CANCEL') {
                $orderStatus = $this->app['config']['order_cancel'];
            }
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_REQUEST_SUCCESS'];
            break;
        case 'SALES':
        case 'RETURN':
            $const = 'PG_MULPAY_PAY_STATUS_' . $formData['Status'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const'][$const];
            break;
        case 'CANCEL':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CANCEL'];
            break;
        case 'PAYSUCCESS':
            $Order->setOrderDate(new \DateTime());
            $Order->setPaymentDate(new \DateTime());
            $orderStatus = $this->app['config']['order_pre_end'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_PAY_SUCCESS'];
            break;
        case 'AUTH':
            $Order->setOrderDate(new \DateTime());
            $orderStatus = $this->app['config']['order_new'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_AUTH'];
            break;
        case 'CAPTURE':
            $Order->setOrderDate(new \DateTime());
            $Order->setPaymentDate(new \DateTime());
            $orderStatus = $this->app['config']['order_pre_end'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CAPTURE'];
            break;
        case 'PAYFAIL':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_FAIL'];
            break;
        case 'EXPIRED':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_EXPIRE'];
            break;
        default:
            return false;
        }

        if (Util\CommonUtil::isBlank($formData['ErrCode']) &&
            isset($formData['Amount']) && $formData['Amount'] != '0') {
            $paymentTotal = (int)trim($formData['Amount']) + (int)trim($formData['Tax']);
            $Order->setPaymentTotal($paymentTotal);
        }
        if (!empty($orderStatus)) {
            $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($orderStatus));
        }
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();

        if (!empty($orderStatus) &&
            ($orderStatus == $this->app['config']['order_pre_end'] ||
             $orderStatus == $this->app['config']['order_new'])) {
            $this->sendOrderMail($Order);
        }

        return true;
    }

    /**
     * Update Order for Docomo payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvDocomo(&$formData, $OrderExtension)
    {
        $Order = $OrderExtension->getOrder();
        $orderStatus = null;

        switch ($formData['Status']) {
            // 未決済
        case 'UNPROCESSED':
            if ($formData['JobCd'] == 'CANCEL') {
                $orderStatus = $this->app['config']['order_cancel'];
            } else if ($formData['JobCd'] == 'AUTH' ||
                       $formData['JobCd'] == 'CAPTURE') {
                $orderStatus = $this->app['config']['order_pay_wait'];
            }
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_UNSETTLED'];
            break;
            // 要求成功
        case 'REQSUCCESS':
            if ($formData['JobCd'] == 'CANCEL') {
                $orderStatus = $this->app['config']['order_cancel'];
            } else if ($formData['JobCd'] == 'AUTH' ||
                       $formData['JobCd'] == 'CAPTURE') {
                $orderStatus = $this->app['config']['order_pay_wait'];
            }
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_REQUEST_SUCCESS'];
            break;
        case 'AUTH':
            $Order->setOrderDate(new \DateTime());
            $orderStatus = $this->app['config']['order_new'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_AUTH'];
            break;
        case 'CAPTURE':
            $Order->setOrderDate(new \DateTime());
            $Order->setPaymentDate(new \DateTime());
            $orderStatus = $this->app['config']['order_pre_end'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CAPTURE'];
            break;
        case 'SALES':
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_SALES'];
            break;
        case 'CANCEL':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CANCEL'];
            break;
        case 'PAYFAIL':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_FAIL'];
            break;
        case 'EXPIRED':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_EXPIRE'];
            break;
        default:
            return false;
        }

        if (Util\CommonUtil::isBlank($formData['ErrCode']) &&
            isset($formData['Amount']) && $formData['Amount'] != '0') {
            $paymentTotal = (int)trim($formData['Amount']) + (int)trim($formData['Tax']);
            $Order->setPaymentTotal($paymentTotal);
        }
        if (!empty($orderStatus)) {
            $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($orderStatus));
        }
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();

        if (!empty($orderStatus) &&
            ($orderStatus == $this->app['config']['order_pre_end'] ||
             $orderStatus == $this->app['config']['order_new'])) {
            $this->sendOrderMail($Order);
        }

        return true;
    }

    /**
     * Update Order for Sb payment after receive PaymentRecv
     * @param integer $OrderID
     * @return integer $orderId
     */
    function doRecvSb(&$formData, $OrderExtension)
    {
        $Order = $OrderExtension->getOrder();
        $orderStatus = null;

        switch ($formData['Status']) {
            // 未決済
        case 'UNPROCESSED':
            if ($formData['JobCd'] == 'CANCEL') {
                $orderStatus = $this->app['config']['order_cancel'];
            } else if ($formData['JobCd'] == 'AUTH' ||
                       $formData['JobCd'] == 'CAPTURE' ||
                       $formData['JobCd'] == 'SALES') {
                $orderStatus = $this->app['config']['order_pending'];
            }
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_UNSETTLED'];
            break;
            // 要求成功
        case 'REQSUCCESS':
            if ($formData['JobCd'] == 'CANCEL') {
                $orderStatus = $this->app['config']['order_cancel'];
            } else if ($formData['JobCd'] == 'AUTH' ||
                       $formData['JobCd'] == 'CAPTURE' ||
                       $formData['JobCd'] == 'SALES') {
                $orderStatus = $this->app['config']['order_pending'];
            }
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_REQUEST_SUCCESS'];
            break;
        case 'AUTH':
            $Order->setOrderDate(new \DateTime());
            $orderStatus = $this->app['config']['order_new'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_AUTH'];
            break;
        case 'CAPTURE':
            $Order->setOrderDate(new \DateTime());
            $Order->setPaymentDate(new \DateTime());
            $orderStatus = $this->app['config']['order_new'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CAPTURE'];
            break;
        case 'SALES':
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_SALES'];
            break;
        case 'CANCEL':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_CANCEL'];
            break;
        case 'PAYFAIL':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_FAIL'];
            break;
        case 'EXPIRED':
            $orderStatus = $this->app['config']['order_cancel'];
            $formData['pay_status'] = $this->app['config']['GmoPaymentGateway']['const']['PG_MULPAY_PAY_STATUS_EXPIRE'];
            break;
        default:
            return false;
        }

        if (Util\CommonUtil::isBlank($formData['ErrCode']) &&
            isset($formData['Amount']) && $formData['Amount'] != '0') {
            $paymentTotal = (int)trim($formData['Amount']) + (int)trim($formData['Tax']);
            $Order->setPaymentTotal($paymentTotal);
        }
        if (!empty($orderStatus)) {
            $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($orderStatus));
        }
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush();

        if (!empty($orderStatus) &&
            $orderStatus == $this->app['config']['order_new']) {
            $this->sendOrderMail($Order);
        }

        return true;
    }

    /**
     * レスポンスを返す。
     *
     * @param boolean
     * @param mode
     * @return void
     */
    function sendResponse($result) {        
        $objMdl = &PluginUtil::getInstance($this->app);
        if($result) {
            $objMdl->printLog('0');
            return 0;
        } else {
            $objMdl->printLog('1');
            return 1;
        }
    }
    
    
    function log($msg){
        $objMdl = &PluginUtil::getInstance($this->app);
        $objMdl->printLog("\n");
        $objMdl->printLog($msg);
        
    }

    private function sendOrderMail($Order)
    {
        if (version_compare(Constant::VERSION, '3.0.10', '>=')) {
            $this->app['eccube.service.shopping']->sendOrderMail($Order);
        } else {
            $this->app['eccube.service.mail']->sendOrderMail($Order);
        }
    }
}
