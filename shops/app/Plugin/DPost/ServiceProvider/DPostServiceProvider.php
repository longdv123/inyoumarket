<?php
/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class DPostServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        /**
         * 管理画面
         */
        // Setting
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/dpost/config', '\\Plugin\\DPost\\Controller\\ConfigController::edit')->bind('plugin_DPost_config');
        // 新着情報Ex
        $app->match('/' . $app["config"]["admin_route"] . '/content/newsex', '\\Plugin\DPost\Controller\Admin\Content\NewsControllerEx::index')->bind('admin_content_news_ex');
        // 新着情報Ex 送信
        $app->match('/' . $app["config"]["admin_route"] . '/content/{id}/newsex_push', '\\Plugin\DPost\Controller\Admin\Content\NewsControllerEx::push')->assert('id', '\d+')->bind('admin_content_news_push');
        // 入荷通知
        $app->match('/' . $app["config"]["admin_route"] . '/product/push', '\\Plugin\DPost\Controller\Admin\Product\ProductPushController::index')->bind('admin_product_push');
        // 入荷通知 送信
        $app->match('/' . $app["config"]["admin_route"] . '/product/{id}/push', '\\Plugin\DPost\Controller\Admin\Product\ProductPushController::push')->assert('id', '\d+')->bind('admin_product_push_send');

        /**
         * フロント画面
         */
        // MyPage D-POST
        $app->match('/mypage/dpost', '\\Plugin\DPost\Controller\Mypage\MypageDPostController::index')->bind('mypage_dpost');
        $app->match('/mypage/dpostcheck', '\\Plugin\DPost\Controller\Mypage\MypageDPostController::check')->bind('mypage_dpost_check');
        // Message D-POST
        $app->match('/dpost/message_index', '\\Plugin\\DPost\Controller\MessageController::index')->bind('dpost_index');
        $app->get('/dpost/message', '\\Plugin\\DPost\Controller\MessageController::message')->bind('dpost_message');
        $app->get('/dpost/redirect', '\\Plugin\\DPost\Controller\MessageController::redirectPage')->bind('dpost_redirect');

        /**
         * メニュー拡張
         */
        $app['config'] = $app->share($app->extend('config', function($config) {

            $config['nav'][0]['child'][] = array(
                'id' => 'product_post',
                'name' => '入荷通知',
                'url' => 'admin_product_push'
            );

            return $config;
        }));


        // Form/Extension
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\DPost\Form\Type\ConfigType($app);
            $types[] = new \Plugin\DPost\Form\Type\DPostType($app);
            return $types;
        }));

        // Repository
        $app['eccube.repository.newsex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\DPost\\Entity\NewsEx');
        });
        $app['eccube.repository.dpostsetting'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\DPost\\Entity\DpostSetting');
        });
        $app['eccube.repository.customerex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\DPost\\Entity\CustomerEx');
        });
        $app['eccube.repository.postqueue'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\DPost\\Entity\PostQueue');
        });
        $app['eccube.repository.productpostqueue'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\DPost\\Entity\ProductPostQueue');
        });
        $app['eccube.repository.productpush'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\DPost\\Entity\ProductPush');
        });

        // Service
        $app['dpost.service.dpostservice'] = $app->share(function () use ($app) {
            return new \Plugin\DPost\Service\DPostService($app);
        });
        $app['dpost.service.renderservice'] = $app->share(function () use ($app) {
            return new \Plugin\DPost\Service\RenderService($app);
        });

    }

    public function boot(BaseApplication $app)
    {
    }
}
