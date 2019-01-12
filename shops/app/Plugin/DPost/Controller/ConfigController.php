<?php

/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Controller;

use Eccube\Application;
use Plugin\DPost\Form\type\ConfigType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Controller to handle setting
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
        $this->app = $app;

        $tpl_subtitle = "D-POST設定";

        // データ取得
        /* @var $DpostSetting \Plugin\DPost\Entity\DpostSetting */
        $DpostSetting = $app['eccube.repository.dpostsetting']->findOneBy(array());

        if(empty($DpostSetting)) {
            $DpostSetting = new \Plugin\DPost\Entity\DpostSetting();
        }

        $form = $this->app['form.factory']->createBuilder('dpostconfig', $DpostSetting)->getForm();

        if ('POST' == $this->app['request']->getMethod()) {

            $form->handleRequest($this->app['request']);

            if ($form->isValid()) {
                $formData = $form->getData();

                $this->app['orm.em']->getConnection()->beginTransaction();

                $this->app['orm.em']->persist($DpostSetting);
                $this->app['orm.em']->flush();

                $this->app['orm.em']->getConnection()->commit();


                // JSON更新
                $upResult = $this->updateJSON($DpostSetting->getProjectId());

                $fs = new Filesystem();
                $fs->dumpFile($upResult[0], $upResult[1]);

                $app->addSuccess('admin.register.complete', 'admin');

                // twig キャッシュの削除.
                $finder = Finder::create()->in($app['config']['root_dir'] . '/app/cache/twig');
                $fs->remove($finder);

                return $this->app->redirect($this->app['url_generator']->generate('plugin_DPost_config'));
            }
        }

        return $this->app['view']->render('DPost/Resource/template/admin/dpost_config.twig',
            array(
                'form' => $form->createView(),
                'tpl_subtitle' => $tpl_subtitle,
            ));
    }

    public function updateJSON($projectId) {

        $config = $this->app['config'];
        $pluginHtmlPath = $config['plugin_html_urlpath'] . "dpost/";
        $fileName = $pluginHtmlPath . "log-192x192.png";

        $value = '';
        $value .= '{' . "\n";
//         $value .= '  "name": "D-POST",' . "\n";
//         $value .= '    "short_name": "D-POST",' . "\n";
//         $value .= '  "icons": [{' . "\n";
//         $value .= '        "src": "' . $fileName . '",' . "\n";
//         $value .= '        "sizes": "192x192"' . "\n";
//         $value .= '      }],' . "\n";
//         $value .= '  "start_url": "./mypage/dpost",' . "\n";
//         $value .= '  "display": "standalone",' . "\n";
        $value .= '  "gcm_sender_id": "' . $projectId . '",' . "\n";
        $value .= '' . "\n";
        $value .= '  "//": "gcm_user_visible_only is only needed until Chrome 44 is in stable ",' . "\n";
        $value .= '  "gcm_user_visible_only": true' . "\n";
        $value .= '}';

        $jsonFileName = $config['root_dir'] . "/html/plugin/dpost/manifest.json";

        return array($jsonFileName, $value);
    }

}
