<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Controller\DataObj;

/**
 * Extra object 
 *
 *
 */
class RecvExtension
{

    /**
     *
     * @var type Eccube\Entity\Order
     */
    private $Order;

    /**
     *
     * @var type array of payment data, extracted from plg_external_order_payment
     */
    private $arrPaymentData;

    /**
     * Temporary ID
     * @var type string
     */
    private $OrderID;
    private $arrResult;
    private $sendFlg;
    private $param;
    private $mailTo;
    private $remoteIp;
    private $errorCode;
    private $errorMsg;
    private $mailBody;

    /**
     * contain all the memos, 01 -> 10
     * @var Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment
     */
    private $externalOrderPayment;

    public function getOrder()
    {
        return $this->Order;
    }

    public function setOrder(\Eccube\Entity\Order $order)
    {
        $this->Order = $order;
    }

    public function getPaymentData()
    {
        return $this->arrPaymentData;
    }

    public function setPaymentData($arrPaymentData)
    {
        $this->arrPaymentData = $arrPaymentData;
    }

    public function getOrderID()
    {
        return $this->OrderID;
    }

    public function setOrderID($OrderID)
    {
        $this->OrderID = $OrderID;
    }

    public function getResult()
    {
        return $this->arrResult;
    }

    public function setResult($arrResult)
    {
        $this->arrResult = $arrResult;
    }

    public function getExternalOrderPayment()
    {
        return $this->externalOrderPayment;
    }

    public function setExternalOrderPayment($orderPayment)
    {
        $this->externalOrderPayment = $orderPayment;
    }

    public function setMailTo($mailTo)
    {
        $this->mailTo = $mailTo;
    }

    public function getMailTo()
    {
        return $this->mailTo;
    }

    public function setSendFlg($flg)
    {
        $this->sendFlg = $flg;
    }

    public function getSendFlg()
    {
        return $this->sendFlg;
    }

    public function setParam($paramKey)
    {
        $this->param[$paramKey] = (isset($_GET[$paramKey])) ? $_GET[$paramKey] : "" ;
    }

    public function getParam($paramKey)
    {
        return $this->param[$paramKey];
    }

    public function setRemoteIp()
    {
        $this->remoteIp = (isset($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : "" ;
    }

    public function getRemoteIp()
    {
        return $this->remoteIp;
    }

    public function setErrorCode($code)
    {
        $this->errorCode = $code;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function setErrorMsg($msg)
    {
        $this->errorMsg = $msg;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    public function setMailBody($str)
    {
        $this->mailBody = $str;
    }

    public function getMailBody()
    {
        return $this->mailBody;
    }

}
