<?php

namespace Plugin\SimpleCoupon\Tests\Web\Admin;

use Eccube\Common\Constant;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\ConditionCustomer;

/**
 * Class CouponControllerTest
 *
 * @package Plugin\SimpleCoupon\Tests\Web\Admin
 */
class CouponControllerTest extends AbstractAdminWebTestCase
{

    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_list'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    public function testAdd()
    {
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_add'));
    	$this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    public function testEdit()
    {
    	$Coupon = $this->createCoupon();
    	
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_edit', array('id' => $Coupon->getCouponId())));
    	$this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    public function testDelete()
    {
    	$Coupon = $this->createCoupon();
    	
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_delete', array('id' => $Coupon->getCouponId())));
    	$this->assertTrue($this->client->getResponse()->isRedirection());
    }
    /**/
    public function testDownload()
    {
    	$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush($Coupon);
    
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_download_list_csv', array('id' => $Coupon->getCouponId())));
    	$this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    public function testDownloadDaily()
    {
    	$Coupon = $this->createCoupon();
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush($Coupon);
    
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_download_daily_csv', array('id' => $Coupon->getCouponId())));
    	$this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    public function testEditCondition()
    {
    	$Coupon = $this->createCoupon();
    
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_edit_condition', array('id' => $Coupon->getCouponId())));
    	$this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    public function testConditionCustomerDelete()
    {
    	$Customer = $this->createCustomer();
    	$Coupon = $this->createCoupon();
    	
    	$crawler = $this->client->request('GET', $this->app->url('plg_simplecoupon_admin_coupon_edit_condition_customer_delete', array('id' => $Coupon->getCouponId(), 'customer_id'=>$Customer->getId())));
    	$this->assertTrue($this->client->getResponse()->isRedirection());
    }

    protected function createCoupon($param = array()){
    
    	$Coupon = new Coupon();
    
    	$date1 = new \DateTime();
    	$date2 = new \DateTime();
    	$d1 = $date1->setDate(2016, 1, 1);
    	$d2 = $date2->setDate(2020, 12, 31);
    
    	$Coupon->setCouponCode(isset($param['coupon_code']) ? $param['coupon_code'] : 'abcdefghijk');
    	$Coupon->setCouponName(isset($param['coupon_name']) ? $param['coupon_name'] : 'ユニットテストクーポン');
    	$Coupon->setDiscountType(isset($param['discount_type']) ? $param['discount_type'] : Coupon::DISCOUNT_TYPE_PRICE);
    	$Coupon->setDiscountTargetType(isset($param['discount_target_type']) ? $param['discount_target_type'] : Coupon::DISCOUNT_TARGET_TYPE_TOTAL);
    	$Coupon->setDiscountValue(isset($param['discount_value']) ? $param['discount_value'] : 100);
    	$Coupon->setCombinedUseFlg(isset($param['combined_use_flg']) ? $param['combined_use_flg'] : Coupon::COMBINED_USE_FLG_DENY);
    	$Coupon->setGuestUseFlg(isset($param['guest_use_flg']) ? $param['guest_use_flg'] : Coupon::GUEST_USE_FLG_DENY);
    	$Coupon->setOnetimeUseFlg(isset($param['onetime_use_flg']) ? $param['onetime_use_flg'] : Coupon::ONETIME_USE_FLG_ONETIME);
    	$Coupon->setConditionType(isset($param['condition_type']) ? $param['condition_type'] : Coupon::CONDITION_TYPE_NONE);
    	$Coupon->setNumberOfIssued(isset($param['number_of_issued']) ? $param['number_of_issued'] : 10);
    	$Coupon->setBottomPrice(isset($param['bottom_price']) ? $param['bottom_price'] : 0);
    	$Coupon->setStatus(isset($param['status']) ? $param['status'] : Coupon::STATUS_VALID);
    	$Coupon->setDelFlg(isset($param['del_flg']) ? $param['del_flg'] : Coupon::DEL_FLG_ACTIVE);
    	$Coupon->setFromDate(isset($param['from_date']) ? $param['from_date'] : $d1);
    	$Coupon->setToDate(isset($param['to_date']) ? $param['to_date'] : $d2);
    
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush($Coupon);
    	
    	return $Coupon;
    
    }


}