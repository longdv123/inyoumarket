<?php
/*
 * Copyright(c) 2016 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\GmoPaymentGateway\Controller\Helper;

use Plugin\GmoPaymentGateway\Service\client\PG_MULPAY_Client_Sb;
use Plugin\GmoPaymentGateway\Controller\Util\PaymentUtil;

/**
 * 決済モジュール 決済画面ヘルパー：sb決済
 */
class PageHelper_Sb
{
    /**
     * array of errors
     * @var array
     */
    private $errors;
    public $isComplete = false;
    public $orderId;
    private $results;

    public function __construct()
    {
        $this->errors = array();
    }

    /**
     * 画面モード毎のアクションを行う
     *
     * @param text $mode Mode値
     * @param FormParam $objFormParam FormParam インスタンス
     * @param array $arrOrder 受注情報
     * @param Page $objPage 呼出元ページオブジェクト
     * @return void
     */
    function modeAction
        ($mode, $listParam,
         \Eccube\Entity\Order $order,
         \Plugin\GmoPaymentGateway\Controller\DataObj\PaymentExtension $paymentExtension,
         \Eccube\Application $app)
    {
        $this->app = $app;
        $objUtil = new PaymentUtil($app);
        $orderExtension = $objUtil->getOrderPayData($order->getId());
        $objClient = new PG_MULPAY_Client_Sb($app);
        $result = false;

        switch ($mode) {
        case 'next':
            // 決済実行
            $ret = $objClient->doPaymentRequest($orderExtension, $listParam, $paymentExtension);
            if ($ret) {
                $results = $objClient->getResults();
                $this->setResults($results);
                $result = true;
                $this->isComplete = true;
            } else {
                $errors = $objClient->getError();
                $this->setErrors($errors);
                $result = false;
            }
            break;

        default:
            $result = false;
            break;
        }

        return $result;
    }

    /**
     * get array of results
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * set results
     * @param  results
     */
    public function setResults($results)
    {
        return $this->results = $results;
    }

    /**
     * get array of errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * set errors
     * @param  $errors
     */
    public function setErrors($errors)
    {
        return $this->errors = $errors;
    }
}
?>
