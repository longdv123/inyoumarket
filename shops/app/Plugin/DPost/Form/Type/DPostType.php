<?php
/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DPostType extends AbstractType
{
    private $app;
    private $subData;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
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

        $builder
            ->add('regist_id', 'text', array(
                'label' => 'Regist ID',
                )
            )
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Plugin\DPost\Entity\CustomerEx',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dposttype';
    }
}
