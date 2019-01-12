<?php

namespace Plugin\SimpleCoupon;


use Monolog\Logger;

use Eccube\Event\EventArgs;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DomCrawler\Crawler;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;
use Symfony\Component\Validator\Constraints as Assert;

class SimpleCoupon
{
	
	/** @var \Eccube\Application */
	private $app;
	
	/**
	 * @var string 非会員用セッションキー
	 */
	private $sessionKey = 'eccube.front.shopping.nonmember';
	
	public function __construct($app)
	{
		$this->app = $app;
	}
	
	
	/**
	 * Shopping Index のHTMLレンダリング後のイベント（HTMLの差し替えができる）
	 * @param FilterResponseEvent $event
	 */
	public function onEccubeEventRouteShoppingResponse(FilterResponseEvent $event){

		$request = $event->getRequest();
		$response = $event->getResponse();
		
		if($response->getStatusCode() != 200){
			return;
		}
	
		$content = $response->getContent();

		$builder = $this->app['form.factory']->createBuilder('plg_simplecoupon_use_coupon', new Coupon());
	
		$form = $builder->getForm();
	
		//注文情報を取得
		$preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
		
		$Order = $this->app['eccube.repository.order']->findOneBy(array(
				'pre_order_id' => $preOrderId,
				'OrderStatus' => $this->app['config']['order_processing']
		));

		//支払い方法が無い場合はクーポンを利用できないので、ボタンを表示しない。
		//支払い方法が無いとそもそも、注文も通らないので特殊なケースではあるが、エラー回避のため）
		if(is_null($Order->getPayment())){
			return;
		}
		
		$coupon_list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
	
		$snipet = $this->app->render(
				'SimpleCoupon/Resource/template/default/Shopping/snipet_coupon.twig',
				array(
						'form' => $form->createView(),
						'coupon_order_list' => $coupon_list
				)
				);
	
		// 書き換え処理ここから
		$crawler = new Crawler($content);
		
		$insert_position_id = $this->_getShoppingInsertPosition();
		$oldElement = $crawler->filter('#' . $insert_position_id);
		if(count($oldElement)==0){
			return;
		}
		$oldHtml = $oldElement->html();
	
		$newHtml = $snipet->getContent() . $oldHtml;
		$html = $crawler->html();

		$pos = strpos($content, "<head>");
		$h1="";
		$h2="";
		if($pos !== false && $pos>=0){
			$h1 = substr($content,0,$pos);
			$h2="</html>";
		}
		
		$html = str_replace($oldHtml, $newHtml, $html);
		// 書き換え処理ここまで
		$response->setContent($h1.$html.$h2);
		$event->setResponse($response);
	
	
	}
	
	
	
