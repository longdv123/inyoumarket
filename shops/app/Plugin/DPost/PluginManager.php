<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\DPost;

use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager
{

    public function __construct()
    {
        $this->src = __DIR__ . '/Resource/copy/';
        $this->dst = __DIR__ . '/../../../html/plugin/dpost';

        $this->src2 = __DIR__ . '/Resource/copytop/';
        $this->dst2 = __DIR__ . '/../../../html';
    }

    public function install($config, $app)
    {

    }

    public function uninstall($config, $app)
    {
        $this->startUnInstall($config, $app);
    }

    public function enable($config, $app)
    {
        $this->startInstall($config, $app);
    }

    public function disable($config, $app)
    {

    }

    public function update($config, $app)
    {
        $this->startInstall($config, $app);
    }

    private function startInstall($config, $app)
    {
        // DB周り
        $this->migrationSchema($app, __DIR__.'/Migration', $config['code']);

        // Copy
        $this->fileCopy($this->src, $this->dst);
        $this->fileCopy($this->src2, $this->dst2);
    }

    private function startUnInstall($config, $app)
    {

        // File 削除
        $this->removeFile($this->dst);
        $this->removeFileOne($this->dst2 . "/dpost-service-worker.js");

        // DB周り
        $this->migrationSchema($app, __DIR__.'/Migration', $config['code'], 0);
    }

    private function fileCopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->fileCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function removeFileOne($fileName) {
        unlink($fileName);
    }

    private function removeFile($dir) {
        if (!file_exists($dir))
            return true;
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..')
                continue;
            if (!$this->removeFile($dir . "/" . $item)) {
                chmod($dir . "/" . $item, 0777);
                if (!$this->removeFile($dir . "/" . $item))
                    return false;
            };
        }
        return rmdir($dir);
    }
}