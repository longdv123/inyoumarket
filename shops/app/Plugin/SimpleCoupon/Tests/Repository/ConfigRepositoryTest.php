<?php

namespace Plugin\SimpleCoupon\Tests\Repository;

use Eccube\Common\Constant;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\Config;
use Plugin\SimpleCoupon\Tests;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;

/**
 * Class ConfigRepositoryTest
 *
 * @package Plugin\SimpleCoupon\Tests\Repository
 */
class ConfigRepositoryTest extends SimpleCouponTestBase
{
	
    public function testGetCouponPaymentId()
    {
        $paymentId = $this->app['eccube.plugin.simplecoupon.repository.config']->getCouponPaymentId();
        $this->assertEquals(is_numeric($paymentId), true);
    }
    
    public function testFindOrCreate(){
    	$Config = $this->app['eccube.plugin.simplecoupon.repository.config']->findOrCreate();
    	$this->assertEquals($Config instanceof \Plugin\SimpleCoupon\Entity\Config, true);
    }

}