	/**
	 * 注文処理のShoppingControllerのconfirmの前に実行されるイベント
	 * 
	 */
	public function onEccubeEventRouteShoppingConfirmController(){
		$cartService = $this->app['eccube.service.cart'];
		$preOrderId = $cartService->getPreOrderId();
		if (is_null($preOrderId)) {
			return;
		}
		
		$Order = $this->app['eccube.repository.order']->findOneBy(array(
				'pre_order_id' => $preOrderId,
				'OrderStatus' => $this->app['config']['order_processing']
		));
		
		//クーポンを利用しているかチェックする。
		$coupon_list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
		if(count($coupon_list)==0){
			return;
		}
		
		if ($this->app->isGranted('ROLE_USER')) {
			$Customer = $this->app->user();
		} else {
			$Customer = $this->app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember');
		}
		
		//利用済みのクーポンかどうかチェックする
		foreach($coupon_list as $CouponOrder){
			$Coupon = $CouponOrder->getCoupon();
			$isUsed = $this->app['eccube.plugin.simplecoupon.service.coupon']->isUsedCoupon($Coupon, $Customer);
			if ($isUsed) {
				if($Coupon->getOnetimeUseFlg() == Coupon::ONETIME_USE_FLG_ONETIME){
					$this->app->addError($this->app->trans('front.plugin.simplecoupon.shopping.sameuser'), 'front.request');
					header("Location: ".$this->app->url('shopping'));
					exit;
				}
			}
		}
		
	}
	
	
	/**
	 * 管理画面の受注管理の注文情報編集のHTMLレンダリング後（HTMLの差し替えをする）
	 * @param FilterResponseEvent $event
	 */
	public function onEccubeEventRouteAdminOrderEditResponse(FilterResponseEvent $event){
		$request = $event->getRequest();
		$response = $event->getResponse();
		
		if($response->getStatusCode() != 200){
			return;
		}
		
		
		$orderId = $request->get('id');
		if (is_null($orderId)) {
			return;
		}
		/*
		$builder = $this->app['form.factory']->createBuilder('plg_simplecoupon_use_coupon', new Coupon());
		
		$form = $builder->getForm();
		
		*/
		
		//注文情報を取得
		//$preOrderId = $this->app['eccube.service.cart']->getPreOrderId();
		/*$Order = $this->app['eccube.repository.order']->findOneBy(array(
				'order_id' => $orderId,
		));*/
		$coupon_order_list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($orderId);
		
		$snipet = $this->app->render(
				'SimpleCoupon/Resource/template/admin/Order/snipet_coupon.twig',
				array(
						'coupon_order_list' => $coupon_order_list
				)
				);
		
		
		// 書き換え処理ここから
		$content = $response->getContent();
		$crawler = new Crawler($content);
		
		$insert_position_id = $this->_getAdminOrderEditInsertPosition();
		$oldElement = $crawler->filter('#' . $insert_position_id);
		if(count($oldElement)==0){
			return;
		}
		
		$oldHtml = $oldElement->html();
		
		$newHtml = $snipet->getContent() . $oldHtml;

		$html = $crawler->html();
		$pos = strpos($content, "<head>");
		$h1="";
		$h2="";
		if($pos !== false && $pos>=0){
			$h1 = substr($content,0,$pos);
			$h2="</html>";
		}
		$html = str_replace($oldHtml, $newHtml, $html);
		// 書き換え処理ここまで
		$response->setContent($h1.$html.$h2);
		$event->setResponse($response);
		
		
	}
	
	/**
	 * マイページ　注文履歴
	 * @param FilterResponseEvent $event
	 */
	public function onEccubeEventRouteMypageHistoryResponse(FilterResponseEvent $event){
		$request = $event->getRequest();
		$response = $event->getResponse();
	
	
		$orderId = $request->get('id');
		if (is_null($orderId)) {
			return;
		}

		$coupon_order_list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($orderId);
	
		$snipet = $this->app->render(
				'SimpleCoupon/Resource/template/default/Mypage/history_snipet_coupon.twig',
				array(
						'coupon_order_list' => $coupon_order_list
				)
				);
	
		// 書き換え処理ここから
		$content = $response->getContent();
		$crawler = new Crawler($content);
	
		$insert_position_id = $this->_getMypageHistoryInsertPosition();
		$oldElement = $crawler->filter('#' . $insert_position_id);
		if(count($oldElement)==0){
			return;
		}
	
		$oldHtml = $oldElement->html();
	
		$newHtml = $snipet->getContent() . $oldHtml;
		$html = $crawler->html();

		$pos = strpos($content, "<head>");
		$h1="";
		$h2="";
		if($pos !== false && $pos>=0){
			$h1 = substr($content,0,$pos);
			$h2="</html>";
		}
		
		$html = str_replace($oldHtml, $newHtml, $html);
		// 書き換え処理ここまで
		$response->setContent($h1.$html.$h2);
		$event->setResponse($response);
	
	
	}
	
	/**
	 * 注文確認画面、初期化イベント
	 * @param EventArgs $event
	 */
	public function onFrontShoppingIndexInitialize(EventArgs $event){
		$this->_modifyFormForCouponPayment($event);
		
	}
	
