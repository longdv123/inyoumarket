<?php

namespace Plugin\SimpleCoupon\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SearchCouponType extends AbstractType
{

    protected $app;
    protected $config;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->config;
        $builder
            // クーポンID・クーポン名・クーポンコード
            ->add('multi', 'text', array(
                'label' => 'クーポンID・クーポン名・クーポンコード',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array('max' => $config['stext_len'])),
                ),
            ))
            ->add('status', 'choice', array(
                'label' => 'ステータス',
            	'choices' => array(
            		'1' => '有効',
            		'0' => '無効'
            		
            	),
            ))
            ->add('date_start', 'date', array(
                'label' => '期間(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('date_end', 'date', array(
                'label' => '期間(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
        ;
    }
    

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'plg_simplecoupon_admin_search_coupon';
    }
}