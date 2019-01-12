<?php
/*
 */
namespace Plugin\SimpleCoupon\Tests\Form\Type\Admin;

use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\Test\TypeTestCase;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class CouponConditionTypeTest extends SimpleCouponTestBase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        	'coupon_code' => 'abcdefg',
    		'condition_type' => Coupon::CONDITION_TYPE_NONE,
    		'target_customer' => '',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('plg_simplecoupon_admin_coupon_condition', null, array(
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
     * クーポンコードのNot Null
     */
    public function testValidCouponCode_Blank()
    {
    	$this->formData['coupon_code']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 条件種別のNot Null
     */
    public function testInValidConditionType_Blank()
    {
    	$this->formData['condition_type']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 条件種別の不正値
     */
    public function testInValidConditionType_Value()
    {
    	$this->formData['condition_type']= '100';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 条件種別=会員指定の場合の、会員IDのnot null
     */
    public function testInValidTargetCustomer_Blank()
    {
    	$this->formData['condition_type']= Coupon::CONDITION_TYPE_CUSTOMER_ID;
    	$this->formData['target_customer']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }

    /**
     * 条件種別=商品指定の場合の、商品IDのnot null
     */
    public function testInValidTargetProduct_Blank()
    {
        $this->formData['condition_type']= Coupon::CONDITION_TYPE_PRODUCT;
        $this->formData['target_product']= '';
    
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * 条件種別=カテゴリ指定の場合の、カテゴリIDのnot null
     */
    public function testInValidTargetCategory_Blank()
    {
        $this->formData['condition_type']= Coupon::CONDITION_TYPE_CATEGORY;
        $this->formData['target_category']= '';
    
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
    
}
