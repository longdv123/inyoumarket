<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Event\RenderEvent;
use Eccube\Event\ShoppingEvent;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Entity\Shipping;
use Plugin\ExternalPaymentGateway\Controller\Util\CommonUtil;
use Plugin\ExternalPaymentGateway\Controller\Util\PaymentUtil;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Plugin\ExternalPaymentGateway\Controller\Util\PluginUtil;


class ExternalPaymentGateway
{

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    // 購入処理 Before
    // テレコムの決済画面にリダイレクトするので購入処理(ShoppingController)のconfirmを変更
    // メール送信部分、完了画面表示を削除
    // オーダーのステータスを決済処理中に更新する処理を追加
    public function onControllerShoppingConfirmBefore()
    {

        $cartService = $this->app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $this->app->redirect($this->app->url('cart'));
        }

        // オーダー取得
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $this->app['eccube.service.cart']->getPreOrderId()));
        if (!$Order) {
            $this->app->addError('front.shopping.order.error');
            return $this->app->redirect($this->app->url('shopping_error'));
        }

        // POSTのみ許可
        if ('POST' !== $this->app['request']->getMethod()) {
            return $this->app->redirect($this->app->url('cart'));
        }

        // form作成
        $form = $this->app['eccube.service.shopping']->getShippingForm($Order);

        $form->handleRequest($this->app['request']);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // POSTパラメータを取得
            $requestData = $this->app['request']->request->all();
            
            // 支払い方法ID取得
            $chkId = $requestData['shopping']['payment'];

            $ExternalPayment = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')->getExternalPayment('id', $chkId, false, $this->app);
            // プラグインの支払い方法ではない場合はreturn
            if (!$ExternalPayment) {
                return;
            }

            // トランザクション制御
            $em = $this->app['orm.em'];
            $em->getConnection()->beginTransaction();
            try {
                // 商品公開ステータスチェック、商品制限数チェック、在庫チェック
                $check = $this->app['eccube.service.shopping']->isOrderProduct($em, $Order);
                if (!$check) {
                    $em->getConnection()->rollback();

                    $this->app->addError('front.shopping.stock.error');
                    return $this->app->redirect($this->app->url('shopping_error'));
                }

                // 受注情報、配送情報を更新
                $this->app['eccube.service.shopping']->setOrderUpdate($Order, $data);
                // 在庫情報を更新
                $this->app['eccube.service.shopping']->setStockUpdate($em, $Order);

                if ($this->app->isGranted('ROLE_USER')) {
                    // 会員の場合、購入金額を更新
                    $this->app['eccube.service.shopping']->setCustomerUpdate($Order, $this->app->user());
                }

                 // 受注情報、配送情報を更新（決済処理中として更新する）
                $this->app['eccube.service.order']->setOrderUpdate($this->app['orm.em'], $Order, $data);
                $Order->setOrderStatus($this->app['eccube.repository.order_status']->find($this->app['config']['order_pending']));

                $em->flush();
                $em->getConnection()->commit();

            } catch (\Exception $e) {
                $em->getConnection()->rollback();

                $this->app->log($e);

                $this->app->addError('front.shopping.system.error');
                return $this->app->redirect($this->app->url('shopping_error'));
            }

            $em->close();
            // テレコムクレジット決済画面にリダイレクト
            header("Location: " . $this->app->url('external_shopping_payment'));
            exit;

        } else {
            return;
        }
        return $this->app->redirect($this->app->url('cart'));
    }


    //ご注文内容のご確認 after
    public function onControllerShoppingAfter()
    {

        // 登録済みの受注情報を取得
        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $this->app['eccube.service.cart']->getPreOrderId()));

        // オーダーが存在したら
        if (!is_null($Order)) {
            // テレコムクレジットの決済を取得
            $speedCode = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE'];
            $speedPaymentMethod = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')->getPaymentByType($speedCode, true, $this->app);
            if (!empty($speedPaymentMethod)){

                $Payment = $Order->getPayment();

                // スピード決済ができるか？
                $externalOrderPaymentRepo = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment');
                $externalOrderPaymentRepo->setConfig($this->app['config']['ExternalPaymentGateway']['const']);

                // 非会員のためにスピード決済は不可に初期設定
                $Customer = $Order->getCustomer();
                $ret = 0;
                if (!is_null($Customer)) {
                    $ret = $externalOrderPaymentRepo->getOrderHistoryCount($Order->getCustomer()->getId());
                }

                if(!$ret){

                    // オーダーがスピード決済に選択されていたら、
                    if ($Payment->getId() == $speedPaymentMethod->getId()){

                        // テレコムクレジットの決済を取得
                        $sslCode = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE'];
                        $sslPaymentMethod = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')->getPaymentByType($sslCode, true, $this->app);

                        // 都度に変更
                        $chgPayment = $this->app['eccube.repository.payment']
                            ->findOrCreate($sslPaymentMethod->getId());
                        $Order->setPayment($chgPayment);
                        $Order->setPaymentMethod($chgPayment->getMethod());
                        $this->app['orm.em']->flush();
                    }
                }
            }
        }
    }


    public function onRenderShoppingBefore(FilterResponseEvent $event)
    {

        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $this->app['eccube.service.cart']->getPreOrderId()));
        if (!is_null($Order)) {

            $Payment = $Order->getPayment();
            $PaymentConfig = null;
            $plist_elements = null;

            if (!is_null($Payment)) {
                $PaymentConfig = $this->app['eccube.plugin.external_pg.repository.external_payment_method']->find($Payment->getId());
            }
            if (!is_null($PaymentConfig)) {
                // 現在のソースを取得
                $source = $event->getResponse()->getContent();

                // DOMDocumentのエラー制御
                libxml_use_internal_errors(true);

                // DOMDocumentを用いてソースを取得する
                $dom = new \DOMDocument();
                libxml_use_internal_errors( true );
                $dom->loadHTML('<?xml encoding="UTF-8">' . $source);
                libxml_clear_errors();
                $dom->encoding = "UTF-8";

                // ソースを結合したい部分を取得
                $domElements = $dom->getElementsByTagName("*");
                for ($i = 0; $i < $domElements->length; $i++) {

                    // お支払い方法を取得
                    if (@$domElements->item($i)->attributes->getNamedItem('class')->nodeValue == "payment_list") {
                        $plist_elements[] = $domElements->item($i);
                    }

                    // 決済ボタンの取得
                    if (@$domElements->item($i)->attributes->getNamedItem('id')->nodeValue == "order-button") {
                        $elements[] = $domElements->item($i);
                    }
                }

                // ホームページリンク表示フラグ
                if ($this->app['config']['ExternalPaymentGateway']['const']['TELECOM_CREDIT_HOMEPAGE_LINK_FLG'] == 1) {
                    if (!empty($plist_elements)) {
                        $plist_template = $dom->createDocumentFragment();
                        $plist_template->appendXML($this->app['twig']->render('ExternalPaymentGateway/View/telecom_link.twig',
                            array('telecom_homepage' => $this->app['config']['ExternalPaymentGateway']['const']['TELECOM_CREDIT_HOMEPAGE'])
                        ));

                        $plist_elements[0]->insertBefore($plist_template);
                        $source = $event->getResponse()->setContent($dom->saveHTML());

                    }
                }

                if (!empty($elements)) {

                    // 購入ボタンを変更
                    $objUtil = new PaymentUtil($this->app);
                    $PaymentExtension = $objUtil->getPaymentTypeConfig($Order->getPayment()->getId());
                    $template = $dom->createDocumentFragment();

                    $template->appendXML($this->app['twig']->render('ExternalPaymentGateway/View/next_cart_button.twig',
                        array('button_comment' => 'クレジット決済へ')
                    ));
                    $elements[0]->parentNode->replaceChild($template, $elements[0]);
                    $source = $event->getResponse()->setContent($dom->saveHTML());

                }
            }

            // スピード決済ができるか？
            $externalOrderPaymentRepo = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment');
            $externalOrderPaymentRepo->setConfig($this->app['config']['ExternalPaymentGateway']['const']);

            // 非会員のためにスピード決済は不可に初期設定
            $Customer = $Order->getCustomer();
            $ret = 0;
            if (!is_null($Customer)) {
                $ret = $externalOrderPaymentRepo->getOrderHistoryCount($Order->getCustomer()->getId());
            }

            if(!$ret){

                // テレコムクレジットのスピード決済を取得
                // スピード決済ができない場合はjavascriptでラジオボタンを削除
                // また、スピード決済のランクが高になっていた場合はデフォルトでチェックされてしまうので都度決済にチェックする処理を追加
                $speedCode = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE'];
                $sslCode = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE'];
                $speedPaymentMethod = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')->getPaymentByType($speedCode, true, $this->app);
                $sslPaymentMethod = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')->getPaymentByType($sslCode, true, $this->app);
                if (!empty($speedPaymentMethod) && !empty($sslPaymentMethod)){

                    $speedId = $speedPaymentMethod->getId();
                    $sslId = $sslPaymentMethod->getId();
                    $strPaymentSpeedLi = '';
                    $strPaymentSpeed = '';
                    $strPaymentSsl = '';
                    // 都度決済チェックボックス状態を取得
                    $strPaymentSsl .= '$("#shopping_payment_'.$sslId.'");
                            ';
                    // スピード決済チェックボックス状態を取得
                    $strPaymentSpeed .= '$("#shopping_payment_'.$speedId.'");
                            ';
                    // スピード決済の一番近い親liを取得
                    $strPaymentSpeedLi .= '$("#shopping_payment_'.$speedId.'").closest("li");
                            ';
                    $source = $event->getResponse()->getContent();
                    // jquery作成 スピード決済を削除する
                    $jqueryCode = '
                    <script>
                        $(function(){
                            var pSpeed = '.$strPaymentSpeed.'
                            var pSsl   = '.$strPaymentSsl.'
                            if (pSpeed.prop("checked") == true) {
                                pSsl.prop("checked", true);
                            }
                            var pnode = '.$strPaymentSpeedLi.'
                            pnode.remove();
                        });
                    </script>
                    ';
                    $source .= $jqueryCode;
                    $event->getResponse()->setContent($source);
                }
            }
        }
    }

    // 支払方法登録・編集
    public function onRenderAdminSettingShopPaymentEditBefore(FilterResponseEvent $event)
    {

        // uriからpaymentのidを取得する
        $url = $_SERVER["REQUEST_URI"];
        preg_match('/payment\/([0-9]+)/',$url,$match);
        $id = $match[1];

        // プラグインのペイメントメソッドを取得
        $PaymentMethod = $this->app['eccube.plugin.external_pg.repository.external_payment_method']
            ->findOrCreate($id);

        // プラグインの支払方法の場合
        if (!empty($PaymentMethod)){

            $source = $event->getResponse()->getContent();

            // DOMDocumentを用いてソースを取得する
            $dom = new \DOMDocument();
            libxml_use_internal_errors( true );
            $dom->loadHTML('<?xml encoding="UTF-8">' . $source);
            libxml_clear_errors();
            $dom->encoding = "UTF-8";

            // ソースを結合したい部分を取得
            $domElements = $dom->getElementsByTagName("*");
            for ($i = 0; $i < $domElements->length; $i++) {

                // 登録ボタンの取得
                if (@$domElements->item($i)->attributes->getNamedItem('class')->nodeValue == "btn btn-primary btn-block btn-lg") {
                    $elements[] = $domElements->item($i);
                }
            }

            if (!empty($elements)) {
                $template = $dom->createDocumentFragment();

                $template->appendXML($this->app['twig']->render('ExternalPaymentGateway/View/admin/external_payment_edit_button.twig'));
                $elements[0]->parentNode->replaceChild($template, $elements[0]);
                $source = $event->getResponse()->setContent($dom->saveHTML());

                //コンフィグより支払方法の利用条件(下限)の値を取得する
                $ruleMinLimit = $this->app['config']['ExternalPaymentGateway']['const']['CREDIT_RULE_MIN_LIMIT'];

                // jquery作成 利用条件の下限のチェックを行う
                $jqueryCode = '
                <script>
                function cnfmAndSubmit(){
                    var ruleMin = $("#payment_register_rule_min").val();
                    if(ruleMin.match(/[0-9]+/)){
                        if (ruleMin < '.$ruleMinLimit.') {
                            alert("利用条件(下限)は'.$ruleMinLimit.'円以上にしてください。");
                            return false;
                        }
                    }
                }
                </script>
                ';
                $source .= $jqueryCode;
                $event->getResponse()->setContent($source);
            }
        }
    }


}
