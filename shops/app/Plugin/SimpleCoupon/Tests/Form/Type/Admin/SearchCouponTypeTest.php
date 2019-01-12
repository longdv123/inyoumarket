<?php
/*
 */
namespace Plugin\SimpleCoupon\Tests\Form\Type\Admin;

use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Tests\SimpleCouponTestBase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\Test\TypeTestCase;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class SearchCouponTypeTest extends SimpleCouponTestBase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        	'multi' => '1',
    		'status' => Coupon::STATUS_VALID,
    		'date_start' => '2016-10-01',
    		'date_end' => '2017-10-01',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('plg_simplecoupon_admin_search_coupon', null, array(
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
     * クーポンコードの長さ（正常）
     */
    public function testValidMulti_Length()
    {
    	$val = '';
    	$length = $this->app['config']['stext_len'];
    	for($i=1;$i<=$length;$i++){
    		$val = $val . 'a';
    	}
    	$this->formData['multi']= $val;
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * クーポンコードの長さエラー
     */
    public function testInValidMulti_Length()
    {
    	$val = '';
    	$length = $this->app['config']['stext_len'] + 1;
    	for($i=1;$i<=$length;$i++){
    		$val = $val . 'a';
    	}
    	$this->formData['multi']= $val;
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 開始日、フォーマット不正
     */
    public function testInValidDateStart_Format()
    {
    	$this->formData['date_start']= '15/10/4';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 開始日 Null許可
     */
    public function testValidDateStart_Blank()
    {
    	$this->formData['date_start']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
    
    /**
     * 終了日、フォーマット不正
     */
    public function testInValidDateEnd_Format()
    {
    	$this->formData['date_end']= '15/10/4';
    
    	$this->form->submit($this->formData);
    	$this->assertFalse($this->form->isValid());
    }
    
    /**
     * 終了日 Null許可
     */
    public function testValidDateEnd_Blank()
    {
    	$this->formData['date_end']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
    }
        
    /**
     * ステータス Null許可
     */
    public function testValidStatus_Blank()
    {
    	$this->formData['status']= '';
    
    	$this->form->submit($this->formData);
    	$this->assertTrue($this->form->isValid());
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
    
}
