<?php
/*
 * Copyright(c) 2015 SYSTEM_KD
 */

namespace Plugin\DPost\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractType
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
            ->add('project_id', 'text', array(
                'label' => 'Project ID',
                'attr' => array(
                    'class' => 'lockon_card_row',
                ),
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '※ サイトIDが入力されていません。')),
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))

            ->add('api_key', 'text', array(
                'label' => 'API Key',
                'attr' => array(
                    'class' => 'lockon_card_row',
                ),
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '※ ショップIDが入力されていません。')),
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))

            ->add('product_flg', 'choice', array(
                    'label' => '入荷通知受付',
                    'choices' => array(0 => '無効', 1 => '有効'),
                    'expanded' => true,
                    'constraints' => array(
                            new Assert\NotBlank(),
                    ),
            ))

            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Plugin\DPost\Entity\DpostSetting',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dpostconfig';
    }
}
