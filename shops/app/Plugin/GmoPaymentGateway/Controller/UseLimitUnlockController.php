<?php
/*
 * Copyright(c) 2017 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\GmoPaymentGateway\Controller;

use Eccube\Application;
use Eccube\Common\Constant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Plugin\GmoPaymentGateway\Controller\Util\PluginUtil;
use Plugin\GmoPaymentGateway\Controller\Util\PaymentUtil;
use Plugin\GmoPaymentGateway\Controller\Util\AccountLockUtil;

class UseLimitUnlockController
{
    public function index(Application $app, Request $request)
    {
        $config = $app['config'];
        $searchForm = $app['form.factory']
            ->createBuilder()
            ->add('search_ipaddress', 'text', array(
                'label' => '検索IPアドレス',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array('max' => $config['stext_len'])),
                ),
            ))
            ->add('ipaddress', 'hidden')
            ->add('mode', 'hidden')
            ->getForm();

        $lineCount = -1;
        $accounts = array();

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                switch ($searchData['mode']) {
                case 'unlock':
                    $this->unlockAccount($app, $searchData['ipaddress']);
                case 'search':
                    list($lineCount, $accounts) = $this->accountDisp
                        ($app, $searchData['search_ipaddress']);
                    break;

                default:
                    break;
                }
            }
        }

        return $app->render
            ('GmoPaymentGateway/View/admin/gmo_use_limit_unlock.twig',
             array('searchForm' => $searchForm->createView(),
                   'lineCount' => $lineCount,
                   'accounts' => $accounts
            ));
    }

    /**
     * アカウントを取得する
     */
    function accountDisp($app, $ipAddr) {
        $objMdl =& PluginUtil::getInstance($app);
        $objUtil = new PaymentUtil($app);

        $gmoSetting = $objMdl->getUserSettings();

        $const = $app['config']['GmoPaymentGateway']['const'];
        $internalCode = $const['PG_MULPAY_PAYID_TOKEN'];
        if ($gmoSetting['credit_token'] == 0) {
            $internalCode = $const['PG_MULPAY_PAYID_CREDIT'];
        }

        $payInfo = $objUtil->getPaymentInfoFromInternalCode($internalCode);
        $paymentInfo = $payInfo->getArrPaymentConfig();

        if (empty($paymentInfo)) {
            return array(0, array());
        }

        $limitMin   = $paymentInfo['limit_min'];
        $limitCount = $paymentInfo['limit_count'];
        $lockMin    = $paymentInfo['lock_min'];

        $objAL =& AccountLockUtil::getInstance
            ($app, $limitMin, $limitCount, $lockMin);

        $accounts = $objAL->getLockList($ipAddr);

        if (count($accounts) == 0) {
            return array(0, array());
        }

        $sort = array();
        foreach ($accounts as $key => &$account) {
            $sort[$key] = $account['date_time'];

            $dt = \DateTime::createFromFormat('YmdHis', $account['date_time']);
            $account['date_time'] = $dt->format('Y/m/d H:i:s');

            $dt->add(new \DateInterval('PT' . $lockMin . 'M'));
            $account['unlock_date_time'] = $dt->format('Y/m/d H:i:s');

            $isLock = $objAL->isLock($account['ipaddress']);
            $account['lock_status'] = $isLock ? "ロック中" : "";
            $account['is_lock'] = $isLock;
        }

        // エラー検出日時の降順でソート
        array_multisort($sort, SORT_DESC, $accounts);

        return array(count($accounts), $accounts);
    }

    /**
     * ロック中のアカウントをロック解除します
     */
    function unlockAccount($app, $ipAddr) {
        $objMdl =& PluginUtil::getInstance($app);
        $objUtil = new PaymentUtil($app);

        $gmoSetting = $objMdl->getUserSettings();

        $const = $app['config']['GmoPaymentGateway']['const'];
        $internalCode = $const['PG_MULPAY_PAYID_TOKEN'];
        if ($gmoSetting['credit_token'] == 0) {
            $internalCode = $const['PG_MULPAY_PAYID_CREDIT'];
        }

        $payInfo = $objUtil->getPaymentInfoFromInternalCode($internalCode);
        $paymentInfo = $payInfo->getArrPaymentConfig();

        if (empty($ipAddr) || empty($paymentInfo)) {
            return;
        }

        $limitMin   = $paymentInfo['limit_min'];
        $limitCount = $paymentInfo['limit_count'];
        $lockMin    = $paymentInfo['lock_min'];

        $objAL =& AccountLockUtil::getInstance
            ($app, $limitMin, $limitCount, $lockMin);

        $objAL->unLock($ipAddr);
    }
}
