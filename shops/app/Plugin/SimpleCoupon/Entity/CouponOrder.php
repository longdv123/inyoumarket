<?php

namespace Plugin\SimpleCoupon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CouponOrder
 */
class CouponOrder extends \Eccube\Entity\AbstractEntity
{
	
	const STATUS_PROCESSING = 0;
	const STATUS_COMPLETE = 1;
	const STATUS_CANCEL = 2;
	
	
    /**
     * @var integer
     */
    private $couponOrderId;

    /**
     * @var integer
     */
    private $orderId;

    /**
     * @var integer
     */
    private $customerId;

    /**
     * @var string
     */
    private $email;
    
    /**
     * @var integer
     */
    private $status;
    
    /**
     * @var integer
     */
    private $discountPrice;
    
    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \Plugin\SimpleCoupon\Entity\Coupon
     */
    private $Coupon;
    
    /**
     * @var \Eccube\Entity\Order
     */
    private $Order;


    /**
     * Get couponOrderId
     *
     * @return integer 
     */
    public function getCouponOrderId()
    {
        return $this->couponOrderId;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return CouponOrder
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set customerId
     *
     * @param integer $customerId
     * @return CouponOrder
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
    
    /**
     * Set email
     *
     * @param string $email
     * @return CouponOrder
     */
    public function setEmail($email)
    {
    	$this->email = $email;
    
    	return $this;
    }
    
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
    	return $this->email;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CouponOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set discountPrice
     *
     * @param integer $discountPrice
     * @return CouponOrder
     */
    public function setDiscountPrice($discountPrice)
    {
    	$this->discountPrice = $discountPrice;
    
    	return $this;
    }
    
    /**
     * Get discountPrice
     *
     * @return integer
     */
    public function getDiscountPrice()
    {
    	return $this->discountPrice;
    }
    
    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return CouponOrder
     */
    public function setCreateDate($createDate)
    {
    	$this->createDate = $createDate;
    
    	return $this;
    }
    
    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
    	return $this->createDate;
    }

    /**
     * Set Coupon
     *
     * @param \Plugin\SimpleCoupon\Entity\Coupon $coupon
     * @return CouponOrder
     */
    public function setCoupon(\Plugin\SimpleCoupon\Entity\Coupon $coupon = null)
    {
        $this->Coupon = $coupon;

        return $this;
    }

    /**
     * Get Coupon
     *
     * @return \Plugin\SimpleCoupon\Entity\Coupon 
     */
    public function getCoupon()
    {
        return $this->Coupon;
    }
    
    /**
     * Set Order
     *
     * @param \Eccube\Entity\Order $Order
     * @return CouponOrder
     */
    public function setOrder(\Eccube\Entity\Order $Order = null)
    {
    	$this->Order = $Order;
    
    	return $this;
    }
    
    /**
     * Get Order
     *
     * @return \Eccube\Entity\Order
     */
    public function getOrder()
    {
    	return $this->Order;
    }
}
