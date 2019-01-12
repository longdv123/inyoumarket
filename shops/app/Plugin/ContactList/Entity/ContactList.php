<?php

namespace Plugin\ContactList\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ContactList extends \Eccube\Entity\AbstractEntity
{
    private $contact_id;
    private $contact_ins_time;
    private $contact_del_time;
    private $contact_status;
    private $contact_name01;
    private $contact_name02;
    private $contact_kana01;
    private $contact_kana02;
    private $contact_zip01;
    private $contact_zip02;
    private $contact_pref;
    private $contact_addr01;
    private $contact_addr02;
    private $contact_tel01;
    private $contact_tel02;
    private $contact_tel03;
    private $contact_email;
    //private $contact_kind;
    private $contact_contents;
    private $contact_memo;
    private $contact_replies;

    public function __construct()
    {
        $this->contact_replies = new ArrayCollection();
    }

    public function getId()
    {
        return $this->contact_id;
    }

    public function getInsTime()
    {
        return $this->contact_ins_time;
    }

    public function setInsTime($ins_time)
    {
        $this->contact_ins_time = $ins_time;

        return $this;
    }

    public function getDelTime()
    {
        return $this->contact_del_time;
    }

    public function setDelTime($del_time)
    {
        $this->contact_del_time = $del_time;

        return $this;
    }

    public function getName01()
    {
        return $this->contact_name01;
    }

    public function setName01($name01)
    {
        $this->contact_name01 = $name01;

        return $this;
    }

    public function getStatus()
    {
        return $this->contact_status;
    }

    public function setStatus(ContactStatus $status = null)
    {
        $this->contact_status = $status;

        return $this;
    }

    public function getName02()
    {
        return $this->contact_name02;
    }

    public function setName02($name02)
    {
        $this->contact_name02 = $name02;

        return $this;
    }

    public function getKana01()
    {
        return $this->contact_kana01;
    }

    public function setKana01($kana01)
    {
        $this->contact_kana01 = $kana01;

        return $this;
    }

    public function getKana02()
    {
        return $this->contact_kana02;
    }

    public function setKana02($kana02)
    {
        $this->contact_kana02 = $kana02;

        return $this;
    }

    public function getZip01()
    {
        return $this->contact_zip01;
    }

    public function setZip01($zip01)
    {
        $this->contact_zip01 = $zip01;

        return $this;
    }

    public function getZip02()
    {
        return $this->contact_zip02;
    }

    public function setZip02($zip02)
    {
        $this->contact_zip02 = $zip02;

        return $this;
    }

    public function getPref()
    {
        return $this->contact_pref;
    }

    public function setPref(\Eccube\Entity\Master\Pref $pref = null)
    {
        $this->contact_pref = $pref;

        return $this;
    }

    public function getAddr01()
    {
        return $this->contact_addr01;
    }

    public function setAddr01($addr01)
    {
        $this->contact_addr01 = $addr01;

        return $this;
    }

    public function getAddr02()
    {
        return $this->contact_addr02;
    }

    public function setAddr02($addr02)
    {
        $this->contact_addr02 = $addr02;

        return $this;
    }

    public function getTel01()
    {
        return $this->contact_tel01;
    }

    public function setTel01($tel01)
    {
        $this->contact_tel01 = $tel01;

        return $this;
    }

    public function getTel02()
    {
        return $this->contact_tel02;
    }

    public function setTel02($tel02)
    {
        $this->contact_tel02 = $tel02;

        return $this;
    }

    public function getTel03()
    {
        return $this->contact_tel03;
    }

    public function setTel03($tel03)
    {
        $this->contact_tel03 = $tel03;

        return $this;
    }

    public function getEmail()
    {
        return $this->contact_email;
    }

    public function setEmail($email)
    {
        $this->contact_email = $email;

        return $this;
    }

    public function getKind()
    {
        return $this->contact_kind;
    }

    public function setKind($kind)
    {
        $this->contact_kind = $kind;

        return $this;
    }

    public function getContents()
    {
        return $this->contact_contents;
    }

    public function setContents($contents)
    {
        $this->contact_contents = $contents;

        return $this;
    }

    public function getMemo()
    {
        return $this->contact_memo;
    }

    public function setMemo($memo)
    {
        $this->contact_memo = $memo;

        return $this;
    }

    public function addContactReply(ContactReply $ContactReply)
    {
        $this->contact_replies[] = $ContactReply;

        return $this;
    }

    public function getContactReplies(){
        return $this->contact_replies;
    }
}