<?php
/*
 * Copyright(c) 2015 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\GmoPaymentGateway\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class PaymentServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Setting
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/gmo_payment/config', '\\Plugin\\GmoPaymentGateway\\Controller\\ConfigController::edit')->bind('plugin_GmoPaymentGateway_config');
        // GmoMember management
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/gmo_member', '\\Plugin\\GmoPaymentGateway\\Controller\\GmoMemberController::index')->bind('plugin_GmoPaymentGateway_member');
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/gmo_member/page/{page_no}', '\\Plugin\\GmoPaymentGateway\\Controller\\GmoMemberController::index')->assert('page_no', '\d+')->bind('plugin_GmoPaymentGateway_member_page');
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/gmo_member/organize', '\\Plugin\\GmoPaymentGateway\\Controller\\GmoMemberController::organizeGmoMember')->bind('plugin_GmoPaymentGateway_member_organize');
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/gmo_member/export', '\\Plugin\\GmoPaymentGateway\\Controller\\GmoMemberController::exportGmoMember')->bind('plugin_GmoPaymentGateway_export_member');
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/gmo_member/processDuplicate/{step_no}/{total_step}', '\\Plugin\\GmoPaymentGateway\\Controller\\GmoMemberController::processDuplicate')->assert('step_no', '\d+')->bind('plugin_GmoPaymentGateway_member_process_duplicate');

        
        // Input payment info screens
        $app->match('/shopping/gmo_payment', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentController::index')->bind('gmo_shopping_payment');
        $app->match('/shopping/gmo_payment/back', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentController::goBack')->bind('gmo_shopping_payment_back');
        $app->match('/shopping/gmo_payment_recv', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentRecvController::index')->bind('gmo_shopping_payment_recv');
        $app->match('/shopping/rakutenResult/{result}', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentController::rakutenResult')->bind('gmo_shopping_rakuten_result');
        $app->match('/shopping/tokenProcess', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentController::tokenProcess')->bind('gmo_shopping_token');
        $app->match('/shopping/auResult', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentController::auResult')->bind('gmo_shopping_au_result');
        $app->match('/shopping/docomoResult', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentController::docomoResult')->bind('gmo_shopping_docomo_result');
        $app->match('/shopping/sbResult', '\\Plugin\\GmoPaymentGateway\\Controller\\PaymentController::sbResult')->bind('gmo_shopping_sb_result');
        
        //My page
        //Change card
        $app->post('/mypage/change_card/del', '\\Plugin\\GmoPaymentGateway\\Controller\\MypageCardEditController::delRegisCard')->bind('gmo_delete_card');
        $app->match('/mypage/change_card', '\\Plugin\\GmoPaymentGateway\\Controller\\MypageCardEditController::index')->value('mypageno', 'card')->bind('gmo_mypage_change_card');

        // Order Payment Status Page
        $app->match('/' . $app["config"]["admin_route"] . '/order/gmo_order_status/{paymentStatus}/{paymentType}', '\\Plugin\\GmoPaymentGateway\\Controller\\OrderStatusController::index')
            ->value('paymentStatus', null)->assert('paymentStatus', '\d+|')->value('paymentType', null)->assert('paymentType', '\d+|')->bind('gmo_admin_order_status');

        // クレジット入力ロック解除
        $app->match('/' . $app["config"]["admin_route"] . '/order/gmo_use_limit_unlock', '\\Plugin\\GmoPaymentGateway\\Controller\\UseLimitUnlockController::index')->bind('gmo_admin_use_limit_unlock');

        $app['eccube.plugin.service.payment'] = $app->share(function () use ($app) {
            return new \Plugin\GmoPaymentGateway\Service\PaymentService($app);
        });

        $app['eccube.plugin.gmo_pg.repository.gmo_plugin'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\GmoPaymentGateway\Entity\GmoPlugin');
        });

        $app['eccube.plugin.gmo_pg.repository.gmo_payment_method'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\GmoPaymentGateway\Entity\GmoPaymentMethod');
        });

        $app['eccube.plugin.gmo_pg.repository.gmo_order_payment'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\GmoPaymentGateway\Entity\GmoOrderPayment');
        });

        $app['eccube.plugin.gmo_pg.repository.gmo_member'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\GmoPaymentGateway\Entity\GmoMember');
        });

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\GmoPaymentGateway\Form\Type\PaymentType($app);
            $types[] = new \Plugin\GmoPaymentGateway\Form\Type\ConfigType($app);
            $types[] = new \Plugin\GmoPaymentGateway\Form\Type\RecvType($app);
            $types[] = new \Plugin\GmoPaymentGateway\Form\Type\RegistCreditSelectType($app);
            $types[] = new \Plugin\GmoPaymentGateway\Form\Type\MyPageRegistCreditType($app);
            return $types;
        }));

        // Form extensions
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\PayEasyTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\CvsTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\CreditTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\ATMTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\RakutenIdTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\TokenTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\AuTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\DocomoTypeExtension($app);
            $extensions[] = new \Plugin\GmoPaymentGateway\Form\Extension\SbTypeExtension($app);

            return $extensions;
        }));

        $app['config'] = $app->share($app->extend('config', function ($config) {
            $nav = $config['nav'];

            $addNavi[0]['id'] = "gmo_admin_order_status";
            $addNavi[0]['name'] = "決済状況管理";
            $addNavi[0]['url'] = "gmo_admin_order_status";

            $addNavi[1]['id'] = "gmo_admin_use_limit_unlock";
            $addNavi[1]['name'] = "クレジット入力ロック解除";
            $addNavi[1]['url'] = "gmo_admin_use_limit_unlock";

            foreach ($nav as $key => $val) {
                if ("order" == $val["id"]) {
                    $nav[$key]['child'][] = $addNavi[0];
                    $nav[$key]['child'][] = $addNavi[1];
                    break;
                }
            }

            $config['nav'] = $nav;
            return $config;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