	/**
	 * 注文確定処理、初期化イベント
	 * @param EventArgs $event
	 */
	public function onFrontShoppingConfirmInitialize(EventArgs $event){
		$this->_modifyFormForCouponPayment($event);
	
	}
	
	/**
	 * 注文データ登録完了後のイベント
	 * クーポン利用のステータスを完了にする
	 * @param EventArgs $event
	 */
	public function onFrontShoppingConfirmProcessing(EventArgs $event)
	{
		$this->_confirmProcessing($event);
	}

	/**
	 * 決済プラグインからの決済完了通知をトリガーとするイベント
	 * クーポン利用のステータスを完了にする
	 * @param EventArgs $event
	 */
	public function onServiceShoppingNotifyComplete(EventArgs $event)
	{
		$this->_confirmProcessing($event);
	}
	
	/**
	 * 決済完了処理時の実処理
	 * クーポン利用のステータスを完了にする
	 * @param EventArgs $event
	 */
	private function _confirmProcessing(EventArgs $event)
	{
		$Order = $event->getArgument('Order');
	
		//この注文で利用登録したクーポン情報を取得する
		$coupon_list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
	
		$em = $this->app['orm.em'];
		$em->getConnection()->beginTransaction();
		try{
				
			foreach($coupon_list as $CouponOrder){
				$CouponOrder->setStatus(CouponOrder::STATUS_COMPLETE);
				$em->persist($CouponOrder);
			}
				
			$em->flush();
				
			//ゼロ円の場合は支払い方法をnullに
			$total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal() + $Order->getCharge() - $Order->getDiscount();
			if($total == 0 && count($coupon_list)>0){
				$payment = $this->app['eccube.plugin.simplecoupon.service.coupon']->getPaymentForCoupon();
				$Order->setPayment($payment);
				$Order->setPaymentMethod($payment->getMethod());
				$Order->setCharge(0);
				$em->persist($Order);
				$em->flush();
			}
				
			$em->getConnection()->commit();
		}catch(\Exception $ex){
			$em->getConnection()->rollback();
			throw $ex;
		}
	
	}
	
	private function _modifyFormForCouponPayment(EventArgs $event){
		$Order = $event->getArgument('Order');
		$builder = $event->getArgument('builder');
		
		$obj = $builder->get('payment');
		
		$coupon_list = $this->app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
		
		$total = $Order->getSubtotal() + $Order->getDeliveryFeeTotal() + $Order->getCharge() - $Order->getDiscount();
		if($total == 0 && count($coupon_list)>0){
			$payment_by_coupon = $this->app['eccube.plugin.simplecoupon.service.coupon']->getPaymentForCoupon();
				
			//支払い金額がゼロ円の場合、支払い方法を「クーポン利用のため無料」に強制設定
			$builder->remove('payment');
			$builder->add('payment', 'entity', array(
					'class' => 'Eccube\Entity\Payment',
					'property' => 'method',
					'choices' => array($payment_by_coupon),
					'data' => $payment_by_coupon,
					'expanded' => true,
					'constraints' => array(
							new Assert\NotBlank(),
					),
			));
			
		}else{
			//支払いが発生する場合は、支払い方法の選択肢の中から、支払い方法「クーポン利用のため無料」を削除する。
			$payment_builder = $builder->get('payment');
			$choices = $payment_builder->getOptions()['choices'];
			for($i=0;$i<count($choices);$i++){
				$choice = $choices[$i];
				$coupon_payment_id = $this->app['eccube.plugin.simplecoupon.service.coupon']->getPaymentIdForCoupon();
				if($choice->getId() == $coupon_payment_id){
					array_splice($choices, $i, $i);
					break;
				}
			}
			$builder->remove('payment');
			$builder->add('payment', 'entity', array(
					'class' => 'Eccube\Entity\Payment',
					'property' => 'method',
					'choices' => $choices,
					'data' => $payment_builder->getData(),
					'expanded' => true,
					'constraints' => array(
							new Assert\NotBlank(),
					),
			));
				
		}
	}


