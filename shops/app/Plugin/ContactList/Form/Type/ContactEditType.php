<?php

namespace Plugin\ContactList\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContactEditType extends AbstractType
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
            ->add('status', 'entity', array(
                'label' => '状況',
                'required' => false,
                'class' => 'Plugin\ContactList\Entity\ContactStatus',
                'property' => 'name',
                'empty_value' => '選択してください',
                'empty_data' => null,
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('o')
                              ->orderBy('o.rank', 'ASC');
                },
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
        return 'contact_edit';
    }
}