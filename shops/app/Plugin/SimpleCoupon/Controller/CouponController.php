<?php




namespace Plugin\SimpleCoupon\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Monolog\Logger;

use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;

class CouponController extends AbstractController
{
	
	/**
	 * クーポンの新規作成/編集確定
	 *
	 * @param Application $app
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function index(Application $app, Request $request)
	{

		if (!$app['eccube.service.cart']->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
		}
		
		$Coupon = null;
		
		$form = $app['form.factory']->createBuilder('plg_simplecoupon_use_coupon', $Coupon)->getForm();
	
		$form->handleRequest($request);
	
		//注文情報を取得する。
		$Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
		}
		if(is_null($Order->getPayment())){
            $app->addError('お支払い方法が選択されていないため、クーポンを利用できません');
            return $app->redirect($app->url('shopping_error'));
		}
		
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			//ユーザ情報
			$Customer = $app->user();
			
			$data = $form->getData();
			
			$Coupon = $app['eccube.plugin.simplecoupon.repository.coupon']->findOneBy(
					array(
							'couponCode' => $data['couponCode'],
							'status' => Coupon::STATUS_VALID,
							'delFlg' => Coupon::DEL_FLG_ACTIVE,
					));
			
			//Coupon 料金計算する
			$app['eccube.plugin.simplecoupon.service.coupon']->useCoupon($Order, $Coupon);
			
			return $app->redirect($app->url('plg_simplecoupon_front_coupon'));
		}else{
			
		}
		
		
		//この注文で登録済みのクーポン情報を取得する。
		$coupon_list = $app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
		
		return $app->render('SimpleCoupon/Resource/template/default/Simplecoupon/index.twig', array(
				'form' => $form->createView(),
				//'id' => $id,
				'coupon_list' => $coupon_list,
		));
	
	}
	
	
	/**
	 * クーポンの削除
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function delete(Application $app, Request $request, $id = null)
	{
		$CouponOrder = $app['eccube.plugin.simplecoupon.repository.coupon_order']->findOneBy(
				array('couponOrderId' => $id));
		
		if (!$CouponOrder) {
			return $app->redirect($app->url('plg_simplecoupon_front_coupon'));
		}
		
		$app['eccube.plugin.simplecoupon.service.coupon']->cancelCoupon($CouponOrder);
		
		return $app->redirect($app->url('plg_simplecoupon_front_coupon'));
	}
	
	
	
	
	
}
