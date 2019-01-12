<?php


namespace Plugin\SimpleCoupon\Service;

use Eccube\Application;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;
use Monolog\Logger;
use Eccube\Util\Str;

class SimpleCouponService
{
	/** @var \Eccube\Application */
	public $app;

	/**
	 * コンストラクタ
	 *
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * クーポン利用登録をする
	 * @param Order $Order
	 * @param SimpleCoupon $Coupon
	 * @throws Exception
	 */
	public function useCoupon(Order $Order, Coupon $Coupon){
		
		$em = $this->app['orm.em'];
		$em->getConnection()->beginTransaction();
		
		try{
			
			//ユーザ情報
			if ($this->app->isGranted('ROLE_USER')) {
				$Customer = $this->app->user();
			}else{
				$Customer = $this->app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember');
			}
			
			//クーポン登録
			$CouponOrder = new CouponOrder();
			$CouponOrder->setCoupon($Coupon);
			$CouponOrder->setOrderId($Order->getId());
			$CouponOrder->setCustomerId($Customer->getId());
			$CouponOrder->setEmail($Customer->getEmail());
			$CouponOrder->setStatus(CouponOrder::STATUS_PROCESSING);
			$CouponOrder->setDiscountPrice(0);
			$CouponOrder->setOrder($Order);
			$em->persist($CouponOrder);
			$em->flush();
			
			//値引き金額、課金額を再計算
			$this->recalcOrderPrice($Order);
			
			$em->flush();
			$em->getConnection()->commit();
			
		}catch(\Exception $e){
			$em->getConnection()->rollback();
			throw $e;
		}
	}
	
	public function cancelCoupon(CouponOrder $CouponOrder){
		
		$Order = $this->app['eccube.repository.order']->findOneBy(array(
				'id' => $CouponOrder->getOrderId(),
				'OrderStatus' => $this->app['config']['order_processing']
		));
		$em = $this->app['orm.em'];
		$em->getConnection()->beginTransaction();
		
		try{
			$deleted_coupo_price = $CouponOrder->getDiscountPrice();
			$this->app['orm.em']->remove($CouponOrder);
			$this->app['orm.em']->flush();
			
			//値引き金額、課金額の再計算
			$this->recalcOrderPrice($Order, $deleted_coupo_price);
			
			$em->flush();
			$em->getConnection()->commit();
		}catch(\Exception $e){
			
			$em->getConnection()->rollback();
			throw $e;
		}
		
	}
	
