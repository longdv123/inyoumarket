<?php
/*
 */
namespace Plugin\SimpleCoupon\Tests\Form\Type\Admin;

use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\Test\TypeTestCase;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class CouponTypeTest extends SimpleCouponTestBase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        	'coupon_code' => 'abcdefg1234567890',
    		'coupon_name' => 'ユニットテストクーポン',
    		'discount_type' => Coupon::DISCOUNT_TYPE_PRICE,
    		'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_TOTAL,
    		'discount_value' => 200,
    		'from_date' => '2016-10-01',
    		'to_date' => '2017-10-01',
    		'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    		'guest_use_flg' => Coupon::GUEST_USE_FLG_DENY,
    		'onetime_use_flg' => Coupon::ONETIME_USE_FLG_ONETIME,
    		'number_of_issued' => 100,
    		'status' => Coupon::STATUS_VALID,
    		'bottom_price' => 0
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('plg_simplecoupon_admin_coupon', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }

	/**
	 * 新規登録時のクーポンコードの重複エラー
	 */
    public function testInValidCouponCode_Exists_ForInsert()
    {
    	$Coupon = $this->createCoupon(array('coupon_code'=>'utcoupon123'));
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush($Coupon);
    	
        $this->formData['coupon_code']= 'utcoupon123';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    
    /**
     * 更新時のクーポン重複チェック
     */
    public function testValidCouponCode_Exists_ForUpdate()
    {
    	$Coupon = $this->createCoupon(array('coupon_code'=>'utcoupon123'));
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->flush($Coupon);
    	 
    	$this->formData['coupon_code']= 'utcoupon123';
    
    	$form = $this->app['form.factory']
    	->createBuilder('plg_simplecoupon_admin_coupon', $Coupon, array(
    			'csrf_protection' => false,
    	))
    	->getForm();
    	
    	$form->submit($this->formData);
    	$this->assertTrue($form->isValid());
    	
    }
    
    /**
     * 更新時のクーポン重複チェック
     */
    public function testInValidCouponCode_Exists_ForUpdate()
    {
    	$Coupon = $this->createCoupon(array('coupon_code'=>'utcoupon123'));
    	$Coupon2 = $this->createCoupon(array('coupon_code'=>'utcoupon1234'));
    	$this->app['orm.em']->persist($Coupon);
    	$this->app['orm.em']->persist($Coupon2);
    	$this->app['orm.em']->flush();
    
    	$this->formData['coupon_code']= 'utcoupon123';
    
    	$form = $this->app['form.factory']
    	->createBuilder('plg_simplecoupon_admin_coupon', $Coupon2, array(
    			'csrf_protection' => false,
    	))
    	->getForm();
    	 
    	$form->submit($this->formData);
    	$this->assertFalse($form->isValid());
    	 
    }
    
    /**
     * クーポンコードの長さ（正常）
     */
    public function testValidCouponCode_Length()
    {
    	$this->formData['coupon_code']= '12345678901234567890123456789012345678901234567890';
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * クーポンコードの長さエラー
     */
    public function testInValidCouponCode_Length()
    {
    	$this->formData['coupon_code']= '123456789012345678901234567890123456789012345678901';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポンコードのNot Null
     */
    public function testInValidCouponCode_Blank()
    {
    	$this->formData['coupon_code']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポン名の長さ（正常）
     */
    public function testValidCouponName_Length()
    {
    	$name = '';
    	for($i=1;$i<=200;$i++){
    		$name = $name . 'a';
    	}
    	$this->formData['coupon_name']= $name;
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * クーポン名の長さ（エラー）
     */
    public function testInValidCouponName_Length()
    {
    	$name = '';
    	for($i=1;$i<=201;$i++){
    		$name = $name . 'a';
    	}
    	$this->formData['coupon_name']= $name;
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポン名のNot Null
     */
    public function testInValidCouponName_Blank()
    {
    	$this->formData['coupon_name']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き種別のNot Null
     */
    public function testInValidDiscountType_Blank()
    {
    	$this->formData['discount_type']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き種別の不正値
     */
    public function testInValidDiscountType_Value()
    {
    	$this->formData['discount_type']= '100';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き対象種別のNot Null
     */
    public function testInValidDiscountTargetType_Blank()
    {
    	$this->formData['discount_target_type']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き対象種別の不正値
     */
    public function testInValidDiscountTargetType_Value()
    {
    	$this->formData['discount_target_type']= '100';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き種別=値引き金額の場合の、値引き値のnot null
     */
    public function testInValidDiscountValue_Blank()
    {
    	$this->formData['discount_type']= Coupon::DISCOUNT_TYPE_PRICE;
    	$this->formData['discount_value']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き種別=値引き率の場合の、値引き値のnot null
     */
    public function testInValidDiscountValue_Blank2()
    {
    	$this->formData['discount_type']= Coupon::DISCOUNT_TYPE_RATE;
    	$this->formData['discount_value']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き種別=値引き率の場合の、値引き値の値
     */
    public function testInValidDiscountValue_MaxValue()
    {
    	$this->formData['discount_type']= Coupon::DISCOUNT_TYPE_RATE;
    	$this->formData['discount_value']= 101;
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き種別=値引き率の場合の、値引き値の値
     */
    public function testInValidDiscountValue_MinValue()
    {
    	$this->formData['discount_type']= Coupon::DISCOUNT_TYPE_RATE;
    	$this->formData['discount_value']= -1;
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 値引き種別=値引き率の場合の、値引き値の値
     */
    public function testValidDiscountValue_MaxValue()
    {
    	$this->formData['discount_type']= Coupon::DISCOUNT_TYPE_RATE;
    	$this->formData['discount_value']= 100;
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 値引き種別=値引き率の場合の、値引き値の値
     */
    public function testValidDiscountValue_MinValue()
    {
    	$this->formData['discount_type']= Coupon::DISCOUNT_TYPE_RATE;
    	$this->formData['discount_value']= 1;
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 開始日、フォーマット不正
     */
    public function testInValidFromDate_Format()
    {
    	$this->formData['from_date']= '15/10/4';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 開始日Not Null
     */
    public function testInValidFromDate_Blank()
    {
    	$this->formData['from_date']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 終了日、フォーマット不正
     */
    public function testInValidToDate_Format()
    {
    	$this->formData['to_date']= '15/10/4';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 終了日Not Null
     */
    public function testInValidToDate_Blank()
    {
    	$this->formData['to_date']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 開始日、終了日（不正）
     */
    public function testInValidFromDateToDate()
    {
    	$this->formData['from_date']= '2016-11-11';
    	$this->formData['to_date']= '2016-11-10';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 併用利用フラグNot Null
     */
    public function testInValidCombinedUseFlg_Blank()
    {
    	$this->formData['combined_use_flg']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 併用利用フラグの不正値
     */
    public function testInValidCombinedUseFlg_Value()
    {
    	$this->formData['combined_use_flg']= '100';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * ゲスト利用フラグNot Null
     */
    public function testInValidGuestUseFlg_Blank()
    {
    	$this->formData['guest_use_flg']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * ゲスト利用フラグの不正値
     */
    public function testInValidGuestUseFlg_Value()
    {
    	$this->formData['guest_use_flg']= '100';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 一回利用制限フラグNot Null
     */
    public function testInValidOnetimeUseFlg_Blank()
    {
    	$this->formData['onetime_use_flg']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 一回利用制限利用フラグの不正値
     */
    public function testInValidOnetimeUseFlg_Value()
    {
    	$this->formData['onetime_use_flg']= '100';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 発行数Not Null
     */
    public function testInValidNumberOfIssued_Blank()
    {
    	$this->formData['number_of_issued']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 発行数の不正値
     */
    public function testInValidNumberOfIssued_Value()
    {
    	$this->formData['number_of_issued']= 'abc';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * ステータスNot Null
     */
    public function testInValidStatus_Blank()
    {
    	$this->formData['status']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * ステータスの不正値
     */
    public function testInValidStatus_Value()
    {
    	$this->formData['status']= '100';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 注文下限金額Not Null
     */
    public function testInValidBottomPrice_Blank()
    {
    	$this->formData['bottom_price']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 注文下限金額の不正値
     */
    public function testInValidBottomPrice_Value()
    {
    	$this->formData['bottom_price']= 'abc';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
}
