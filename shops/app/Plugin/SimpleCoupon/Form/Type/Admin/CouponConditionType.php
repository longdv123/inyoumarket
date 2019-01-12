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

class CouponConditionType extends AbstractType
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
        	))
            ->add('condition_type', 'choice', array(
                'choices' => array(0 => '条件なし', 1 => 'ユーザID限定', 2 => '商品限定', 3 => 'カテゴリ限定'),
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => '条件種別',
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ))
            ->add('condition_action_type', 'choice', array(
                'choices' => array(0 => '条件に合致する場合にクーポンの利用を許可する', 1 => '条件に合致する場合にクーポンの利用を拒否する'),
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'label' => '処理種別',
            ))
            ->add('target_customer', 'textarea', array(
            		'label' => '対象ユーザID',
            		'required' => false,
            		'trim' => true,
            ))
            ->add('target_product', 'textarea', array(
                'label' => '対象商品ID',
                'required' => false,
                'trim' => true,
            ))
            ->add('target_category', 'textarea', array(
                'label' => '対象カテゴリID',
                'required' => false,
                'trim' => true,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                $form = $event->getForm();
                $data = $form->getData();

                if ($data['condition_type'] != 0) {
                    $errors = $app['validator']->validateValue($data['condition_action_type'], array(
                        new Assert\NotBlank(),
                    ));

                    if ($errors->count() > 0) {
                        foreach ($errors as $error) {
                            $form['condition_action_type']->addError(new FormError($error->getMessage()));
                        }
                    }
                }
                
                if ($data['condition_type'] == 1) {
                    // 会員ID限定
                	
                    /** @var ConstraintViolationList $errors */
                    $errors = $app['validator']->validateValue($data['target_customer'], array(
                        //new Assert\NotBlank(),
                    ));
                    
                    if ($errors->count() > 0) {
                        foreach ($errors as $error) {
                            $form['target_customer']->addError(new FormError($error->getMessage()));
                        }
                    }

                }
                else if ($data['condition_type'] == 2) {
                    // 商品ID限定
                	
                    /** @var ConstraintViolationList $errors */
                    $errors = $app['validator']->validateValue($data['target_product'], array(
                        //new Assert\NotBlank(),
                    ));
                    
                    if ($errors->count() > 0) {
                        foreach ($errors as $error) {
                            $form['target_product']->addError(new FormError($error->getMessage()));
                        }
                    }

                }
                else if ($data['condition_type'] == 3) {
                    // 商品カテゴリ限定
                	
                    /** @var ConstraintViolationList $errors */
                    $errors = $app['validator']->validateValue($data['target_category'], array(
                        //new Assert\NotBlank(),
                    ));
                    
                    if ($errors->count() > 0) {
                        foreach ($errors as $error) {
                            $form['target_category']->addError(new FormError($error->getMessage()));
                        }
                    }

                }
                else if ($data['condition_type'] == 0) {
                    // 条件指定なしの場合

                }

            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\SimpleCoupon\Entity\CouponCondition',
        ));
    }


    public function getName()
    {
        return 'plg_simplecoupon_admin_coupon_condition';
    }
}