	public function recalcOrderPrice(Order $Order, $deleted_coupon_price=0){
		
		$coupon_list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
		//現在の値引き額を取得する。
		$current_payment = $Order->getPaymentTotal();
		$current_order_discount = $Order->getDiscount();
		$current_coupon_discount = $deleted_coupon_price;
		foreach($coupon_list as $CouponOrder){
			$coupon_discount = $CouponOrder->getDiscountPrice();
			$current_coupon_discount += $coupon_discount;
		}
		//当プラグイン以外の値引き分を計算。
		$current_other_discount = $current_order_discount - $current_coupon_discount;

		//クーポンの利用総額をまず取得する。
		$coupon_total = 0;
		foreach($coupon_list as $CouponOrder){
			$discount = $this->getDiscountPrice($Order, $CouponOrder->getCoupon());
			$coupon_total += $discount;
		}

		
		$minus = false;
		$order_total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal() + $Order->getCharge();
		$total = 0;
		$total_discount = $current_other_discount;//当プラグイン以外の値引き分はそのまま保持する。
		$index = 0;

		$discount_type = Coupon::DISCOUNT_TYPE_PRICE;
		//当プラグイン以外の値引き分はそのまま保持するため、値引き対象金額からその分を差し引いた金額からスタートする。
		$total = $total - $current_other_discount;
		foreach($coupon_list as $CouponOrder){
			if($index == 0){
				$Coupon = $CouponOrder->getCoupon();
				$discount_type = $Coupon->getDiscountType();
				if($Coupon->getDiscountTargetType() == Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL){
					//商品小計の場合。クーポンの値引きが最大で商品小計。他の値引きを合わせれば、商品小径以上になる場合も考慮。 =================
					if($Order->getDeliveryFeeTotal() >= $current_other_discount){
						//他の値引きが送料より小さい場合は、値引き対象=商品小径
						$total = $Order->getSubtotal();
					}else{
						//他の値引きが送料より大きい場合は、値引き対象 ＝ 商品小計＋送料 - 他の値引き
						$total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal() - $current_other_discount;
					}
				}
				elseif($Coupon->getDiscountTargetType() == Coupon::DISCOUNT_TARGET_TYPE_DELIVERY_FEE){
					//送料のみ値引きの場合。クーポンの値引きの最大が送料。=============================================================
					if($Order->getSubtotal() >= $current_other_discount){
						//他の値引きが商品小径より小さい場合は、値引き対象=送料
						$total = $Order->getDeliveryFeeTotal();
					}else{
						//他の値引きが商品小計より大きい場合は、値引き対象 ＝ 商品小計＋送料 - 他の値引き
						$total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal() - $current_other_discount;
					}
				}
				else{
					//商品小計＋配送料小計（決済手数料は含む必要なし） ===============================================================
					$total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal() - $current_other_discount;	
				}
				
			}
			$discount = $this->getDiscountPrice($Order, $CouponOrder->getCoupon());
			
			if($total >= $discount){
				$CouponOrder->setDiscountPrice($discount);
				$total_discount += $discount;
				$total = $total - $discount;
			}else{
				$CouponOrder->setDiscountPrice($total);
				$total_discount += $total;
				$total = 0;
				$minus = true;
			}
			$this->app['orm.em']->persist($CouponOrder);
			$index ++;
		}

		if(($Order->getSubtotal() + $Order->getDeliveryFeeTotal() - $total_discount) == 0){
			//0円の場合は支払い方法を変更する。
			$Coupon_Payment = $this->getPaymentForCoupon();
			$Order->setPayment($Coupon_Payment);
			$Order->setPaymentMethod($Coupon_Payment->getMethod());
			$Order->setCharge(0);
			$Order->setTotal(0);
			$Order->setPaymentTotal(0);
			$Order->setDiscount($total_discount);
		}else{

			
			if($Order->getPayment()->getId() == $this->getPaymentIdForCoupon()){
				//変更前が0円だった場合、決済手数料を加算
				$Payment = $this->getDefaultPayment($Order);
				if(is_null($Payment)){
					$Order->setPayment(null);
					$Order->setPaymentMethod('');
					$Order->setCharge(0);
				}else{
					$Order->setPayment($Payment);
					$Order->setPaymentMethod($Payment->getMethod());
					$Order->setCharge($Payment->getCharge());
				}
			}
			if($Order->getCharge()>0 && $discount_type == Coupon::DISCOUNT_TYPE_RATE && $Coupon->getDiscountTargetType() == Coupon::DISCOUNT_TARGET_TYPE_TOTAL){
				//割引率の場合で、支払い金額が残っている場合は、手数料も割引計算する。
				foreach($coupon_list as $CouponOrder){
					$Coupon = $CouponOrder->getCoupon();
					if($Coupon->getDiscountType() == Coupon::DISCOUNT_TYPE_RATE){
						$discount_rate_price = ceil($Order->getCharge() * $Coupon->getDiscountValue() / 100);

						$total_discount += $discount_rate_price;
						$current_coupon_discount = $CouponOrder->getDiscountPrice();
						$CouponOrder->setDiscountPrice($current_coupon_discount + $discount_rate_price);
						$this->app['orm.em']->persist($CouponOrder);
					}
				}
			}

			$order_total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal() + $Order->getCharge();
			$Order->setTotal($order_total - $total_discount);
			$Order->setPaymentTotal($order_total - $total_discount);
			$Order->setDiscount($total_discount);
		}
		
		$this->app['orm.em']->persist($Order);
		return $minus;
	}
	
	
	/**
	 * 利用済みのクーポンかどうかチェックする
	 * @param Coupon $coupon
	 * @param Customer $Customer
	 * @return boolean
	 */
	public function isUsedCoupon(Coupon $coupon, Customer $Customer){
		if ($this->app->isGranted('ROLE_USER')) {
			$couponOthers = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->findBy(
				array('Coupon' => $coupon,
						'customerId' => $Customer->getId(),
						//'status' => CouponOrder::STATUS_COMPLETE,
				)
			);
		}else{
			$couponOthers = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->findBy(
					array('Coupon' => $coupon,
							'email' => $Customer->getEmail(),
							//'status' => CouponOrder::STATUS_COMPLETE,
					)
			);
		}
		if(count($couponOthers)>0){
			//注文のステータスと削除フラグを確認する
			$isOrderd = false;
			foreach($couponOthers as $CouponOrder){
				$Order = $this->app['eccube.repository.order']->findOneBy(
						array(
								'id' => $CouponOrder->getOrderId()
						)
						);
				if(is_null($Order)){
					//注文が削除されている場合、上記findOneByで$Orderがnullになる。
					//$CouponOrder->getOrder()とすると、$Orderはnullにはならないが、$Order->get***を実行するとエラーになる。
				}else{
					if($Order->getDelFlg() == \Eccube\Common\Constant::DISABLED
							&& $Order->getOrderStatus()->getId() != $this->app['config']['order_cancel']
							&& $Order->getOrderStatus()->getId() != $this->app['config']['order_processing']
							){
								$isOrderd = true;
					}
				}
				
			}
			return $isOrderd;
		}
		
		return false;
	}
	
	
	/**
	 * クーポン利用により支払いない場合に適用するPaymentレコードのIdを取得する。
	 */
	public function getPaymentIdForCoupon(){
		return $this->app['eccube.plugin.simplecoupon.repository.config']->getCouponPaymentId();
		//return $this->app['config']['SimpleCoupon']['const']['payment_method_id'];
	}
	
