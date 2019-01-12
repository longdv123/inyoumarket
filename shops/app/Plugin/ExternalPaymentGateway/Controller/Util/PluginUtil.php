<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Controller\Util;

/**
 * 決済モジュール基本クラス
 */
class PluginUtil
{

    private $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /** サブデータを保持する変数 */
    var $subData = null;

    /** モジュール情報 */
    var $pluginInfo = array(
        'pluginCode' => 'ExternalPaymentGateway',
        'externalPluginVersion' => '1.0.0',
    );

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    var $updateFile = array();

    private $pluginCode;

    /**
     * コンストラクタ
     *
     * @return void
     */
    function PluginUtil()
    {
    }

    /**
     * PG_MULPAYのインスタンスを取得する
     *
     * @return PluginUtil
     */
    static function &getInstance($app)
    {
        static $paymentUtil;
        if (empty($paymentUtil)) {
            $paymentUtil = new PluginUtil($app);
        }
        $paymentUtil->init();
        return $paymentUtil;
    }

    /**
     * 初期化処理.
     */
    function init()
    {
        foreach ($this->pluginInfo as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * 終了処理.
     */
    function destroy()
    {
    }

    /**
     * モジュール表示用名称を取得する
     *
     * @return string
     */
    function getName()
    {
        return $this->pluginName;
    }

    /**
     * モジュール表示用名称を設定する
     *
     * @return string
     */
    function setName($pluginName)
    {
        $this->pluginName = $pluginName;
    }

    /**
     * 支払い方法名(決済モジュールの場合のみ)
     *
     * @return string
     */
    function getPaymentName()
    {
        return $this->paymentName;
    }

    /**
     * 支払い方法名(決済モジュールの場合のみ)
     *
     * @return string
     */
    function setPaymentName($paymentName)
    {
        $this->paymentName = $paymentName;
    }

    /**
     * モジュールコードを取得する
     *
     * @param boolean $toLower trueの場合は小文字へ変換する.デフォルトはfalse.
     * @return string
     */
    function getCode($toLower = false)
    {
        $pluginCode = $this->pluginCode;
        return $pluginCode;
    }

    /**
     * モジュールバージョンを取得する
     *
     * @return string
     */
    function getVersion()
    {
        return $this->externalPluginVersion;
    }

    /**
     * サブデータを取得する.
     *
     * @return mixed|null
     */
    function getSubData($key = null)
    {
    	//サブデータすでに取得済みか　isset 存在してるならtrue
        if (isset($this->subData)) {
            if (is_null($key)) {
                return $this->subData;
            } else {
                return $this->subData[$key];
            }
        }

        //PluginUtilからpluginCode取得→ExternalPaymentGateway
        $pluginCode = $this->getCode(true);

        //Migrationで作成したテーブルExternalPluginからリポジストリをつかってデータ取得（検索キー値はExternalPaymentGateway）。列は全て取得
        $ret = $this->app['orm.em']->getRepository('Plugin\ExternalPaymentGateway\Entity\ExternalPlugin')
            ->getSubData($pluginCode);
        
        //データ取得後処理
        if (isset($ret)) {
            //取得データを配列化
            $this->subData = unserialize($ret);

            if (is_null($key)) {
                return $this->subData;
            } else {
                return $this->subData[$key];
            }
        }
        return null;
    }

    /**
     * サブデータをDBへ登録する
     * $keyがnullの時は全データを上書きする
     *
     * @param mixed $data
     * @param string $key
     */
    function registerSubData($data, $key = null)
    {
        $subData = $this->getSubData();

        if (is_null($key)) {
            $subData = $data;
        } else {
            $subData[$key] = $data;
        }
        //配列をシリアル化
        $subDataSer = serialize($subData);

        $pluginCode = $this->getCode(true);
        $ExternalPlugin = $this->app['orm.em']->getRepository('Plugin\ExternalPaymentGateway\Entity\ExternalPlugin')
            ->findOneBy(array('code' => $pluginCode));
        if (!is_null($ExternalPlugin)) {
            $ExternalPlugin->setSubData($subDataSer);
            $this->app['orm.em']->persist($ExternalPlugin);
            $this->app['orm.em']->flush();
        }

        $this->subData = $subData;
    }

    function getUserSettings($key = null)
    {
        $subData = $this->getSubData();
        $returnData = null;

        if (is_null($key)) {
            $returnData = isset($subData['user_settings'])
                ? $subData['user_settings']
                : null;
        } else {
            $returnData = isset($subData['user_settings'][$key])
                ? $subData['user_settings'][$key]
                : null;
        }

        return $returnData;
    }

    function registerUserSettings($data)
    {
        $this->registerSubData($data, 'user_settings');
    }

    /**
     * ログを出力.
     *
     * @param string $msg
     * @param mixed $data
     */
    function printLog($msg, $date = null)
    {
        $path = $this->app['config']['root_dir'] . "/app/log/" . $this->getCode(true) . '_' . date('Ymd') . '.log';

        $text = '';
        if (is_array($msg)) {
            $text = print_r($msg, true);
        } else {
            $text = $msg;
        }
        CommonUtil::printLog($this->app, $text, $path);
    }

    /**
     * インストール処理
     *
     * plg_external_pluginのsub_dataをget
     */
    function install()
    {
    	//テーブルplg_external_pluginからデータ取得　空ならsubdataにセット後取得
        $subData = $this->getSubData();
    }

}
