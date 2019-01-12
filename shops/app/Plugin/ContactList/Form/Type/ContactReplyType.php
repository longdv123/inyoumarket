<?php

namespace Plugin\ContactList\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContactReplyType extends AbstractType
{
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
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
            ->add('subject', 'text', array(
                'label' => '件名',
                'required' => true,
            ))
            ->add('contents', 'textarea', array(
                'label' => '本文',
                'required' => true,
            ))
            ->add('memo', 'textarea', array(
                'label' => '管理者メモ',
                'required' => false,
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'contact_reply';
    }
}