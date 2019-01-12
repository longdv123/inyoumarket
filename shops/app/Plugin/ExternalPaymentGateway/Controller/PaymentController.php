<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Controller;

use Eccube\Application;
use Plugin\ExternalPaymentGateway\Controller\Util\PaymentUtil;
use Plugin\ExternalPaymentGateway\Controller\Util\PluginUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentController
{
    public $app;
    public function index(\Eccube\Application $app)
    {
        $this->app = $app;
        $preOrderId = $this->app['eccube.service.cart']->getPreOrderId();

        $Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id' => $app['eccube.service.cart']->getPreOrderId()));
        if (is_null($Order)) {
            $error_message = "注文情報の取得が出来ませんでした。この手続きは無効となりました。";
            $error_title   = "エラー";
            return $this->app['view']->render('error.twig', array('error_message' => $error_message ,'error_title' => $error_title ,));
        }

        // Payment取得
        $objUtil = new PaymentUtil($this->app);
        $PaymentExtension = $objUtil->getPaymentTypeConfig($Order->getPayment()->getId());
        $externalPayment = $PaymentExtension->getExternalPaymentMethod();

        //注文TEL
        $orderTel = $Order->getTel01().$Order->getTel02().$Order->getTel03();

        //決済パラメータ設定
        $arrSendParams = array(
            'clientip'      => $externalPayment->getMemo01()		//クライアントIP
            ,'money'        => $Order->getPaymentTotal()		//決済金額合計
            ,'usrtel'       => $orderTel						//注文時電話番号
            ,'usrmail'      => $Order->getEmail()				//注文時メールアドレス
        );

        // externalOrderPaymentRepoを作成
        $externalOrderPaymentRepo = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment');
        $externalOrderPaymentRepo->setConfig($this->app['config']['ExternalPaymentGateway']['const']);

        // テレコムクレジットのスピード決済を取得
        $speedCode = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE'];
        $paymentCode = $externalPayment->getMemo03();
        $orderIdForSpeed = NULL;
        //スピード決済
        if($speedCode == $paymentCode){

            // 一番最後に購入している商品の注文番号取得 都度決済のみ検索対象
            $orderIdForSpeed = $externalOrderPaymentRepo->getOrderIdForSpeed($Order->getCustomer()->getId());
            $arrSendParams['sendid'] = $orderIdForSpeed;

            // 注文番号
            $arrSendParams['option'] = $Order->getId();

        //都度決済
        } else {

            // 注文番号
            $arrSendParams['sendid'] = $Order->getId();

        }

        //ECCUBE決済完了ページへの遷移URL
        $redirectUrl = $this->app->url('external_shopping_payment_complete');
        $redirectUrl .= '?id='.$this->app['eccube.service.cart']->getPreOrderId();
        $arrSendParams['redirect_url'] = urlencode($redirectUrl);

        // ExternalOrderPaymentを取得
        $externalOrder = $externalOrderPaymentRepo->findRecord($Order->getId());

        // クライアントIP
        $externalOrder->setMemo07($arrSendParams['clientip']);
        // モジュールコード
        $externalOrder->setMemo08($paymentCode);
        // スピード決済用 注文番号
        $externalOrder->setMemo09($orderIdForSpeed);
        // 現在のセッション情報の保存(ログ用)
        $externalOrder->setMemo10(serialize($_SESSION));

        $this->app['orm.em']->persist($externalOrder);
        $this->app['orm.em']->flush();

        // カート削除
        $this->app['eccube.service.cart']->clear()->save();

        // テストモードを取得
        $testMode = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_TEST_MODE'];
        // テストモード
        if ($testMode == 1) {
            $this->lfSendCreditForTest($externalPayment->getMemo02(), $arrSendParams, $redirectUrl, $this->app->url('external_shopping_payment_recv'));

        } else {

            // SSL決済画面へリダイレクト
            return $this->app->redirect($this->createSendCreditUrl($externalPayment->getMemo02(), $arrSendParams ));

        }

    }

    /**
     * データ送信パラメータ作成処理
     */
    function createSendCreditUrl($creditUrl, $arrSendParams ){

        // GET パラメータを作成
        $param = "";
        foreach($arrSendParams as $key => $val){
            $param .= ($param) ? "&" : "?";
            $param .= $key."=".$val;
        }

        // SSL決済画面へリダイレクト内容の返却
        return "$creditUrl.$param";
	}

    /**
     * データ送信処理(テスト用)
     */
    function lfSendCreditForTest($creditUrl, $arrSendParams, $redirectUrl ,$recvUrl){

        $param = "";
        $paramPlus = "";
        foreach($arrSendParams as $key => $val){
            $param .= ($param) ? "&" : "?";
            $param .= $key."=".$val;

            if($key == "option")$paramPlus = "&oneclick=yes";
        }

        //テスト用
        print("<HTML><BODY><b>決済ページ遷移URL：</b><BR />");
        print($creditUrl.$param."<BR /><BR />");
        print('<a href="'.$creditUrl.$param.'" target="_blank">決済画面へ(通常通り)</a>');
        print("<BR /><BR />");

        print("<HR />");

        $speedMode = $paramPlus;
        if($speedMode){
            //スピード決済
            print('<b>スピード決済</b><BR />');
            print('<a href="'.$recvUrl.$param.$paramPlus.'&rel=yes&cont=yes" target="_blank">決済完了処理(テスト)</a>');
        }
        else{
            //都度決済
            print('<b>都度決済</b><BR />');
            print('<a href="'.$recvUrl.$param.$paramPlus.'&rel=yes&cont=no" target="_blank">決済完了処理(テスト)</a>');
        }
        print('<BR /><BR />');
        print('<a href="'.$redirectUrl.'" target="_blank">決済完了画面へ(テスト)</a>');
        print('<BR /><BR />');

        print("<HR />");

        // 決済結果受信デバッグフォーム start
        print ('<form name="DUMMY_recv" action="'.$recvUrl.'" method="get" target="_blank">');
        print('<b>決済完了処理(デバッグ)</b><BR />');
        print('<table border="1" width="30%">');

        $redirect_url_val = "";
        foreach($arrSendParams as $key => $val){

            // リダイレクトは読み飛ばす
            if($key == "redirect_url") {
                $redirect_url_val = $val;
                continue;
            }

            print('<tr>');
            print('<td>'.$key.'</td>');
            print('<td><input type="text" name="'.$key.'"value="'.$val.'"  style="width:100%" /></td>');
            print('</tr>');

            if($key == "option") {
                print('<tr>');
                print('<td>oneclick</td>');
                print('<td><input type="text" name="oneclick" value="yes"  style="width:100%" /></td>');
                print('</tr>');
            }

        }
        // rel
        print('<tr>');
        print('<td>rel</td>');
        print('<td><input type="text" name="rel" value="yes"  style="width:100%" /></td>');
        print('</tr>');
        // cont
        print('<tr>');
        print('<td>cont</td>');
        if($speedMode){
            //スピード決済
            print('<td><input type="text" name="cont" value="yes"  style="width:100%" /></td>');
        }
        else{
            //都度決済
            print('<td><input type="text" name="cont" value="no"  style="width:100%" /></td>');
        }
        print('</tr>');

        print('</table>');
        print('<BR />');

        print('<input type="submit" name="submit" value="recv送信">');
        print('</form>');
        // 決済結果受信デバッグフォーム end

        print("<HR />");

        // リダイレクトURLの分解等
        $uniqId_tmp = array();
        $uniqId = "";
        $compUrl = "";
        $dec_url = rawurldecode($redirect_url_val);
        $uniqId_tmp = explode('?id=', $dec_url);
        if (isset($uniqId_tmp[1])) {
            $uniqId = $uniqId_tmp[1];
            $compUrl = $uniqId_tmp[0];
        }

        // 決済結果画面デバッグフォーム start
        print ('<form name="DUMMY_complete" action="'.$compUrl.'" method="get" target="_blank">');
        print('<b>決済完了処理(デバッグ)</b><BR />');
        print('<table border="1" width="30%">');

        print('<tr>');
        print('<td>id</td>');
        print('<td><input type="text" name="id" value="'.$uniqId.'"  style="width:100%" /></td>');
        print('</tr>');

        print('</table>');
        print('<BR />');
        print('<input type="submit" name="submit" value="complete送信">');

        print('</form>');
        // 決済結果画面デバッグフォーム end
        print("<HR />");
        print("</BODY></HTML>");
        exit();
    }

}
