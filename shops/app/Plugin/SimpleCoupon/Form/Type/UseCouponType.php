<?php

namespace Plugin\SimpleCoupon\Form\Type;

use Carbon\Carbon;
use Plugin\Coupon\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Monolog\Logger;
use Plugin\SimpleCoupon\Entity\Coupon;
use Plugin\SimpleCoupon\Entity\CouponOrder;

class UseCouponType extends AbstractType
{

    private $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Build config type form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;
        
        $builder
            ->add('coupon_code', 'text', array(
                'label' => 'クーポンコード',
                'required' => false,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Regex(array(
                            'pattern' => '/^[a-zA-Z0-9]+$/i'
                        )
                    )
                ),
            	'attr' => array(
            		'placeholder' => 'クーポンコード',
            	)
            ));
         
         
         $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                $form = $event->getForm();
                $data = $form->getData();

                if ($app->isGranted('ROLE_USER')) {
                	$Customer = $app->user();
                }else{
                	$Customer = $app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember');
                }
                
                //注文情報を取得する。
                $preOrderId = $app['eccube.service.cart']->getPreOrderId();
                
                $Order = $this->app['eccube.repository.order']->findOneBy(array(
                		'pre_order_id' => $preOrderId,
                		'OrderStatus' => $app['config']['order_processing']
                ));
                
                //クーポンのステータス、有効期間などをチェックする。
                $coupons = $app['eccube.plugin.simplecoupon.repository.coupon']->findBy(
                		array(
                				'couponCode' => $data['couponCode'],
                				'status' => Coupon::STATUS_VALID,
                				'delFlg' => Coupon::DEL_FLG_ACTIVE,
                		),
                		array('couponId' => 'DESC')
                );
                
                if(count($coupons)==0){
                	$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.code.invalid'));
                	return;
                }else{
                	$Coupon = $coupons[0];
                	
                	if($Coupon){
                		//期限チェック
                		$now = Carbon::today();
                		$start = Carbon::instance($Coupon->getFromDate());
                		if($start->gt($now)){
                			$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.code.invalid'));
                			return;
                		}
                		if(!is_null($Coupon->getToDate())){
                			$end = Carbon::instance($Coupon->getToDate());
                			if($end->lt($now)){
                				$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.terms.over'));
                				return;
                			}
                		}
                		
                		//最低金額チェック
                		if($Order->getSubtotal() < $Coupon->getBottomPrice()){
                			$msg_format = $this->app->trans('front.plugin.simplecoupon.shopping.bottom_price.under');
                			$form['coupon_code']->addError(new FormError(sprintf($msg_format,$Coupon->getBottomPrice())));
                			return;
                		}
                		
                		//ゲスト購入での利用可否チェック
                		if (!$app->isGranted('ROLE_USER')) {
	                		if(Coupon::GUEST_USE_FLG_DENY == $Coupon->getGuestUseFlg()){
	                			$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.guest.deny'));
	                			return;
	                		}
                		}
                		//詳細条件をチェック
                		if($Coupon->getConditionType() == Coupon::CONDITION_TYPE_CUSTOMER_ID){
                			//ユーザID指定の場合
                			if (!$app->isGranted('ROLE_USER')) {
                				$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.guest.deny'));
                				return;
                			}
                			$Customer = $app->user();
											$Condition = $app['eccube.plugin.simplecoupon.repository.condition_customer']->findOneBy(array('couponId'=>$Coupon->getCouponId(), 'customerId'=>$Customer->getId()));
											if($Coupon->getConditionActionType() == Coupon::CONDITION_ACTION_TYPE_ALLOW){
												if(!$Condition){
													$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.code.invalid'));
													return;
												}
											}
											else{
												if($Condition){
													$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.code.invalid'));
													return;
												}
											}
										}
										else if($Coupon->getConditionType() == Coupon::CONDITION_TYPE_PRODUCT){
											//商品指定の場合
											$ConditionList = $app['eccube.plugin.simplecoupon.repository.condition_product']->findBy(array('couponId'=>$Coupon->getCouponId()));
											$DetailList = $Order->getOrderDetails();
											$ok = false;
											foreach($DetailList as $Detail){
												foreach($ConditionList as $Condition){
													if($Detail->getProductClass()->getId() == $Condition->getProductClassId()){
														$ok = true;
														break;
													}
												}
												if($ok){
													break;
												}
											}
											$app->log("UseCouponType validation STEP 1000");
											if($Coupon->getConditionActionType() == Coupon::CONDITION_ACTION_TYPE_ALLOW){
												$app->log("UseCouponType validation STEP 1010");
												if(!$ok){
													$app->log("UseCouponType validation STEP 1011");
													$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.product.limited'));
													return;
												}
											}
											else{
												$app->log("UseCouponType validation STEP 1020");
												if($ok){
													$app->log("UseCouponType validation STEP 1021");
													$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.product.limited'));
													return;
												}
											}
											

										}
										else if($Coupon->getConditionType() == Coupon::CONDITION_TYPE_CATEGORY){
											//カテゴリ指定の場合
											$ConditionList = $app['eccube.plugin.simplecoupon.repository.condition_product']->findBy(array('couponId'=>$Coupon->getCouponId()));
											$DetailList = $Order->getOrderDetails();
											$ok = false;
											foreach($DetailList as $Detail){

												$ProductCategoryList = $Detail->getProduct()->getProductCategories();
												foreach($ProductCategoryList as $ProductCategory){

													foreach($ConditionList as $Condition){
														if($ProductCategory->getCategoryId() == $Condition->getCategoryId()){
															$ok = true;
															break;
														}
													}
													if($ok){
														break;
													}

												}
												if($ok){
													break;
												}
											}
											if($Coupon->getConditionActionType() == Coupon::CONDITION_ACTION_TYPE_ALLOW){
												if(!$ok){
													$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.product.limited'));
													return;
												}
											}
											else{
												if($ok){
													$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.product.limited'));
													return;
												}
											}

										}
                		
                		//発行数上限をチェック
                		$count = $app['eccube.plugin.simplecoupon.repository.coupon_order']->getCountByCoupon($Coupon);
                		if($count >= $Coupon->getNumberOfIssued()){
                			$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.end'));
                			return;
                		}
                	}
                }
                
                //クーポンが登録済みかどうかチェックする。
                $couponOrder = $app['eccube.plugin.simplecoupon.repository.coupon_order']->findOneBy(
                		array('Coupon' => $Coupon, 
                				'orderId' => $Order->getId()
                		)
                );
                if(!is_null($couponOrder)){
                	$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.already_registered'));
                	return;
                }
                //他の注文で利用済みかどうかチェックする。
                if($app['eccube.plugin.simplecoupon.service.coupon']->isUsedCoupon($Coupon, $Customer)){
                	if($Coupon->getOnetimeUseFlg() == Coupon::ONETIME_USE_FLG_ONETIME){
                		$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.already_used'));
                		return;
                	}
                }
                //併用可否のチェック。この注文で登録済みの他のクーポンを取得する。
                $coupon_list = $app['eccube.plugin.simplecoupon.repository.coupon_order']->getCouponListByOrder($Order->getId());
                if($Coupon->getCombinedUseFlg() == Coupon::COMBINED_USE_FLG_DENY){
                	if(count($coupon_list)>0){
                		$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.combined_use.deny.1'));
                		return;
                	}
                }else{
                	foreach($coupon_list as $c){
                		if($c->getCoupon()->getCombinedUseFlg() == Coupon::COMBINED_USE_FLG_DENY){
                			$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.combined_use.deny.2'));
                			return;
                		}elseif($c->getCoupon()->getDiscountTargetType() != $Coupon->getDiscountTargetType()){
                			$form['coupon_code']->addError(new FormError('front.plugin.simplecoupon.shopping.combined_use.deny.3'));
                			return;
                		}
                	}
                }
                
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\SimpleCoupon\Entity\Coupon',
        ));
    }


    public function getName()
    {
        return 'plg_simplecoupon_use_coupon';
    }
}
