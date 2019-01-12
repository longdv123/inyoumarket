<?php

namespace Plugin\SimpleCoupon\Tests\Repository;

use Eccube\Common\Constant;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\ConditionCustomer;
use Plugin\SimpleCoupon\Entity\CouponOrder;
use Plugin\SimpleCoupon\Tests;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;

/**
 * Class ConditionCustomerRepositoryTest
 *
 * @package Plugin\SimpleCoupon\Tests\Repository
 */
class ConditionCustomerRepositoryTest extends SimpleCouponTestBase
{

	protected $Customer;
	protected $Coupon;
	
	public function setUp() {
		parent::setUp();
	}
	
    public function testGetQueryBuilderBySearchDataForAdmin_1()
    {
		$Coupon = $this->createCoupon();
    	$Coupon2 = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    
    	$Customer1 = $this->createCustomer();
    	$Customer2 = $this->createCustomer();
    	$Condition1 = $this->createConditionCustomer($Coupon, $Customer1);
    	$Condition2 = $this->createConditionCustomer($Coupon, $Customer2);
    	$Condition3 = $this->createConditionCustomer($Coupon2, $Customer1);
    	$Condition4 = $this->createConditionCustomer($Coupon2, $Customer2);
    	
        $param = array('coupon_id'=>$Coupon->getCouponId());
        $qb = $this->app['eccube.plugin.simplecoupon.repository.condition_customer']->getQueryBuilderBySearchDataForAdmin($param);
        $list = $qb->getQuery()->getResult();
        $this->expected = 2;
        $this->actual = count($list);
        $this->verify();

    }

    public function testGetQueryBuilderBySearchDataForAdmin_2()
    {
    	$Coupon = $this->createCoupon();
    	$Coupon2 = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    
    	$Customer1 = $this->createCustomer();
    	$Customer2 = $this->createCustomer();
    	$Condition1 = $this->createConditionCustomer($Coupon, $Customer1);
    	$Condition2 = $this->createConditionCustomer($Coupon, $Customer2);
    	$Condition3 = $this->createConditionCustomer($Coupon2, $Customer1);
    	$Condition4 = $this->createConditionCustomer($Coupon2, $Customer2);
    
    	$param = array('coupon_id'=>$Coupon->getCouponId(), 'customer_id'=>$Customer1->getId());
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.condition_customer']->getQueryBuilderBySearchDataForAdmin($param);
    	$list = $qb->getQuery()->getResult();
    	$this->expected = 1;
    	$this->actual = count($list);
    	$this->verify();
    
    }
    
    
    
}