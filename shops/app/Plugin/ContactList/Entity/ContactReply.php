<?php

namespace Plugin\ContactList\Entity;

class ContactReply extends \Eccube\Entity\AbstractEntity {
    private $reply_id;
    private $reply_ins_time;
    private $reply_del_time;
    private $reply_contact_id;
    private $reply_member;
    private $reply_subject;
    private $reply_contents;
    private $reply_memo;

    public function getId() {
        return $this->reply_id;
    }

    public function getInsTime() {
        return $this->reply_ins_time;
    }

    public function setInsTime( $ins_time ) {
        $this->reply_ins_time = $ins_time;

        return $this;
    }

    public function getDelTime() {
        return $this->reply_del_time;
    }

    public function setDelTime( $del_time ) {
        $this->reply_del_time = $del_time;

        return $this;
    }

    public function getReplyContactId() {
        return $this->reply_contact_id;
    }

    public function setReplyContactId( ContactList $ContactList = null ) {
        $this->reply_contact_id = $ContactList;

        return $this;
    }

    public function getReplyMember() {
        return $this->reply_member;
    }

    public function setReplyMember( \Eccube\Entity\Member $Member = null ) {
        $this->reply_member = $Member;

        return $this;
    }

    public function getReplySubject() {
        return $this->reply_subject;
    }

    public function setReplySubject( $subject ) {
        $this->reply_subject = $subject;

        return $this;
    }

    public function getReplyContents() {
        return $this->reply_contents;
    }

    public function setReplyContents( $contents ) {
        $this->reply_contents = $contents;

        return $this;
    }

    public function getReplyMemo() {
        return $this->reply_memo;
    }

    public function setReplyMemo( $memo ) {
        $this->reply_memo = $memo;

        return $this;
    }
}