	public function getPaymentForCoupon(){
		$payment = $this->app['eccube.repository.payment']->find($this->getPaymentIdForCoupon());
		return $payment;
	}
	
	
	/**
	 * ランダムな文字列を生成する。
	 */
	public function createCouponCode($length=12){
		return Str::random($length);
	}
	
	
	private function getDiscountPrice(Order $Order, Coupon $Coupon){
		if($Coupon->getDiscountType() == Coupon::DISCOUNT_TYPE_PRICE){
			return $Coupon->getDiscountValue();
		}else{
			if($Coupon->getDiscountTargetType() == Coupon::DISCOUNT_TARGET_TYPE_SUBTOTAL){
				$discount =  ceil($Order->getSubtotal() * $Coupon->getDiscountValue() / 100);
			}
			elseif($Coupon->getDiscountTargetType() == Coupon::DISCOUNT_TARGET_TYPE_DELIVERY_FEE){
				$discount =  ceil($Order->getDeliveryFeeTotal() * $Coupon->getDiscountValue() / 100);
			}else{
				$total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal();// + $Order->getCharge();
				$discount =  ceil($total * $Coupon->getDiscountValue() / 100);
			}
	        return $discount;
		}
	}

	//支払い方法のデフォルト値を取得
	private function getDefaultPayment($Order){
		$Shopping_Service = $this->app['eccube.service.shopping'];

		// 配送業者情報を取得
		$deliveries = $Shopping_Service->getDeliveriesCart();
		// 初期選択の支払い方法をセット
        $payments = $this->app['eccube.repository.payment']->findAllowedPayments($deliveries);
        $payments = $Shopping_Service->getPayments($payments, $Order->getSubtotal());
        if (count($payments) > 0) {
            return $payments[0];
        } else {
            return null;
        }
	}
}