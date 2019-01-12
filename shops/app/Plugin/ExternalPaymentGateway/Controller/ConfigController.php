<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Controller;

use Eccube\Application;
use Plugin\ExternalPaymentGateway\Controller\Util\PaymentUtil;
use Plugin\ExternalPaymentGateway\Controller\Util\PluginUtil;
use Plugin\ExternalPaymentGateway\Form\Type\ConfigType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

/**
 * Controller to handle module setting screen
 */
class ConfigController
{

    /**
     * Edit config
     *
     * @param Application $app
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function edit(Application $app, Request $request)
    {
        //silexのApplicationインスタンス取得
        $this->app = $app;

        //PluginUtilのインスタンス取得　モジュール情報
        $objMdl =& PluginUtil::getInstance($this->app);
        //PaymentUtilのインスタンス取得　決済モジュール情報
        $objUtil = new PaymentUtil($this->app);
        //PluginUtilからモジュール名取得
        $objMdl->setName($this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_MODULE_NAME']);
        $objMdl->setPaymentName($this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_PAYMENT_NAME']);
        $tpl_subtitle = $objMdl->getName();

        //ExternalPluginエンティティから情報取得(sub_data)してセット
        $objMdl->install();

        // Get module code from dtb_plugin
        //yamlファイルのパース
        $self = Yaml::parse(__DIR__ . '/../config.yml');
        //ec-cubeのDBから自プラグイン取得
        $Plugin = $this->app['eccube.repository.plugin']->findOneBy(array('code' => $self['code']));

        //DBにプラグイン情報なければエラー
        if (is_null($Plugin)) {
            $error = "例外エラー プラグインが存在しません。";
            $error_title = 'エラー';

            return $app['twig']->render('error.twig', array(
                'error_title' => $error_title,
                'error_message' => $error,
            ));
        }

        //ExternalPluginのリポジトリをつかって対象コードをもとにデータ取得
        $ExternalPlugin = $this->app['eccube.plugin.external_pg.repository.external_plugin']->findOneBy(array('code' => $Plugin->getCode()));

        //ExternalPluginのリポジトリがなければエラー
        if (is_null($ExternalPlugin)) {
            $error_message = "例外エラー ";
            $error_message .= $objMdl->getPaymentName();
            $error_message .= "のプラグインが存在しません。";
            $error_title = 'エラー';
            return $this->app['view']->render('error.twig', compact('error_message', 'error_title'));
        }
        //ユーザの入力によるデータ取得（フォームデータ）
        $subData = $objMdl->getUserSettings();

        //config.ymlで設定されている決済名全部配列で取得 配列のキーは各決済種別に割り当てられたコード（config.yml参照）
        $Payments = $objUtil->getPaymentTypeNames();
        
        //テレコム決済設定のフォームタイプ生成、既に入力情報あればフォームデータにセット
        $configFrom = new ConfigType($this->app, $subData);
        //FormBuilderつかってフォーム取得型等はconfigFromをもとに自動設定
        $form = $this->app['form.factory']->createBuilder($configFrom)->getForm();

        //リクエストメソッドがPOSTまで一致を確認する
        if ('POST' === $this->app['request']->getMethod()) {
            $form->handleRequest($this->app['request']);

            //フォームタイプ(ConfigType)に設定した各データ属性をもとにバリデーション
            if ($form->isValid()) {

                //ConfigTypeから連想配列でデータ取得
                $formData = $form->getData();

                //トランザクション開始
                $this->app['orm.em']->getConnection()->beginTransaction();
                // $this->savePaymentData($form->getData(), $Payments);
                //対応決済種別保存
                $this->savePaymentData($formData, $Payments);
                //コミット
                $this->app['orm.em']->getConnection()->commit();

                //フラッシュメッセージを設定　EC-CUBEのmessage.ja.ymlに定義しているメッセージ 第二引数はフロントか管理画面の区別(namespaceの切り替えに使う)、adminは管理画面
                $app->addSuccess('admin.register.complete', 'admin');

                //リダイレクト plugin_ExternalPaymentGateway_configはServiceProviderでbindした設定登録画面→ConfigController::edit
                return $this->app->redirect($this->app['url_generator']->generate('plugin_ExternalPaymentGateway_config'));

            }
        }

        //ポストではない場合
        //テレコム決済設定画面表示
        return $this->app['view']->render('ExternalPaymentGateway/View/admin/external_config.twig',
            array(
                'form' => $form->createView(),
                'tpl_subtitle' => $tpl_subtitle,
                'recv_url' => $app->url('external_shopping_payment_recv'),
                'subData' => $subData,
            ));
    }

    /**
     * Save subdata into plg_external_plugin
     * Save payment selected into dtb_payment and plg_external_payment_method
     * @param array $data
     * @param array payment method name $Payments
     */
    public function savePaymentData($data, $Payments)
    {
        $objMdl =& PluginUtil::getInstance($this->app);

        // Regist subdata
        $objMdl->registerUserSettings($data);

        // Delete all payment method at dtb plg_external_payment_method
        $ExternalPaymentRepo = $this->app['eccube.plugin.external_pg.repository.external_payment_method'];
        $ExternalPaymentRepo->setConfig($this->app['config']['ExternalPaymentGateway']['const']);

        // チェックされた決済を登録
        $arrSettingPayment = array();
        $arrSettingPayment = $this->setCreditPayment($data['credit_payment']);

        $installedPayment = array();
        $listId = array();
        foreach ($arrSettingPayment as $paymentTypeId) {
            //インストールされていなければ新規作成
            $id = $this->savePayment($paymentTypeId, $Payments);
            $this->saveExternalPayment($id, $paymentTypeId, $Payments, $data['clientip']);

            $listId[] = $id;
            // 都度決済が選択されていた場合はスピード決済は登録しない
            if (($data['credit_payment'] == $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE']) && 
               ($paymentTypeId == $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE'])){
                continue;
            }
            $installedPayment[] = $id;
        }

        // チェックされていない決済を削除
        foreach (array_diff($listId, $installedPayment) as $paymentId) {
               
                $removeExternalPaymentMethod = $this->app['eccube.plugin.external_pg.repository.external_payment_method']->find($paymentId);
                if (!empty($removeExternalPaymentMethod)){
                    $removeExternalPaymentMethod->setDelFlg(1);
                    $this->app['orm.em']->persist($removeExternalPaymentMethod);
                }
                
                $removePayment = $this->app['eccube.repository.payment']->find($paymentId);
                if (!empty($removePayment)){
                    $removePayment->setDelFlg(1);
                    $this->app['orm.em']->persist($removePayment);
                }
                
                $this->app['orm.em']->flush();
                
        }
    }

    /**
     * Set default value of payment setting
     * @return array default
     */
    function setCreditPayment()
    {
        $listData = array();

        $sslCode =  $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE'];
        $speedCode =  $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE'];

        // 都度決済を作成
        $listData[] = $sslCode;

        // スピード決済を作成
        $listData[] = $speedCode;

        return $listData;
    }

    /**
     * Get default value of payment setting
     * @param integer $payment_type_id
     * @return array default
     */
    function getDefaultPaymentConfig($payment_type_id)
    {
        $listData = array();
        $listData['charge'] = '0';
        $listData['rule_max'] = $this->app['config']['ExternalPaymentGateway']['const']['CREDIT_RULE_MAX'];
        $listData['rule_min'] = $this->app['config']['ExternalPaymentGateway']['const']['CREDIT_RULE_MIN'];

        // 接続先のURLをconfigから取得
        switch ($payment_type_id) {
            case $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE']:
                $listData['connect_url'] = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_URL'];
                break;
            case $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE']:
                $listData['connect_url'] = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_URL'];
        }
        // EXTERNAL_CREDIT_HOSTの部分を接続先に置換する
        if (!empty($listData['connect_url'])) {
            $connect_host = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_HOST'];
            $listData['connect_url'] = str_replace("EXTERNAL_CREDIT_HOST", $connect_host, $listData['connect_url']);
        }

        return $listData;
    }

    /**
     * Insert or update payment that user selected
     * @param integer $paymentTypeId
     * @param array $Payments
     * @return payment_id
     */
    public function savePayment($paymentTypeId, $Payments)
    {

        $arrDefault = $this->getDefaultPaymentConfig($paymentTypeId);
        
        //Get ExternalPaymentMethod of this paymentTypeId without considering del_flg
        $Payment = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')->getPaymentByType($paymentTypeId, true, $this->app);
        
        // If no such data exists, create a new one
        if (is_null($Payment)) {
            $Payment = $this->app['orm.em']->getRepository('\Eccube\Entity\Payment')->findOrCreate(0);
        }

        // If data exists, update some info,
        $Payment->setMethod($Payments[$paymentTypeId]);
        $Payment->setFixFlg(1);
        // 設定がなかったらNULL(無制限)
        if (empty($arrDefault['rule_max'])) {
            $Payment->setRuleMax(null);
        } else {
            $Payment->setRuleMax($arrDefault['rule_max']);
        }
        $Payment->setRuleMin($arrDefault['rule_min']);
        $Payment->setCharge($arrDefault['charge']);
        $Payment->setUpdateDate(new \DateTime());
        $Payment->setDelFlg(0);

        $this->app['orm.em']->persist($Payment);
        $this->app['orm.em']->flush();
        return $Payment->getId();
    }

    /**
     * Insert or update plg_external_payment_method
     * @param integer $id
     * @param integer $paymentId
     * @param array $Payments
     * @param string $clientip
     */
    public function saveExternalPayment($id, $paymentTypeId, $Payments, $clientip)
    {

        $arrDefault = $this->getDefaultPaymentConfig($paymentTypeId);

        $objMdl =& PluginUtil::getInstance($this->app);
        $pluginCode = $objMdl->getCode(true);
        
        $ExternalPayment = $this->app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod')->getExternalPayment('id', $id, true, $this->app);
        
        $createFlg = 0;
        if (is_null($ExternalPayment)) {
            // Create new payment
            $ExternalPayment = $this->app['eccube.plugin.external_pg.repository.external_payment_method']->findOrCreate(0);
            $createFlg = 1;
        }
        
        $ExternalPayment->setId($id);
        $ExternalPayment->setMethod($Payments[$paymentTypeId]);
        $ExternalPayment->setDelFlg(0);
        $ExternalPayment->setUpdateDate(new \DateTime());
        if ($createFlg == 1) {
            $ExternalPayment->setCreateDate(new \DateTime());
        }
        // クライアントIP
        $ExternalPayment->setMemo01($clientip);
        // SSL接続先URL
        $ExternalPayment->setMemo02($arrDefault['connect_url']);
        // 決済TypeId(EC-CUBE2と同じ)
        $ExternalPayment->setMemo03($paymentTypeId);
        $ExternalPayment->setCode($pluginCode);
        $this->app['orm.em']->persist($ExternalPayment);
        $this->app['orm.em']->flush();
    }

}
