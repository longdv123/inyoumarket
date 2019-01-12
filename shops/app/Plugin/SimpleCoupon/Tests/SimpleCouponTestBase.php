<?php

namespace Plugin\SimpleCoupon\Tests;

use Eccube\Common\Constant;
use Eccube\Tests\EccubeTestCase;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;
use Plugin\SimpleCoupon\Entity\ConditionCustomer;
use Eccube\Entity\Order;
use Eccube\Entity\Customer;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Form\Test\TypeTestCase;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Eccube\Tests\Mock\CsrfTokenMock;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpKernel\Client;


/**
 * Class SimpleCouponTestBase
 *
 * @package Plugin\SimpleCoupon\Tests
 */
class SimpleCouponTestBase extends EccubeTestCase
{

	protected $Customer;
	protected $Coupon;
	
	public function setUp() {
		parent::setUp();
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
		
		return $Coupon;
		
	}
	
	protected function createCouponOrder(Coupon $Coupon, Order $Order, Customer $Customer=null, $param = array()){
		
		$date1 = new \DateTime();
		
		$CouponOrder = new CouponOrder();
		
		$CouponOrder->setCoupon($Coupon);
		$CouponOrder->setOrderId($Order->getId());
		$CouponOrder->setCustomerId(is_null($Customer)?null:$Customer->getId());
		$CouponOrder->setEmail(is_null($Customer)?null:$Customer->getEmail());
		$CouponOrder->setStatus(isset($param['status']) ? $param['status'] : CouponOrder::STATUS_COMPLETE);
		$CouponOrder->setDiscountPrice(isset($param['discount_price']) ? $param['discount_price'] : 100);
		$CouponOrder->setCreateDate(isset($param['create_date']) ? $param['create_date'] : $date1);
		$CouponOrder->setOrder($Order);
		
		$this->app['orm.em']->persist($CouponOrder);
		$this->app['orm.em']->flush($CouponOrder);
		
		return $CouponOrder;
	}
	
	protected function createConditionCustomer(Coupon $Coupon, Customer $Customer){
	
		$Condition = new ConditionCustomer();
	
		$Condition->setCouponId($Coupon->getCouponId());
		$Condition->setCustomer($Customer);
		
		$this->app['orm.em']->persist($Condition);
		$this->app['orm.em']->flush($Condition);
	
		return $Condition;
	}
	
	protected function getCoupon($param=array()){
		$Coupon = $this->createCoupon($param);
		$this->app['orm.em']->persist($Coupon);
		$this->app['orm.em']->flush($Coupon);
		return $Coupon;
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	protected function logIn($user = null)
	{
		$firewall = 'customer';
	
		if (!is_object($user)) {
			$user = $this->createCustomer();
		}
		$token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_USER'));
	
		$this->app['security.token_storage']->setToken($token);
		$this->app['session']->set('_security_' . $firewall, serialize($token));
		$this->app['session']->save();
	
		$cookie = new Cookie($this->app['session']->getName(), $this->app['session']->getId());
		$this->client->getCookieJar()->set($cookie);
		return $user;
	}
	
	protected function createPreOrder($client, $Customer){
		// カート画面
		$this->scenarioCartIn($client);
	
		// 確認画面
		$this->scenarioConfirm($client);
	
		// 注文情報を取得
		/*$pre_order_id = $this->app['eccube.service.cart']->getPreOrderId();
		$Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id'=>$pre_order_id));
		return $Order;
		*/
		return $this->getPreOrder();
	}
	
	protected function getPreOrder(){
		$pre_order_id = $this->app['eccube.service.cart']->getPreOrderId();
		$Order = $this->app['eccube.repository.order']->findOneBy(array('pre_order_id'=>$pre_order_id));
		return $Order;
	}
	
	protected function scenarioCartIn($client)
	{
		$crawler = $client->request('POST', '/cart/add', array('product_class_id' => 1));
		$this->app['eccube.service.cart']->lock();
		return $crawler;
	}
	
	protected function scenarioConfirm($client)
	{
		$crawler = $client->request('GET', $this->app->path('shopping'));
		return $crawler;
	}
	
	public function createNonmemberFormData()
	{
		$faker = $this->getFaker();
		$tel = explode('-', $faker->phoneNumber);
	
		$email = $faker->safeEmail;
	
		$form = array(
				'name' => array(
						'name01' => $faker->lastName,
						'name02' => $faker->firstName,
				),
				'kana' => array(
						'kana01' => $faker->lastKanaName ,
						'kana02' => $faker->firstKanaName,
				),
				'company_name' => $faker->company,
				'zip' => array(
						'zip01' => $faker->postcode1(),
						'zip02' => $faker->postcode2(),
				),
				'address' => array(
						'pref' => '5',
						'addr01' => $faker->city,
						'addr02' => $faker->streetAddress,
				),
				'tel' => array(
						'tel01' => $tel[0],
						'tel02' => $tel[1],
						'tel03' => $tel[2],
				),
				'email' => array(
						'first' => $email,
						'second' => $email,
				),
				'_token' => 'dummy'
		);
		return $form;
	}
	
	protected function scenarioInputNonmember($client, $formData)
	{
		$crawler = $client->request(
				'POST',
				$this->app->path('shopping_nonmember'),
				array('nonmember' => $formData)
				);
		$this->app['eccube.service.cart']->lock();
		return $crawler;
	}
	

}