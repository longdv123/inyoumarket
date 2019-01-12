<?php

namespace Plugin\ContactList;

use Eccube\Event\EventArgs;

class ContactListEvent {
    /** @var \Eccube\Application $app */
    private $app;

    public function __construct( $app ) {
        $this->app = $app;
    }

    public function onContactInitialize( EventArgs $event ){
        // プラグインの動作確認用
        // dump( "000" );
    }

    /**
     * お問い合わせ完了：DBに登録
     *
     * @param EventArgs $event
     */
    public function onContactComplete( EventArgs $event ) {
        /** @var Application $app */
        $app = $this->app;
        /** @var Category $target_category */
        $data = $event->getArgument('data');
        /** @var FormInterface $form */
//        $form = $event->getArgument('form');

        $ContactList = new \Plugin\ContactList\Entity\ContactList();
        // エンティティを更新
        $ContactList
            ->setInsTime(new \DateTime())
            ->setName01($data['name01'])
            ->setName02($data['name02'])
            ->setKana01($data['kana01'])
            ->setKana02($data['kana02'])
            ->setZip01($data['zip01'])
            ->setZip02($data['zip02'])
            ->setPref($data['pref'])
            ->setAddr01($data['addr01'])
            ->setAddr02($data['addr02'])
            ->setTel01($data['tel01'])
            ->setTel02($data['tel02'])
            ->setTel03($data['tel03'])
            ->setEmail($data['email'])
            ->setKind($data['kind'])
            ->setContents($data['contents']);
        // DB更新
        $app['orm.em']->persist( $ContactList );
        $app['orm.em']->flush( $ContactList );
        $data['contact_id'] = $ContactList->getId();
        $event->setArgument('data', $data);
    }

    /**
     * お問い合わせメールの件名と本文にお問い合わせ番号を追記
     *
     * @param EventArgs $event
     */
    public function onMailContact( EventArgs $event ){

        $formData = $event->getArgument('formData');
        $swift_message = $event->getArgument('message');
        $subject = $swift_message->getHeaders()->get('subject')->getValue();
        $subject .= sprintf("[お問い合わせ番号：%d]", $formData['contact_id']);
        $swift_message->getHeaders()->get('subject')->setValue($subject);

        $body = $swift_message->getBody();
        $body = <<< EOF

■お問い合わせ番号：{$formData['contact_id']}
{$body}
EOF;
        $swift_message->setBody($body);

    }
}