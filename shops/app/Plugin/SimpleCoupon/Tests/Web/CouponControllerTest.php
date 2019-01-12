<?php

namespace Plugin\SimpleCoupon\Tests\Web;

use Eccube\Common\Constant;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\ConditionCustomer;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
//use Eccube\Tests\Web\AbstractWebTestCase;
use Plugin\SimpleCoupon\Tests\Web\SimpleCouponWebTestBase;

/**
 * Class CouponControllerTest
 *
 * @package Plugin\SimpleCoupon\Tests\Web
 */
class CouponControllerTest extends SimpleCouponWebTestBase
{
    
    public function testIndex()
    {
    	$faker = $this->getFaker();
    	$Customer = $this->logIn();
    	$client = $this->client;
    	
    	$Order = $this->createPreOrder($client, $Customer);
    	
    	// クーポン登録画面
    	$crawler = $client->request('GET', $this->app->url('plg_simplecoupon_front_coupon'));
    	$this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    
    public function testIndexPost()
    {
    	$faker = $this->getFaker();
    	$Customer = $this->logIn();
    	$Coupon = $this->createCoupon();
    	$client = $this->client;
    	
    	$Order = $this->createPreOrder($client, $Customer);
    	
    	// クーポン登録画面
    	$client->request('GET', $this->app->url('plg_simplecoupon_front_coupon'));
    	
    	// クーポン登録処理（テスト対象）
    	$client->request(
    			'POST', 
    			$this->app->url('plg_simplecoupon_front_coupon'),
    			array(
    					'plg_simplecoupon_use_coupon' => array(
    						'coupon_code'=>$Coupon->getCouponCode(),
    						'_token' => 'dummy'
    					)
    			)
    	);
    	
    	$this->assertTrue($this->client->getResponse()->isRedirection());
    	
    	// 登録されているかも確認する。
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	$this->assertEquals(1, count($list));
    }
    
    
    public function testDelete()
    {
    	$Customer = $this->logIn();
    	$client = $this->client;
    	$Order = $this->createPreOrder($client, $Customer);
 
    	$Coupon = $this->createCoupon();
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	
    	// クーポン削除処理（テスト対象）
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_front_coupon_delete', array('id' => $list[0]->getCouponOrderId())));
    	$this->assertTrue($this->client->getResponse()->isRedirection());
    	
    	// 削除されているかも確認する。
    	$list2 = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	$this->assertEquals(0, count($list2));
    }
    
}