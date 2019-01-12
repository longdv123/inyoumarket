<?php

namespace Plugin\SimpleCoupon\Tests\Repository;

use Eccube\Common\Constant;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Tests;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;

/**
 * Class CouponRepositoryTest
 *
 * @package Plugin\SimpleCoupon\Tests\Repository
 */
class CouponRepositoryTest extends SimpleCouponTestBase
{

	protected $Customer;
	protected $Coupon;
	
	public function setUp() {
		parent::setUp();
	}
	
    public function testGetQueryBuilderBySearchDataForAdmin_1()
    {
		$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	
    	//multiに数値指定
        $param = array('multi'=>$Coupon->getCouponId());
        $qb = $this->app['eccube.plugin.simplecoupon.repository.coupon']->getQueryBuilderBySearchDataForAdmin($param);
        $list = $qb->getQuery()->getResult();
        $this->expected = 1;
        $this->actual = count($list);
        $this->verify();

    }
    
    public function testGetQueryBuilderBySearchDataForAdmin_2()
    {
    	$Coupon = $this->createCoupon(array('coupon_name'=>'あおいうえお'));
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	 
    	//multiに数値以外指定
    	$param = array('multi'=>'おいうえお');
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.coupon']->getQueryBuilderBySearchDataForAdmin($param);
    	$list = $qb->getQuery()->getResult();
    	$this->expected = 1;
    	$this->actual = count($list);
    	$this->verify();
    
    }
    
    public function testGetQueryBuilderBySearchDataForAdmin_3()
    {
    	$Coupon = $this->createCoupon(array('coupon_name'=>'あおいうえお', 'status' =>1));
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    
    	//status
    	$param = array('status'=>'0');
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.coupon']->getQueryBuilderBySearchDataForAdmin($param);
    	$list = $qb->getQuery()->getResult();
    	$exist = false;
    	foreach($list as $C){
    		if($C->getCouponId() == $Coupon->getCouponId()){
    			$exist = true;
    		}
    	}
    	$this->expected = false;
    	$this->actual = $exist;
    	$this->verify();
    
    }
    
    public function testGetQueryBuilderBySearchDataForAdmin_4()
    {
    	$date1 = new \DateTime();
    	$date2 = new \DateTime();
    	$d1 = $date1->setDate(2030, 11, 1);
    	$d2 = $date2->setDate(2030, 10, 30);
    	$Coupon = $this->createCoupon(array('to_date'=>$d1));
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    
    	//開始日
    	$param = array('date_start'=>$d2);
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.coupon']->getQueryBuilderBySearchDataForAdmin($param);
    	$list = $qb->getQuery()->getResult();
    	$this->expected = 1;
    	$this->actual = count($list);
    	$this->verify();
    	
    }
    
    public function testGetQueryBuilderBySearchDataForAdmin_5()
    {
    	$date1 = new \DateTime();
    	$date2 = new \DateTime();
    	$date3 = new \DateTime();
    	$d1 = $date1->setDate(2030, 11, 1);
    	$d2 = $date2->setDate(2030, 10, 30);
    	$d3 = $date3->setDate(2030, 12, 30);
    	$Coupon = $this->createCoupon(array('from_date'=>$d2, 'to_date'=>$d3));
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    
    	//終了日
    	$param = array('date_end'=>$d1);
    	$qb = $this->app['eccube.plugin.simplecoupon.repository.coupon']->getQueryBuilderBySearchDataForAdmin($param);
    	$list = $qb->getQuery()->getResult();
    	$this->expected = 1;
    	$this->actual = count($list);
    	$this->verify();
    	
    }

}