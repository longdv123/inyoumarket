<?php
/*
 * Copyright(c) 2017 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\GmoPaymentGateway\Controller\Util;

/**
 * 決済モジュール用 アカウントロック情報管理クラス
 */
class AccountLockUtil {
    private $app;

    /** ロック情報格納ファイルへのパス */
    var $dataFilePath;

    /** ロック検出時間(分) */
    var $limit_min;

    /** エラー上限回数 */
    var $limit_count;

    /** ロック時間(分) */
    var $lock_min;

    /**
     * コンストラクタ
     */
    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * パラメータをセットする
     */
    function setParameter($limit_min = 0, $limit_count = 0, $lock_min = 0) {
        $objMdl =& PluginUtil::getInstance($this->app);
        $this->dataFilePath = $this->app['config']['root_dir'] . '/app/log/' . $objMdl->getCode(true) . '_lock.log';
        $this->limit_min = $limit_min;
        $this->limit_count = $limit_count;
        $this->lock_min = $lock_min;
    }

    /**
     * SC_Util_PG_MULPAY_AccountLock のインスタンスを取得する
     *
     * @return SC_Util_PG_MULPAY_AccountLock
     */
    static function &getInstance
        ($app, $limit_min = 0, $limit_count = 0, $lock_min = 0) {
        static $_objAccountLock;

        if (empty($_objAccountLock)) {
            $_objAccountLock = new AccountLockUtil($app);
        }

        $_objAccountLock->setParameter($limit_min, $limit_count, $lock_min);

        return $_objAccountLock;
    }

    /**
     * リモートアドレスを取得する
     */
    function getRemoteAddr() {
        $ipAddr = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
            !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddrs = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddr = $ipAddrs[0];
        }

        return $ipAddr;
    }

    /**
     * 終了処理
     */
    function finish($fp, $result = false) {
        flock($fp, LOCK_UN);
        fclose($fp);

        return $result;
    }

    /**
     * ロック中かどうかをチェック
     */
    function isLock($ipAddr = "") {
        if (empty($ipAddr)) {
            $ipAddr = $this->getRemoteAddr();
        }

        $fp = fopen($this->dataFilePath, "cb+");
        if (!flock($fp, LOCK_SH)) {
            return $this->finish($fp);
        }

        $data = stream_get_contents($fp);
        if (empty($data)) {
            return $this->finish($fp);
        }

        $lockInfos = unserialize($data);
        if (!is_array($lockInfos) || !isset($lockInfos[$ipAddr])) {
            return $this->finish($fp);
        }

        $lockInfo = $lockInfos[$ipAddr];

        $lock_time =
            \DateTime::createFromFormat('YmdHis', $lockInfo['date_time']);
        $lock_time->add(new \DateInterval('PT' . $this->lock_min . 'M'));

        if ($lockInfo['error_count'] >= $this->limit_count &&
            $lock_time->format('YmdHis') >= date('YmdHis')) {
            return $this->finish($fp, true);
        }

        return $this->finish($fp);
    }

    /**
     * ロックを解除する
     */
    function unLock($ipAddr = "") {
        if (empty($ipAddr)) {
            $ipAddr = $this->getRemoteAddr();
        }

        $fp = fopen($this->dataFilePath, "cb+");
        if (!flock($fp, LOCK_EX)) {
            $this->finish($fp);
            return;
        }

        $data = stream_get_contents($fp);
        if (empty($data)) {
            $this->finish($fp);
            return;
        }

        $lockInfos = unserialize($data);
        if (!is_array($lockInfos) || !isset($lockInfos[$ipAddr])) {
            $this->finish($fp);
            return;
        }

        unset($lockInfos[$ipAddr]);

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, serialize($lockInfos));
        fflush($fp);

        $this->finish($fp);
    }

    /**
     * エラーをカウントしロック状態を返す
     */
    function errCount($ipAddr = "") {
        if (empty($ipAddr)) {
            $ipAddr = $this->getRemoteAddr();
        }

        $fp = fopen($this->dataFilePath, "cb+");
        if (!flock($fp, LOCK_EX)) {
            $this->finish($fp);
            return $this->isLock($ipAddr);
        }

        $now = date('YmdHis');

        $data = stream_get_contents($fp);
        if (empty($data)) {
            $lockInfos = array();
            $lockInfos[$ipAddr] = array('date_time' => $now,
                                        'error_count' => 0);
        } else {
            $lockInfos = unserialize($data);
            if (!is_array($lockInfos)) {
                $lockInfos = array();
            }
            if (!isset($lockInfos[$ipAddr])) {
                $lockInfos[$ipAddr] = array('date_time' => $now,
                                            'error_count' => 0);
            }
        }

        $lockInfo =& $lockInfos[$ipAddr];

        $lock_time =
            \DateTime::createFromFormat('YmdHis', $lockInfo['date_time']);
        $lock_time->add(new \DateInterval('PT' . $this->lock_min . 'M'));

        $limit_time =
            \DateTime::createFromFormat('YmdHis', $lockInfo['date_time']);
        $limit_time->add(new \DateInterval('PT' . $this->limit_min . 'M'));

        if ($lockInfo['error_count'] >= $this->limit_count &&
            $lock_time->format('YmdHis') >= $now) {
            // ロック中（何もしない）
            ;
        } else {
            // ロックされていない
            if ($limit_time->format('YmdHis') >= $now) {
                // 検出時間内
                ++$lockInfo['error_count'];
            } else {
                // 検出時間外
                $lockInfo['date_time'] = $now;
                $lockInfo['error_count'] = 1;
            }
        }

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, serialize($lockInfos));
        fflush($fp);

        $this->finish($fp);

        return $this->isLock($ipAddr);
    }

    /**
     * アカウント情報配列を返す
     */
    function getAccountInfo($ipAddr = "") {
        $result = array();

        if (empty($ipAddr)) {
            $ipAddr = $this->getRemoteAddr();
        }

        $fp = fopen($this->dataFilePath, "cb+");
        if (!flock($fp, LOCK_SH)) {
            return $this->finish($fp, $result);
        }

        $data = stream_get_contents($fp);
        if (empty($data)) {
            return $this->finish($fp, $result);
        }

        $lockInfos = unserialize($data);
        if (!is_array($lockInfos) || !isset($lockInfos[$ipAddr])) {
            return $this->finish($fp, $result);
        }

        $result = array('ipaddress' => $ipAddr);
        $result = array_merge($result, $lockInfos[$ipAddr]);

        return $this->finish($fp, $result);
    }

    /**
     * IP指定の場合はそのアカウント情報配列を返す
     * IPを指定しない場合はロック中のアカウント情報配列をすべて返す
     */
    function getLockList($ipAddr = "") {
        $results = array();

        if (!empty($ipAddr)) {
            $result = $this->getAccountInfo($ipAddr);
            if (count($result) > 0) {
                $results[] = $result;
            }
            return $results;
        }

        $fp = fopen($this->dataFilePath, "cb+");
        if (!flock($fp, LOCK_SH)) {
            return $this->finish($fp, $results);
        }

        $data = stream_get_contents($fp);
        if (empty($data)) {
            return $this->finish($fp, $results);
        }

        $lockInfos = unserialize($data);
        if (!is_array($lockInfos)) {
            return $this->finish($fp, $results);
        }

        foreach ($lockInfos as $key => $value) {
            if ($this->isLock($key)) {
                $info = array('ipaddress' => $key);
                $info = array_merge($info, $lockInfos[$key]);
                $results[] = $info;
            }
        }

        return $this->finish($fp, $results);
    }
}
?>
