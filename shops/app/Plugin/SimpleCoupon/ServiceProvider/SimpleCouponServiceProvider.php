<?php
/*
* Plugin Name : SimpleCoupon
*
*/

namespace Plugin\SimpleCoupon\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class SimpleCouponServiceProvider implements ServiceProviderInterface
{
    
    private $app;
    
    public function register(BaseApplication $app)
    {
        $this->app = $app;
        
        
        $adminRoute = '/'.$app["config"]["admin_route"];
        
        // ============================================================
        //  Admin クーポン管理画面ルーティング
        // ============================================================
        $app->match($adminRoute.'/simplecoupon', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::index')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_admin_coupon_list');
        $app->match($adminRoute.'/simplecoupon/add', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::edit')->value('id', null)->bind('plg_simplecoupon_admin_coupon_add');
        $app->match($adminRoute.'/simplecoupon/{id}/edit', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::edit')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_admin_coupon_edit');
        $app->match($adminRoute.'/simplecoupon/{id}/delete', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::delete')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_admin_coupon_delete');
        $app->match($adminRoute.'/simplecoupon/{id}/download_list_csv', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::download_list_csv')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_admin_coupon_download_list_csv');
        $app->match($adminRoute.'/simplecoupon/{id}/download_daily_csv', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::download_daily_csv')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_admin_coupon_download_daily_csv');
        $app->match($adminRoute.'/simplecoupon/{id}/edit_condition', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::edit_condition')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_admin_coupon_edit_condition');
        $app->match($adminRoute.'/simplecoupon/{id}/edit_condition/customer/{customer_id}/delete', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::edit_condition_customer_delete')->value('id', null)->assert('id', '\d+|')->value('customer_id', null)->assert('customer_id', '\d+|')->bind('plg_simplecoupon_admin_coupon_edit_condition_customer_delete');
        $app->match($adminRoute.'/simplecoupon/{id}/edit_condition/product/{product_class_id}/delete', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::edit_condition_product_delete')->value('id', null)->assert('id', '\d+|')->value('product_class_id', null)->assert('product_class_id', '\d+|')->bind('plg_simplecoupon_admin_coupon_edit_condition_product_delete');
        $app->match($adminRoute.'/simplecoupon/{id}/edit_condition/category/{category_id}/delete', 'Plugin\SimpleCoupon\Controller\Admin\CouponController::edit_condition_category_delete')->value('id', null)->assert('id', '\d+|')->value('category_id', null)->assert('category_id', '\d+|')->bind('plg_simplecoupon_admin_coupon_edit_condition_category_delete');
        
        // ============================================================
        //  Front クーポン登録画面ルーティング
        // ============================================================
        $app->match('/simplecoupon', 'Plugin\SimpleCoupon\Controller\CouponController::index')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_front_coupon');
        $app->match('/simplecoupon/{id}/delete', 'Plugin\SimpleCoupon\Controller\CouponController::delete')->value('id', null)->assert('id', '\d+|')->bind('plg_simplecoupon_front_coupon_delete');
        
        
        // ============================================================
        // リポジトリの登録
        // ============================================================
        $app['eccube.plugin.simplecoupon.repository.coupon'] = $app->share(function () use ($app) {
        	$repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\Coupon');
        	$repo->setApplication($app);
        	return $repo;
        });
        $app['eccube.plugin.simplecoupon.repository.coupon_order'] = $app->share(function () use ($app) {
        	$repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\CouponOrder');
        	$repo->setApplication($app);
        	return $repo;
        });
        $app['eccube.plugin.simplecoupon.repository.config'] = $app->share(function () use ($app) {
       		$repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\Config');
       		$repo->setApplication($app);
       		return $repo;
       	});
        $app['eccube.plugin.simplecoupon.repository.condition_customer'] = $app->share(function () use ($app) {
       		$repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\ConditionCustomer');
       		$repo->setApplication($app);
       		return $repo;
           });
        $app['eccube.plugin.simplecoupon.repository.condition_product'] = $app->share(function () use ($app) {
            $repo = $app['orm.em']->getRepository('Plugin\SimpleCoupon\Entity\ConditionProduct');
            $repo->setApplication($app);
            return $repo;
        });
        
        
        // ============================================================
        // Formの登録
        // ============================================================
        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
        	$types[] = new \Plugin\SimpleCoupon\Form\Type\Admin\SearchCouponType($app);
        	$types[] = new \Plugin\SimpleCoupon\Form\Type\Admin\CouponType($app);
        	$types[] = new \Plugin\SimpleCoupon\Form\Type\Admin\CouponConditionType($app);
        	$types[] = new \Plugin\SimpleCoupon\Form\Type\UseCouponType($app);
        	return $types;
        }));
        
        
        // ============================================================
        // サービスの登録
        // ============================================================
        $app['eccube.plugin.simplecoupon.service.coupon'] = $app->share(function () use ($app) {
        	return new \Plugin\SimpleCoupon\Service\SimpleCouponService($app);
        });
        $app['eccube.plugin.simplecoupon.service.coupon_csv_export'] = $app->share(function () use ($app) {
            $csvService = new \Plugin\SimpleCoupon\Service\SimpleCouponCsvExportService();
            $csvService->setEntityManager($app['orm.em']);
            $csvService->setApp($app);
            $csvService->setConfig($app['config']);
            $csvService->setCouponOrderRepository($app['eccube.plugin.simplecoupon.repository.coupon_order']);
            return $csvService;
        });
        
        // ============================================================
        // メッセージ登録
        // ============================================================
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
        	$translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
        	
        	$file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
        	if (file_exists($file)) {
        		$translator->addResource('yaml', $file, $app['locale']);
        	}
        	
        	return $translator;
        }));
        
        
        // ============================================================
        // メニュー登録
        // ============================================================
        $app['config'] = $app->share($app->extend('config', function ($config) {
        	$addNavi['id'] = "plg_simplecoupon_admin_coupon";
        	$addNavi['name'] = "クーポン管理";
        	$addNavi['url'] = "plg_simplecoupon_admin_coupon_list";
        	
        	$nav = $config['nav'];
        	foreach ($nav as $key => $val) {
        		if ("order" == $val["id"]) {
        			$nav[$key]['child'][] = $addNavi;
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