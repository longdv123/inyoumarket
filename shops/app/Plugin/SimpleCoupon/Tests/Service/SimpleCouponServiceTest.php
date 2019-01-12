<?php

namespace Plugin\SimpleCoupon\Tests\Service;

use Eccube\Common\Constant;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Tests;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Plugin\SimpleCoupon\Entity\CouponOrder;


/**
 * Class SimpleCouponServiceTest
 *
 * @package Plugin\SimpleCoupon\Tests\Service
 */
class SimpleCouponServiceTest extends SimpleCouponTestBase
{

	protected $Customer;
	protected $Coupon;
	protected $client;
	
	public function setUp() {
		parent::setUp();
		$this->client = $this->createClient();
	}
	
    public function testUseCoupon_1()
    {
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE, 'discount_value'=>200);
		$Coupon = $this->createCoupon($param);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	
    	$Customer = $this->login();
    	
    	$Order = $this->createOrder($Customer);
    	$total_price = $Order->getTotalPrice();
    	$discount = $Order->getDiscount();
    	
    	//Execute Test Target Method
        $this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
        
        //Assertion
        $Order_Check = $this->app['eccube.repository.order']->findOneBy(array('id'=>$Order->getId()));
        $this->assertEquals($Order_Check->getDiscount(), 200);
        $this->assertEquals($Order->getTotalPrice(), $total_price - 200);
        
    }
    
    public function testUseCoupon_2()
    {
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE, 'discount_value'=>200, 'combined_use_flg'=>Coupon::COMBINED_USE_FLG_ALLOW);
    	$Coupon = $this->createCoupon($param);
    	$param2 = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE, 'discount_value'=>1000, 'combined_use_flg'=>Coupon::COMBINED_USE_FLG_ALLOW);
    	$Coupon2 = $this->createCoupon($param2);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    	 
    	$Customer = $this->login();
    	 
    	$Order = $this->createOrder($Customer);
    	$total_price = $Order->getTotalPrice();
    	$discount = $Order->getDiscount();
    	 
    	//Execute Test Target Method
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon2);
    
    	//Assertion
    	$Order_Check = $this->app['eccube.repository.order']->findOneBy(array('id'=>$Order->getId()));
    	$this->assertEquals($Order_Check->getDiscount(), 1200);
    	$this->assertEquals($Order_Check->getTotalPrice(), $total_price - 1200);
    
    }
    
    
    /**
     * 商品小計からの値引きの場合で、クーポンが値引きが率で設定されている場合。
     * 値引き額 = 商品小計に対する率で計算されている
     */
    public function testUseCoupon_3()
    {
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_RATE, 
    			'discount_value'=>40, 
    			'combined_use_flg'=>Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL
    	);
    	$Coupon = $this->createCoupon($param);
    	$param2 = array('discount_type'=>Coupon::DISCOUNT_TYPE_RATE, 
    			'discount_value'=>10, 
    			'combined_use_flg'=>Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL
    	);
    	$Coupon2 = $this->createCoupon($param2);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    
    	$Customer = $this->login();
    
    	$Order = $this->createOrder($Customer);
    	$total_price = $Order->getTotalPrice();
    	$subtotal = $Order->getSubtotal();
    	$discount = $Order->getDiscount();
    
    	//Execute Test Target Method
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon2);
    
    	//Assertion //小数点の扱いの積み上げで多少の誤差はでる。
    	$Order_Check = $this->app['eccube.repository.order']->findOneBy(array('id'=>$Order->getId()));
    	$this->assertTrue(abs($Order_Check->getDiscount() - $subtotal / 2) <= 2);
    	$this->assertTrue(abs($Order_Check->getTotalPrice() - ($total_price - ($subtotal / 2)))<=2);
    
    }
    
    /**
     * 商品小計からの値引きの場合で、クーポンの値引き金額が商品小計より多い場合。
     * 値引き額 = 商品小計になる
     */
    public function testUseCoupon_4()
    {
    	$Customer = $this->login();
    	$Order = $this->createOrder($Customer);
    	$total_price = $Order->getTotalPrice();
    	$subtotal = $Order->getSubtotal();
    	$discount = $Order->getDiscount();
    	
    	
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE,
    			'discount_value' => ($subtotal - 1000),
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL
    	);
    	$Coupon = $this->createCoupon($param);
    	$param2 = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE,
    			'discount_value'=> $subtotal,
    			'combined_use_flg'=>Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL
    	);
    	$Coupon2 = $this->createCoupon($param2);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    
    	
    
    	//Execute Test Target Method
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon2);
    
    	//Assertion //小数点の扱いの積み上げで多少の誤差はでる。
    	$Order_Check = $this->app['eccube.repository.order']->findOneBy(array('id'=>$Order->getId()));
    	$this->assertEquals($Order_Check->getDiscount() , $subtotal);
    	$this->assertEquals($Order_Check->getTotalPrice() , $total_price - $subtotal);
    
    }
    
    
    public function testCancelCoupon(){
    	
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE, 'discount_value'=>200);
    	$Coupon = $this->createCoupon($param);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	
    	$Customer = $this->login();
    	 
    	$Order = $this->createOrder($Customer);
    	$total_price = $Order->getTotalPrice();
    	$discount = $Order->getDiscount();
    	
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	$CouponOrder = $list[0];
    	
    	
    	//Execute Test Target Method
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->cancelCoupon($CouponOrder);
    	
    	//Assertion
    	$Order_Check = $this->app['eccube.repository.order']->findOneBy(array('id'=>$Order->getId()));
    	$this->assertEquals($Order_Check->getDiscount(), 0);
    	$this->assertEquals($Order_Check->getTotalPrice(), $total_price);
    	
    	
    }
    
    
    public function testIsUsedCoupon_1(){
    	
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE, 'discount_value'=>200);
    	$Coupon = $this->createCoupon($param);
    	$Coupon2 = $this->createCoupon($param);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    	
    	$Customer = $this->login();
    	
    	$Order = $this->createOrder($Customer);
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    	
    	
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	foreach($list as $CouponOrder){
    		$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
    		$this->app['orm.em']->persist($CouponOrder);
    	}
    	$this->app['orm.em']->flush();
  	
    	//Execute Test Target Method
    	$result = $this->app['eccube.plugin.simplecoupon.service.coupon']->isUsedCoupon($Coupon, $Customer);
    	$result2 = $this->app['eccube.plugin.simplecoupon.service.coupon']->isUsedCoupon($Coupon2, $Customer);
    	 
    	//Assertion
    	$this->assertEquals(true, $result);
    	$this->assertEquals(false, $result2);
    	
    }
    
    public function testIsUsedCoupon_2(){
    	 
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE, 'discount_value'=>200);
    	$Coupon = $this->createCoupon($param);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    	 
    	$Customer = $this->login();
    	 
    	$Order = $this->createOrder($Customer);
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    	 
    	 
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	foreach($list as $CouponOrder){
    		$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
    		$this->app['orm.em']->persist($CouponOrder);
    	}
    	$OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config']['order_cancel']);
    	$Order->setOrderStatus($OrderStatus);
    	$this->app['orm.em']->persist($Order);
    	$this->app['orm.em']->flush();
    	 
    	//Execute Test Target Method
    	$result = $this->app['eccube.plugin.simplecoupon.service.coupon']->isUsedCoupon($Coupon, $Customer);
    
    	//Assertion
    	$this->assertEquals(false, $result);
    	 
    }
    
    public function testIsUsedCoupon_3(){
    
    	//Data Preperation For This Test
    	$param = array('discount_type'=>Coupon::DISCOUNT_TYPE_PRICE, 'discount_value'=>200);
    	$Coupon = $this->createCoupon($param);
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush();
    
    	$Customer = $this->login();
    
    	$Order = $this->createOrder($Customer);
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
    
    
    	$list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
    	foreach($list as $CouponOrder){
    		$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
    		$this->app['orm.em']->persist($CouponOrder);
    	}
    	$Order->setDelFlg(\Eccube\Common\Constant::ENABLED);
    	$this->app['orm.em']->persist($Order);
    	$this->app['orm.em']->flush();
    
    	//Execute Test Target Method
    	$result = $this->app['eccube.plugin.simplecoupon.service.coupon']->isUsedCoupon($Coupon, $Customer);
    
    	//Assertion
    	$this->assertEquals(false, $result);
    
    }
    
    
    public function testGetPaymentIdForCoupon(){
    	$paymentId = $this->app['eccube.plugin.simplecoupon.service.coupon']->getPaymentIdForCoupon();
    	$this->assertEquals(is_numeric($paymentId), true);
    }
    
    public function testGetPaymentForCoupon(){
    	$Payment = $this->app['eccube.plugin.simplecoupon.service.coupon']->getPaymentForCoupon();
    	$this->assertEquals($Payment instanceof \Eccube\Entity\Payment, true);
    }
    
}