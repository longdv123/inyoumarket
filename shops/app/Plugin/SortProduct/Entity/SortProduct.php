<?php

namespace Plugin\SortProduct\Entity;

use Doctrine\ORM\Mapping as ORM;

class SortProduct extends \Eccube\Entity\AbstractEntity
{
    private $id;
    private $product_id;
    private $rank;


    public function getId()
    {
        return $this->id;
    }

    public function setProduct_id($product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }
    public function getProduct_id()
    {
        return $this->product_id;
    }

    public function setRank($rank)
    {
        $this->rank = $rank;
        return $this;
    }
    public function getRank()
    {
        return $this->rank;
    }


}
