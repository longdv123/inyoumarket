<?php

namespace Plugin\DPost\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPush
 */
class ProductPush extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $productPushId;

    /**
     * @var integer
     */
    private $product_id;

    /**
     * @var integer
     */
    private $wait_count;

    /**
     * @var integer
     */
    private $push_count;

    /**
     * @var integer
     */
    private $click;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;


    /**
     * Get productPushId
     *
     * @return integer 
     */
    public function getProductPushId()
    {
        return $this->productPushId;
    }

    /**
     * Set product_id
     *
     * @param integer $productId
     * @return ProductPush
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get product_id
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set wait_count
     *
     * @param integer $waitCount
     * @return ProductPush
     */
    public function setWaitCount($waitCount)
    {
        $this->wait_count = $waitCount;

        return $this;
    }

    /**
     * Get wait_count
     *
     * @return integer 
     */
    public function getWaitCount()
    {
        return $this->wait_count;
    }

    /**
     * Set push_count
     *
     * @param integer $pushCount
     * @return ProductPush
     */
    public function setPushCount($pushCount)
    {
        $this->push_count = $pushCount;

        return $this;
    }

    /**
     * Get push_count
     *
     * @return integer 
     */
    public function getPushCount()
    {
        return $this->push_count;
    }

    /**
     * Set click
     *
     * @param integer $click
     * @return ProductPush
     */
    public function setClick($click)
    {
        $this->click = $click;

        return $this;
    }

    /**
     * Get click
     *
     * @return integer 
     */
    public function getClick()
    {
        return $this->click;
    }

    /**
     * Set Product
     *
     * @param \Eccube\Entity\Product $product
     * @return ProductPush
     */
    public function setProduct(\Eccube\Entity\Product $product = null)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get Product
     *
     * @return \Eccube\Entity\Product 
     */
    public function getProduct()
    {
        return $this->Product;
    }
}
