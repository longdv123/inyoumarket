<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Form\Type;

use Plugin\ExternalPaymentGateway\Controller\Util\PaymentUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ConfigType extends AbstractType
{
    private $app;
    private $subData;

    private $sslCode;
    private $speedCode;

    public function __construct(\Eccube\Application $app, $subData = null)
    {
        $this->app = $app;
        $this->subData = $subData;
        $this->sslCode   = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SSL_CODE'];
        $this->speedCode = $this->app['config']['ExternalPaymentGateway']['const']['EXTERNAL_CREDIT_SPEED_CODE'];

    }

    /**
     * Build config type form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return type
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
     
        if (empty($this->subData)) {
            $this->subData = array(
                'clientip' => null,
                'credit_payment' => null,
            );
        } 
        $arrPayments = array(
            $this->sslCode   => '都度決済',
            $this->speedCode => '都度決済＋スピード決済',
        );

        $builder
            ->add('credit_payment', 'choice', array(
                'choices' => $arrPayments,
                'data' => $this->subData['credit_payment'],
                'expanded' => true,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '※ 利用決済が選択されていません。')),
                ),
            ))
            
            ->add('clientip', 'text', array(
                'label' => 'クライアントIP',
                'attr' => array(
                    'class' => 'lockon_card_row',
                    'minlength' => '5',
                    'maxlength' => '5',
                ),
                'data' => $this->subData['clientip'],
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '※ クライアントIPが入力されていません。')),
                    new Assert\Length(array('min' => 5, 'max' => 5, 'exactMessage' => "※ クライアントIPが5桁ではありません。")),
                    new Assert\Regex(array('pattern' => "/^[0-9]+$/", 'match' => true, 'message' => '※ クライアントIPに数字以外の文字が含まれています。')),
                ),
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'config';
    }
}
