<?php

namespace Plugin\ContactList\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ConfigType extends AbstractType
{
    public function __construct()
    {

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
            ->add('contact_status', 'collection', array(
                'label' => '対応状況',
                'type' => new \Plugin\ContactList\Form\Type\ContactStatusType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'mapped' => true,
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_contact_list_config';
    }
}