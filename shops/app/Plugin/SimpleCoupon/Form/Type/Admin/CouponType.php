<?php

namespace Plugin\SimpleCoupon\Form\Type\Admin;

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

class CouponType extends AbstractType
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
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Regex(array('pattern' => '/^[a-zA-Z0-9]+$/i')),
                	new Assert\Length(array('max' => 50,)),
                ),
            ))
            ->add('coupon_name', 'text', array(
                'label' => 'クーポン名',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                	new Assert\Length(array('max' => 200,)),
                ),
            ))
            ->add('discount_type', 'choice', array(
                'choices' => array(1 => '値引き額', 2 => '値引率'),
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => '値引き種別',
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ))
            ->add('discount_target_type', 'choice', array(
            		'choices' => array(0 => '商品、送料、手数料込みの注文合計金額', 1 => '商品小計', 2 => '送料'),
            		'required' => true,
            		'expanded' => false,
            		'multiple' => false,
            		'label' => '値引き対象金額',
            		'constraints' => array(
            				new Assert\NotBlank()
            		),
            ))
            ->add('discount_value', 'integer', array(
                'label' => '値引額/率',
                'required' => false,
                'constraints' => array(
                    new Assert\Range(array(
                        'min' => 1,
                    ))
                ),
            ))
            ->add('from_date', 'date', array(
                'label' => '有効期間',
                'required' => true,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ))
            ->add('to_date', 'date', array(
                'label' => '有効期間日(TO)',
                'required' => true,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ))
            ->add('combined_use_flg', 'choice', array(
            		'label' => '併用利用の可否',
            		'required' => true,
            		'choices' => array(
            				'' => '--',
            				'0' => '許可しない',
            				'1' => '許可する'
            		),
            		'constraints' => array(
            				new Assert\NotBlank(),
            		),
            ))
            ->add('guest_use_flg', 'choice', array(
            		'label' => 'ゲスト購入時の利用の可否',
            		'required' => true,
            		'choices' => array(
            				'' => '--',
            				'0' => '許可しない',
            				'1' => '許可する'
            		),
            		'constraints' => array(
            				new Assert\NotBlank(),
            		),
            ))
            ->add('onetime_use_flg', 'choice', array(
            		'label' => '一人の利用回数の制限の有無',
            		'required' => true,
            		'choices' => array(
            				'' => '--',
            				'0' => '利用回数の制限なし',
            				'1' => '一人一回まで'
            		),
            		'constraints' => array(
            				new Assert\NotBlank(),
            		),
            ))
            ->add('number_of_issued', 'text', array(
            		'label' => 'クーポン発行数',
            		'required' => true,
            		'trim' => true,
	                'constraints' => array(
	                    new Assert\NotBlank(),
	                    new Assert\Regex(array(
	                            'pattern' => '/^[0-9]+$/i'
	                        )
	                    ),
                		new Assert\Range(array(
                				'min' => 0,
                		))
	                ),
            ))
            ->add('bottom_price', 'text', array(
            		'label' => 'クーポンの利用に必要な購入金額',
            		'required' => true,
            		'trim' => true,
            		'constraints' => array(
            				new Assert\NotBlank(),
            				new Assert\Regex(array(
            						'pattern' => '/^[0-9]+$/i'
            				)
            						),
                			new Assert\Range(array(
                				'min' => 0,
                			))
            		),
            ))
            ->add('status', 'choice', array(
            	'label' => 'ステータス',
            	'required' => true,
           		'choices' => array(
           			'' => '--',
       				'0' => '無効',
       				'1' => '有効'
           		),
            	'constraints' => array(
            		new Assert\NotBlank(),
           		),
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                $form = $event->getForm();
                $data = $form->getData();

                if ($data['discount_type'] == 1) {
                    // 値引き額
                	
                    /** @var ConstraintViolationList $errors */
                    $errors = $app['validator']->validateValue($data['discount_value'], array(
                        new Assert\NotBlank(),
                    ));
                                        
                    if ($errors->count() > 0) {
                        foreach ($errors as $error) {
                            $form['discount_value']->addError(new FormError($error->getMessage()));
                        }
                    }

                } else if ($data['discount_type'] == 2) {
                    // 値引率

                    /** @var ConstraintViolationList $errors */
                    $errors = $app['validator']->validateValue($data['discount_value'], array(
                        new Assert\NotBlank(),
                        new Assert\Range(array(
                            'min' => 1,
                            'max' => 100,
                        )),
                    ));
                    if ($errors->count() > 0) {
                        foreach ($errors as $error) {
                            $form['discount_value']->addError(new FormError($error->getMessage()));
                        }
                    }

                }

                if (!empty($data['from_date']) && !empty($data['to_date'])) {
                    $now = Carbon::today();
                    $fromDate = Carbon::instance($data['from_date']);
                    $toDate = Carbon::instance($data['to_date']);

                    if ($fromDate->gt($toDate)) {
                        $form['from_date']->addError(new FormError('admin.plugin.simplecoupon.regist.terms.invalid'));
                    }
                }
                
                // 既に登録されているクーポンコードは利用できない
                if (null !== $data->getCouponCode()) {
                	$qb = $app['eccube.plugin.simplecoupon.repository.coupon']
                        ->createQueryBuilder('c')
                        ->select('COUNT(c)')
                        ->where('c.couponCode = :coupon_cd')
                        ->setParameter('coupon_cd', $data->getCouponCode());

                    // 新規登録時.
                    if ($data->getCouponId() === null) {
                    	$count = $qb->getQuery()->getSingleScalarResult();
                    // 編集時.
                    } else {
                    	$qb->andWhere('c.couponId <> :coupon_id')
                            ->setParameter('coupon_id', $data->getCouponId());
                        $count = $qb->getQuery()->getSingleScalarResult();
                    }

                    if ($count > 0) {
                        $form['coupon_code']->addError(new FormError('admin.plugin.simplecoupon.regist.already_used'));
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
        return 'plg_simplecoupon_admin_coupon';
    }
}
