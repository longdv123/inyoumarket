<?php

namespace Plugin\ContactList\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ContactListServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        //Repository
        $app['contact_list.repository.contact_list'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ContactList\Entity\ContactList');
        });
        $app['contact_list.repository.contact_reply'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ContactList\Entity\ContactReply');
        });
        $app['contact_list.repository.contact_status'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ContactList\Entity\ContactStatus');
        });

        //設定画面
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/ContactList/config', '\Plugin\\ContactList\\Controller\\ConfigController::index')->bind('plugin_ContactList_config');

        // 一覧
        $app->match('/' . $app["config"]["admin_route"] . '/contacts', '\\Plugin\\ContactList\\Controller\\ContactListController::index')
            ->bind('admin_contacts');
        $app->match('/' . $app["config"]["admin_route"] . '/contacts/page/{page_no}', '\\Plugin\\ContactList\\Controller\\ContactListController::index')
            ->assert('page_no', '\d+')
            ->bind('admin_contacts_page');

        // 編集
        $app->match('/' . $app["config"]["admin_route"] . '/contacts/edit/{contact_id}', '\\Plugin\\ContactList\\Controller\\ContactEditController::index')
            ->assert('contact_id', '\d+')
            ->bind('admin_contacts_edit');

        // 返信
        $app->match('/' . $app["config"]["admin_route"] . '/contacts/reply/{contact_id}', '\\Plugin\\ContactList\\Controller\\ContactReplyController::index')
            ->assert('contact_id', '\d+')
            ->bind('admin_contacts_reply');

        // フォーム登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\ContactList\Form\Type\ContactSearchType($app['config']);
            $types[] = new \Plugin\ContactList\Form\Type\ContactEditType();
            $types[] = new \Plugin\ContactList\Form\Type\ContactReplyType($app['config']);
            $types[] = new \Plugin\ContactList\Form\Type\ContactStatusType();
            $types[] = new \Plugin\ContactList\Form\Type\ConfigType();
            return $types;
        }));

        // メニュー登録
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi = array(
                'id' => "admin_contacts",
                'name' => "問い合わせ管理",
                'icon' => 'cb-comment',
                'has_child' => true,
                'child' => array(
                    array('id' => 'admin_contacts_edit', 'name' => 'お問い合わせマスター', 'url' => 'admin_contacts'),
                    array('id' => 'admin_contacts_status', 'name' => '対応ステータス設定', 'url' => 'plugin_ContactList_config'),
                ),
            );
            $config['nav'][] = $addNavi;
            return $config;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}