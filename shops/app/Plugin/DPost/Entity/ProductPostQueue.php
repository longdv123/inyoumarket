<?php

namespace Plugin\DPost\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPostQueue
 */
class ProductPostQueue extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $product_post_id;

    /**
     * @var string
     */
    private $regist_id;

    /**
     * @var integer
     */
    private $product_id;

    /**
     * @var integer
     */
    private $queue_status;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;


    /**
     * Get product_post_id
     *
     * @return integer 
     */
    public function getProductPostId()
    {
        return $this->product_post_id;
    }

    /**
     * Set regist_id
     *
     * @param string $registId
     * @return ProductPostQueue
     */
    public function setRegistId($registId)
    {
        $this->regist_id = $registId;

        return $this;
    }

    /**
     * Get regist_id
     *
     * @return string 
     */
    public function getRegistId()
    {
        return $this->regist_id;
    }

    /**
     * Set product_id
     *
     * @param integer $productId
     * @return ProductPostQueue
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
     * Set queue_status
     *
     * @param integer $queueStatus
     * @return ProductPostQueue
     */
    public function setQueueStatus($queueStatus)
    {
        $this->queue_status = $queueStatus;

        return $this;
    }

    /**
     * Get queue_status
     *
     * @return integer 
     */
    public function getQueueStatus()
    {
        return $this->queue_status;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return ProductPostQueue
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return ProductPostQueue
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set Product
     *
     * @param \Eccube\Entity\Product $product
     * @return ProductPostQueue
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
