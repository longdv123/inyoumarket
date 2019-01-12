<?php

namespace Plugin\SimpleCoupon\Tests\Repository;

use Eccube\Common\Constant;
use Eccube\Tests\EccubeTestCase;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;


/**
 * Class CouponOrderRepositoryTest
 *
 * @package Plugin\SimpleCoupon\Tests\Repository
 */
class CouponOrderRepositoryTest extends SimpleCouponTestBase
{

	protected $Customer;
	protected $Coupon;
	
	public function setUp() {
		parent::setUp();
	}
	
	public function testAbc(){
		$this->expected = 1;
		$this->actual = 1;
		$this->verify();
	}
	
    public function testGetCountByCoupon()
    {
		$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	
    	$Customer = $this->createCustomer();
    	$Order = $this->createOrder($Customer);    	
    	$CouponOrder = $this->createCouponOrder($Coupon, $Order, $Customer);
    	
    	$Customer2 = $this->createCustomer();
    	$Order2 = $this->createOrder($Customer);
    	$CouponOrder2 = $this->createCouponOrder($Coupon, $Order2, $Customer2);
    	
        $cout = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCountByCoupon($Coupon);
        $this->expected = 2;
        $this->actual = $cout;
        $this->verify();

    }
    
    public function testGetCountByCoupon_2()
    {
    	$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	 
    	$Customer = $this->createCustomer();
    	$Order = $this->createOrder($Customer);
    	$CouponOrder = $this->createCouponOrder($Coupon, $Order, $Customer);
    	 
    	$Customer2 = $this->createCustomer();
    	$Order2 = $this->createOrder($Customer);
    	$CouponOrder2 = $this->createCouponOrder($Coupon, $Order2, $Customer2);
    	
    	$OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_cancel']);
    	$Order2->setOrderStatus($OrderStatus);
    	$this->app['orm.em']->persist($Order2);
    	$this->app['orm.em']->flush();
    	
    	
    	$cout = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCountByCoupon($Coupon);
    	$this->expected = 1;
    	$this->actual = $cout;
    	$this->verify();
    
    }
    
    public function testGetCountByCoupon_3()
    {
    	$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    
    	$Customer = $this->createCustomer();
    	$Order = $this->createOrder($Customer);
    	$CouponOrder = $this->createCouponOrder($Coupon, $Order, $Customer);
    
    	$Customer2 = $this->createCustomer();
    	$Order2 = $this->createOrder($Customer);
    	$CouponOrder2 = $this->createCouponOrder($Coupon, $Order2, $Customer2);
    	 
    	$Order2->setDelFlg(\Eccube\Common\Constant::ENABLED);
    	$this->app['orm.em']->persist($Order2);
    	$this->app['orm.em']->flush();
    	 
    	 
    	$cout = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCountByCoupon($Coupon);
    	$this->expected = 1;
    	$this->actual = $cout;
    	$this->verify();
    
    }
    
    public function testGetCouponListByOrder(){
    	
    	$Coupon = $this->createCoupon();
    	$Coupon2 = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    	
    	$Customer = $this->createCustomer();
    	$Order = $this->createOrder($Customer);
    	$CouponOrder = $this->createCouponOrder($Coupon, $Order, $Customer);
    	$CouponOrder2 = $this->createCouponOrder($Coupon2, $Order, $Customer);
    	
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	
    	$this->expected = 2;
    	$this->actual = count($list);
    	$this->verify();
    	
    }
    
    public function testGetQueryBuilderByCouponForAdmin(){
    
    	$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	 
    	$Customer = $this->createCustomer();
    	$Customer2 = $this->createCustomer();
    	$Order = $this->createOrder($Customer);
    	$Order2 = $this->createOrder($Customer2);
    	$CouponOrder = $this->createCouponOrder($Coupon, $Order, $Customer);
    	$CouponOrder2 = $this->createCouponOrder($Coupon, $Order2, $Customer2);
    	
    	//multiに数値指定
    	$param = array('multi'=>$Coupon->getCouponId());
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getQueryBuilderByCouponForAdmin($Coupon);
    	$list = $qb->getQuery()->getResult();
    	$this->expected = 2;
    	$this->actual = count($list);
    	$this->verify();
    	
    }
    
    public function testGetQueryBuilderByCouponForAdmin_2(){
    
    	$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    
    	$Customer = $this->createCustomer();
    	$Customer2 = $this->createCustomer();
    	$Order = $this->createOrder($Customer);
    	$Order2 = $this->createOrder($Customer2);
    	$CouponOrder = $this->createCouponOrder($Coupon, $Order, $Customer);
    	$CouponOrder2 = $this->createCouponOrder($Coupon, $Order2, $Customer2);
    	 
    	$OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_cancel']);
    	$Order2->setOrderStatus($OrderStatus);
    	$this->app['orm.em']->persist($Order2);
    	$this->app['orm.em']->flush();
    	
    	
    	//multiに数値指定
    	$param = array('multi'=>$Coupon->getCouponId());
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getQueryBuilderByCouponForAdmin($Coupon);
    	$list = $qb->getQuery()->getResult();
    	$this->expected = 1;
    	$this->actual = count($list);
    	$this->verify();
    	 
    }
    
    public function testGetQueryBuilderByCouponForAdmin_3(){
    
    	$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    
    	$Customer = $this->createCustomer();
    	$Customer2 = $this->createCustomer();
    	$Order = $this->createOrder($Customer);
    	$Order2 = $this->createOrder($Customer2);
    	$CouponOrder = $this->createCouponOrder($Coupon, $Order, $Customer);
    	$CouponOrder2 = $this->createCouponOrder($Coupon, $Order2, $Customer2);
    
    	$Order2->setDelFlg(\Eccube\Common\Constant::ENABLED);
    	$this->app['orm.em']->persist($Order2);
    	$this->app['orm.em']->flush();
    	 
    	
    	//multiに数値指定
    	$param = array('multi'=>$Coupon->getCouponId());
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getQueryBuilderByCouponForAdmin($Coupon);
    	$list = $qb->getQuery()->getResult();
    	$this->expected = 1;
    	$this->actual = count($list);
    	$this->verify();
    
    }

}