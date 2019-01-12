<?php

namespace Plugin\ContactList\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContactSearchType extends AbstractType
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('multi', 'text', array(
                'label' => '問い合わせ番号・お名前・メールアドレス',
                'required' => false,
            ))
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
            ->add('contact_id_start', 'integer', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Range(array('min' => 1)),
                ),
            ))
            ->add('contact_id_end', 'integer', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Range(array('min' => 1)),
                ),
            ))
            ->add('name', 'text', array(
                'required' => false,
            ))
            ->add('kana', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                        'message' => 'form.type.admin.notkanastyle',
                    )),
                ),
            ))
            ->add('email', 'email', array(
                'required' => false,
            ))
            ->add('contact_date_start', 'date', array(
                'label' => '問い合わせ日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('contact_date_end', 'date', array(
                'label' => '問い合わせ日(TO)',
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
        return 'contact_search';
    }
}
