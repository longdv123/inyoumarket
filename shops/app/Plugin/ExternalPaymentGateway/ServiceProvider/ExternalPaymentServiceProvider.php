<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ExternalPaymentServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // プラグイン一覧画面の「設定」アンカーの遷移先
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/external_payment/config', '\\Plugin\\ExternalPaymentGateway\\Controller\\ConfigController::edit')->bind('plugin_ExternalPaymentGateway_config');

        // 支払方法登録・編集画面の「設定」アンカーの遷移先
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/external_payment/PaymentRegister', '\\Plugin\\ExternalPaymentGateway\\Controller\\PaymentRegisterController::edit')->bind('plugin_ExternalPaymentGateway_PaymentRegister');

        // Input payment info screens
        // テレコムクレジットのリダイレクト前の画面
        $app->match('/shopping/external_payment', '\\Plugin\\ExternalPaymentGateway\\Controller\\PaymentController::index')->bind('external_shopping_payment');
        //結果通知先URL
        $app->match('/shopping/credit_payment_recv', '\\Plugin\\ExternalPaymentGateway\\Controller\\PaymentRecvController::index')->bind('external_shopping_payment_recv');
        //結果画面
        $app->match('/shopping/external_payment_complete', '\\Plugin\\ExternalPaymentGateway\\Controller\\PaymentRecvController::complete')->bind('external_shopping_payment_complete');

        //無名関数
        $app['eccube.plugin.service.payment'] = $app->share(function () use ($app) {
            return new \Plugin\ExternalPaymentGateway\Service\PaymentService($app);
        });

        $app['eccube.plugin.external_pg.repository.external_plugin'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ExternalPaymentGateway\Entity\ExternalPlugin');
        });

        $app['eccube.plugin.external_pg.repository.external_payment_method'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod');
        });

        $app['eccube.plugin.external_pg.repository.external_order_payment'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\ExternalPaymentGateway\Entity\ExternalOrderPayment');
        });

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            // プラグイン設定画面
            $types[] = new \Plugin\ExternalPaymentGateway\Form\Type\ConfigType($app);
            return $types;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
