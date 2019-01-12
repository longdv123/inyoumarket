<?php
/*
 */
namespace Plugin\SimpleCoupon\Tests\Form\Type;

use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;
use Plugin\SimpleCoupon\Entity\ConditionCustomer;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\Test\TypeTestCase;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Eccube\Tests\Mock\CsrfTokenMock;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpKernel\Client;

class UseCouponTypeTest extends SimpleCouponTestBase
{
	protected $client;
	
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        	'coupon_code' => 'abcdefg1234567890',
    );

    public function setUp()
    {
        parent::setUp();
        
        $this->client = $this->createClient();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('plg_simplecoupon_use_coupon', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
            
        
    }

    /**
     * 正常系
     */
    public function testValidData()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	
    	//有効なクーポンを作成
    	$Coupon = $this->getCoupon();
    	$this->formData['coupon_code'] = $Coupon->getCouponCode();
    	
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 存在しないクーポンコード
     */
    public function testInValidData()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	 
    	$this->form->submit(array('coupon_code'=>'a'));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポンコードNot Null
     */
    public function testInValidCouponCode_Blank()
    {
    	$Customer = $this->logIn();
    
    	$this->form->submit(array('coupon_code'=>''));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポンコード ステータス無効
     */
    public function testInValidStatus_InValid()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    
    	$Coupon = $this->getCoupon(array('status' => Coupon::STATUS_INVALID));
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポンコード 削除フラグOn
     */
    public function testInValidDelFlg_On()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	
    	$param = array(
    			'del_flg' => Coupon::DEL_FLG_DELETED,
    	);
    	
    	$Coupon = $this->getCoupon($param);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
	
    /**
     * クーポンコード 開始日が未来日
     */
    public function testInValidFromDate_Future()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	
    	$param = array(
    			'from_date' => new \DateTime('+1 days'),
    			'to_date'=>  new \DateTime('+10 days'),
    	);
    	 
    	$Coupon = $this->getCoupon($param);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポンコード 終了日が過去日
     */
    public function testInValidToDate_Past()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    
    	$param = array(
    			'from_date' => new \DateTime('-10 days'),
    			'to_date'=>  new \DateTime('-1 days'),
    	);
    
    	$Coupon = $this->getCoupon($param);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * クーポンコード 開始日が今日
     */
    public function testValidFromDate_Today()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    
    	$param = array(
    			'from_date' => new \DateTime((new \DateTime())->format('Y-m-d')),
    			'to_date'=>  new \DateTime('+1 days'),
    	);
    
    	$Coupon = $this->getCoupon($param);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * クーポンコード 終了日が今日
     */
    public function testValidToDate_Today()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    
    	$param = array(
    			'from_date' => new \DateTime('-1 days'),
    			'to_date'=>  new \DateTime((new \DateTime())->format('Y-m-d')),
    	);
    
    	$Coupon = $this->getCoupon($param);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    //ゲスト購入
    
    /**
     * 発行数上限
     */
    public function testValidNumberOfIssued_Max()
    {
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    
    	$param = array(
    			'number_of_issued' => 1,
    	);
    
    	$Coupon = $this->getCoupon($param);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 発行数上限超え
     */
    public function testInValidNumberOfIssued_Max()
    {
    	$param = array(
    			'number_of_issued' => 1,
    	);
    	$Coupon = $this->getCoupon($param);
    	
    	//クーポン利用済みデータを無理やり作る
    	$Customer0 = $this->createCustomer();
    	$Order0 = $this->createOrder($Customer0);
    	$CouponOrder = new CouponOrder();
    	$CouponOrder->setCoupon($Coupon);
    	$CouponOrder->setCustomerId($Customer0->getId());
    	$CouponOrder->setOrderId($Order0->getId());
    	$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
    	$CouponOrder->setDiscountPrice($Coupon->getDiscountValue());
    	$CouponOrder->setCreateDate(new \DateTime());
    	$CouponOrder->setOrder($Order0);
    	$this->app['orm.em']->persist($CouponOrder);
    	$this->app['orm.em']->flush();
    	
    	//上限に達している状態で、新たにクーポン利用 => InValidになる。
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 発行数上限超え
     */
    public function testValidNumberOfIssued_Max2()
    {
    	$param = array(
    			'number_of_issued' => 1,
    	);
    	$Coupon = $this->getCoupon($param);
    	 
    	//クーポン利用の仕掛りデータを無理やり作る（上限チェックにはカウントされないはずのデータ）
    	$Customer0 = $this->createCustomer();
    	$Order0 = $this->createOrder($Customer0);
    	$CouponOrder = new CouponOrder();
    	$CouponOrder->setCoupon($Coupon);
    	$CouponOrder->setCustomerId($Customer0->getId());
    	$CouponOrder->setOrderId($Order0->getId());
    	$CouponOrder->setStatus(CouponOrder::STATUS_PROCESSING);
    	$CouponOrder->setDiscountPrice($Coupon->getDiscountValue());
    	$CouponOrder->setCreateDate(new \DateTime());
    	$this->app['orm.em']->persist($CouponOrder);
    	$this->app['orm.em']->flush();
    	 
    	///新たにクーポン利用 => 上記仕掛りデータはカウントしないので、Validになる。
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    
    /**
     * 会員条件指定
     */
    public function testValidCustomerCondition()
    {
    	//クーポンを作成
    	$param = array(
    		'condition_type' => Coupon::CONDITION_TYPE_CUSTOMER_ID,	
    	);
    	$Coupon = $this->getCoupon($param);
    		
    	$Customer = $this->logIn();
    	
    	//会員条件を設定
    	$Condition = new ConditionCustomer();
    	$Condition->setCouponId($Coupon->getCouponId());
    	$Condition->setCustomer($Customer);
    	$this->app['orm.em']->persist($Condition);
    	$this->app['orm.em']->flush($Condition);
    	
    	
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 会員条件 合致しないユーザ
     */
    public function testInValidCustomerCondition()
    {
    	//クーポンを作成
    	$param = array(
    			'condition_type' => Coupon::CONDITION_TYPE_CUSTOMER_ID,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    	 
    	//会員条件を設定
    	$Customer0 = $this->createCustomer();
    	$Condition = new ConditionCustomer();
    	$Condition->setCouponId($Coupon->getCouponId());
    	$Condition->setCustomer($Customer0);
    	$this->app['orm.em']->persist($Condition);
    	$this->app['orm.em']->flush($Condition);
    	 
    	
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    
    /**
     * 同じ注文で、同じクーポンを複数登録
     */
    public function testInValidCoupon_Used()
    {
    	//クーポンを作成
    	$Coupon = $this->getCoupon();
    
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//１つ目登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
 
    	//同じクーポンで２度目。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 他の注文で利用済みのクーポンを、もう一度使う。
     */
    public function testInValidCoupon_UsedAnotherOrder()
    {
    	//クーポンを作成
    	$Coupon = $this->getCoupon();
    
    	$Customer = $this->logIn();
    	
    	//１つ目登録。
    	$Order0 = $this->createOrder($Customer);
    	$CouponOrder = new CouponOrder();
    	$CouponOrder->setCoupon($Coupon);
    	$CouponOrder->setCustomerId($Customer->getId());
    	$CouponOrder->setOrderId($Order0->getId());
    	$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
    	$CouponOrder->setDiscountPrice($Coupon->getDiscountValue());
    	$CouponOrder->setCreateDate(new \DateTime());
    	$this->app['orm.em']->persist($CouponOrder);
    	$this->app['orm.em']->flush();
    	
    	//同じクーポンで別注文。
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 他の注文で利用済みのクーポン（複数回利用可能）を、もう一度使う。
     */
    public function testValidCoupon_UsedAnotherOrder()
    {
    	//クーポンを作成
    	$Coupon = $this->getCoupon(array('onetime_use_flg'=>0));
    	$Customer = $this->logIn();
    	 
    	//１つ目登録。
    	$Order0 = $this->createOrder($Customer);
    	$CouponOrder = new CouponOrder();
    	$CouponOrder->setCoupon($Coupon);
    	$CouponOrder->setCustomerId($Customer->getId());
    	$CouponOrder->setOrderId($Order0->getId());
    	$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
    	$CouponOrder->setDiscountPrice($Coupon->getDiscountValue());
    	$CouponOrder->setCreateDate(new \DateTime());
    	$this->app['orm.em']->persist($CouponOrder);
    	$this->app['orm.em']->flush();
    	 
    	//同じクーポンで別注文。
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    
    /**
     * 併用利用フラグ OK
     */
    public function testValidCombinedUseFlg()
    {
    	//クーポンを作成
    	$param0 = array(
    		'coupon_code' => 'abc123',
    		'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    	);
    	$Coupon0 = $this->getCoupon($param0);
    	$param = array(
    			'coupon_code' => 'xyz123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    	 
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//１つ目登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon0);
 
    	//違うクーポンを追加登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 併用利用フラグ 不可の後で可
     */
    public function testInValidCombinedUseFlg_Deny1()
    {
    	//クーポンを作成
    	$param0 = array(
    			'coupon_code' => 'abc123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_DENY,
    	);
    	$Coupon0 = $this->getCoupon($param0);
    	$param = array(
    			'coupon_code' => 'xyz123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//併用利用NGのクーポンを登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon0);
    
    	//併用利用OKのクーポンを追加登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    
    /**
     * 併用利用フラグ 可の後で不可
     */
    public function testInValidCombinedUseFlg_Deny2()
    {
    	//クーポンを作成
    	$param0 = array(
    			'coupon_code' => 'abc123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    	);
    	$Coupon0 = $this->getCoupon($param0);
    	$param = array(
    			'coupon_code' => 'xyz123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_DENY,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//併用利用OKのクーポンを登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon0);
    
    	//併用利用NGのクーポンを追加登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 併用利用フラグ 不可の後で不可
     */
    public function testInValidCombinedUseFlg_Deny3()
    {
    	//クーポンを作成
    	$param0 = array(
    			'coupon_code' => 'abc123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_DENY,
    	);
    	$Coupon0 = $this->getCoupon($param0);
    	$param = array(
    			'coupon_code' => 'xyz123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_DENY,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//併用利用NGのクーポンを登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon0);
    
    	//併用利用NGのクーポンを追加登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    
    public function testValidGuestUseFlg()
    {
    	//クーポンを作成
    	$param = array(
    			'guest_use_flg' => Coupon::GUEST_USE_FLG_ALLOW,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	//非会員の注文情報作成
    	$faker = $this->getFaker();
    	$client = $this->createClient();
    	$this->scenarioCartIn($client);
    	$formData = $this->createNonmemberFormData();
    	$this->scenarioInputNonmember($client, $formData);
    	$this->scenarioConfirm($client);
    
    	$Order = $this->getPreOrder();
    	
    	//クーポンを登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    public function testInValidGuestUseFlg_Deny()
    {
    	//クーポンを作成
    	$param = array(
    			'guest_use_flg' => Coupon::GUEST_USE_FLG_DENY,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	//非会員の注文情報作成
    	$faker = $this->getFaker();
    	$client = $this->createClient();
    	$this->scenarioCartIn($client);
    	$formData = $this->createNonmemberFormData();
    	$this->scenarioInputNonmember($client, $formData);
    	$this->scenarioConfirm($client);
    
    	$Order = $this->getPreOrder();
    	 
    	//クーポンを登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    
    public function testValidBottomPrice()
    {
    	//クーポンを作成
    	$param = array(
    			'bottom_price' => 100,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	//注文情報作成
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$Order = $this->getPreOrder();
    	 
    	//クーポンを登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    
    public function testInValidBottomPrice()
    {
    	//クーポンを作成
    	$param = array(
    			'bottom_price' => 1000000,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	//注文情報作成
    	$Customer = $this->logIn();
    	$Order = $this->createPreOrder($this->client, $Customer);
    	$Order = $this->getPreOrder();
    
    	//クーポンを登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    
    /**
     * 併用利用フラグ OK　値引き対象が商品小計
     */
    public function testValidCombinedUseFlg_DiscountTargetTypeSubtotal()
    {
    	//クーポンを作成
    	$param0 = array(
    			'coupon_code' => 'abc123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL,
    	);
    	$Coupon0 = $this->getCoupon($param0);
    	$param = array(
    			'coupon_code' => 'xyz123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//１つ目登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon0);
    
    	//違うクーポンを追加登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 併用利用フラグ OK、　値引き対象が異なる
     */
    public function testInValidCombinedUseFlg_DiscountTargetType_1()
    {
    	//クーポンを作成
    	$param0 = array(
    			'coupon_code' => 'abc123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL,
    	);
    	$Coupon0 = $this->getCoupon($param0);
    	$param = array(
    			'coupon_code' => 'xyz123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_TOTAL,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//１つ目登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon0);
    
    	//違うクーポンを追加登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 併用利用フラグ OK、　値引き対象が異なる
     */
    public function testInValidCombinedUseFlg_DiscountTargetType_2()
    {
    	//クーポンを作成
    	$param0 = array(
    			'coupon_code' => 'abc123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_TOTAL,
    	);
    	$Coupon0 = $this->getCoupon($param0);
    	$param = array(
    			'coupon_code' => 'xyz123',
    			'combined_use_flg' => Coupon::COMBINED_USE_FLG_ALLOW,
    			'discount_target_type' => Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL,
    	);
    	$Coupon = $this->getCoupon($param);
    
    	$Customer = $this->logIn();
    
    	$Order = $this->createPreOrder($this->client, $Customer);
    	//１つ目登録。
    	$this->app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon0);
    
    	//違うクーポンを追加登録。
    	$this->form->submit(array('coupon_code'=>$Coupon->getCouponCode()));
    	$this->assertFalse($this->form->isValid());
    }
}