	/**
     * お届け先変更
	 *
	 */
	public function onFrontShoppingShippingChangeInitialize(EventArgs $event){
		$Order = $event->getArgument('Order');
		$builder = $event->getArgument('builder');

		if($Order->getPaymentTotal()==0){
			$this->_modifyFormForCouponPayment($event);
		}


	}
	
	
	/**
	 * 管理画面　配送方法編集画面の初期化イベント
	 * @param EventArgs $event
	 */
	public function onAdminSettingShopDeliveryEditInitialize(EventArgs $event){
		//支払い金額がゼロ円の場合、支払い方法を「クーポン利用のため無料」に強制設定
		$builder = $event->getArgument('builder');
		$builder->remove('payment');
		$builder->add('payments', 'entity', array(
                'label' => '支払方法',
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('p')
	                    ->andWhere('p.id != :coupon')
	                    ->setParameter('coupon', $this->app['eccube.plugin.simplecoupon.service.coupon']->getPaymentIdForCoupon())
                        ->orderBy('p.rank', 'DESC');
                },
                'mapped' => false,
         ));
		
	}
	
	
	/**
	 * 配送方法変更
	 * @param unknown $event
	 */
	public function onFrontShoppingDeliveryComplete($event){
		$this->_recalcDiscountPrice($event);
	}
	
	/**
	 * 支払い方法変更
	 * @param unknown $event
	 */
	public function onFrontShoppingPaymentComplete($event){
		$this->_recalcDiscountPrice($event);
	}
	
	/**
	 * 届け先変更
	 * @param unknown $event
	 */
	public function onFrontShoppingShippingComplete($event){
		$this->_recalcDiscountPrice($event);
	}
	
	/**
	 * 複数配送先設定
	 * @param unknown $event
	 */
	public function onFrontShoppingMultipleComplete($event){
		$this->_recalcDiscountPrice($event);
	}
	
	/**
	 * 届け先変更
	 * @param unknown $event
	 */
	public function onfrontShoppingShippingEditComplete($event){
		$Order = $this->app['eccube.service.shopping']->getOrder($this->app['config']['order_processing']);
		$this->_recalcDiscountPrice($event, $Order);
	}
	
	private function _recalcDiscountPrice($event, $Order=null){
		if(is_null($Order)){
			$this->app['eccube.plugin.simplecoupon.service.coupon']->recalcOrderPrice($event->getArgument('Order'));
		}else{
			$this->app['eccube.plugin.simplecoupon.service.coupon']->recalcOrderPrice($Order);
		}
		$this->app['orm.em']->flush();
	}
	
	
	private function _getShoppingInsertPosition(){
		if(isset($this->app['config']['SimpleCoupon']['const']['shopping_index_insert_position'])){
			$config_value = $this->app['config']['SimpleCoupon']['const']['shopping_index_insert_position'];
			if(strlen($config_value)>0){
				return $config_value;
			}
		}
		return 'payment_list';
	}
	
	private function _getMypageHistoryInsertPosition(){
		if(isset($this->app['config']['SimpleCoupon']['const']['mypage_history_insert_position'])){
			$config_value = $this->app['config']['SimpleCoupon']['const']['mypage_history_insert_position'];
			if(strlen($config_value)>0){
				return $config_value;
			}
		}
		return 'detail_box__payment_method';
	}
	
	private function _getAdminOrderEditInsertPosition(){
		if(isset($this->app['config']['SimpleCoupon']['const']['admin_order_edit_insert_position'])){
			$config_value = $this->app['config']['SimpleCoupon']['const']['admin_order_edit_insert_position'];
			if(strlen($config_value)>0){
				return $config_value;
			}
		}
		return 'payment_info_box__body';
	}
}