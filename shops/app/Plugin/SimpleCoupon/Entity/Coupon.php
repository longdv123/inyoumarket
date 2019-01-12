<?php

namespace Plugin\SimpleCoupon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Coupon
 */
class Coupon extends \Eccube\Entity\AbstractEntity
{
	
	const STATUS_INVALID = 0;
	const STATUS_VALID = 1;
	
	const DEL_FLG_ACTIVE = 0;
	const DEL_FLG_DELETED = 1;
	
	const DISCOUNT_TYPE_PRICE = 1;
	const DISCOUNT_TYPE_RATE = 2;
	
	const DISCOUNT_TARGET_TYPE_TOTAL = 0;
    const DISCOUNT_TARGET_TYPE_SUBTOTAL = 1;
    const DISCOUNT_TARGET_TYPE_DELIVERY_FEE = 2;
	
	const COMBINED_USE_FLG_DENY = 0;
	const COMBINED_USE_FLG_ALLOW = 1;
	
	const GUEST_USE_FLG_DENY = 0;
	const GUEST_USE_FLG_ALLOW = 1;
	
	const CONDITION_TYPE_NONE = 0;
    const CONDITION_TYPE_CUSTOMER_ID = 1;
    const CONDITION_TYPE_PRODUCT = 2;
    const CONDITION_TYPE_CATEGORY = 3;

    const CONDITION_ACTION_TYPE_ALLOW = 0;
    const CONDITION_ACTION_TYPE_DENY = 1;
	
	const ONETIME_USE_FLG_NOLIMIT = 0;
	const ONETIME_USE_FLG_ONETIME = 1;
	
	
	
    /**
     * @var integer
     */
    private $couponId;

    /**
     * @var string
     */
    private $couponName;

    /**
     * @var string
     */
    private $couponCode;

    /**
     * @var \DateTime
     */
    private $fromDate;

    /**
     * @var \DateTime
     */
    private $toDate;

    /**
     * @var integer
     */
    private $discountValue;

    /**
     * @var integer
     */
    private $discountType;
    
    /**
     * @var integer
     */
    private $discountTargetType;

    /**
     * @var integer
     */
    private $combinedUseFlg;
    
    /**
     * @var integer
     */
    private $guestUseFlg;
    
    /**
     * @var integer
     */
    private $onetimeUseFlg;
    
    /**
     * @var integer
     */
    private $numberOfIssued;
    
    /**
     * @var integer
     */
    private $bottomPrice;
    
    /**
     * @var integer
     */
    private $conditionType;

    /**
     * @var integer
     */
     private $conditionActionType;
    
    /**
     * @var integer
     */
    private $status;
    
    /**
     * @var integer
     */
    private $delFlg;
    
    /**
     * @var \DateTime
     */
    private $createDate;
    
    /**
     * @var \DateTime
     */
    private $updateDate;
        
    /**
     * @var integer
     */
    private $orderCount;
    

    /**
     * Get couponId
     *
     * @return integer 
     */
    public function getCouponId()
    {
        return $this->couponId;
    }

    /**
     * Set couponName
     *
     * @param string $couponName
     * @return Coupon
     */
    public function setCouponName($couponName)
    {
        $this->couponName = $couponName;

        return $this;
    }

    /**
     * Get couponName
     *
     * @return string 
     */
    public function getCouponName()
    {
        return $this->couponName;
    }

    /**
     * Set couponCode
     *
     * @param string $couponCode
     * @return Coupon
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;

        return $this;
    }

    /**
     * Get couponCode
     *
     * @return string 
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * Set fromDate
     *
     * @param \DateTime $fromDate
     * @return Coupon
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime 
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     * @return Coupon
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime 
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set discountValue
     *
     * @param integer $discountValue
     * @return Coupon
     */
    public function setDiscountValue($discountValue)
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    /**
     * Get discountValue
     *
     * @return integer 
     */
    public function getDiscountValue()
    {
        return $this->discountValue;
    }

    /**
     * Set discountType
     *
     * @param integer $discountType
     * @return Coupon
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;

        return $this;
    }

    /**
     * Get discountType
     *
     * @return integer 
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }
    
    /**
     * Set discountTargetType
     *
     * @param integer $discountTargetType
     * @return Coupon
     */
    public function setDiscountTargetType($discountTargetType)
    {
    	$this->discountTargetType = $discountTargetType;
    
    	return $this;
    }
    
    /**
     * Get discountTargetType
     *
     * @return integer
     */
    public function getDiscountTargetType()
    {
    	return $this->discountTargetType;
    }

    /**
     * Set combinedUseFlg
     *
     * @param integer $combinedUseFlg
     * @return Coupon
     */
    public function setCombinedUseFlg($combinedUseFlg)
    {
    	$this->combinedUseFlg = $combinedUseFlg;
    
    	return $this;
    }
    
    /**
     * Get combinedUseFlg
     *
     * @return integer
     */
    public function getCombinedUseFlg()
    {
    	return $this->combinedUseFlg;
    }
    
    /**
     * Set guestUseFlg
     *
     * @param integer $guestUseFlg
     * @return Coupon
     */
    public function setGuestUseFlg($guestUseFlg)
    {
    	$this->guestUseFlg = $guestUseFlg;
    
    	return $this;
    }
    
    /**
     * Get guestUseFlg
     *
     * @return integer
     */
    public function getGuestUseFlg()
    {
    	return $this->guestUseFlg;
    }
    
    /**
     * Set onetimeUseFlg
     *
     * @param integer $onetimeUseFlg
     * @return Coupon
     */
    public function setOnetimeUseFlg($onetimeUseFlg)
    {
    	$this->onetimeUseFlg = $onetimeUseFlg;
    
    	return $this;
    }
    
    /**
     * Get onetimeUseFlg
     *
     * @return integer
     */
    public function getOnetimeUseFlg()
    {
    	return $this->onetimeUseFlg;
    }
    
    /**
     * Set numberOfIssued
     *
     * @param integer $numberOfIssued
     * @return Coupon
     */
    public function setNumberOfIssued($numberOfIssued)
    {
    	$this->numberOfIssued = $numberOfIssued;
    
    	return $this;
    }
    
    /**
     * Get numberOfIssued
     *
     * @return integer
     */
    public function getNumberOfIssued()
    {
    	return $this->numberOfIssued;
    }
    
    /**
     * Set bottomPrice
     *
     * @param integer $bottomPrice
     * @return Coupon
     */
    public function setBottomPrice($bottomPrice)
    {
    	$this->bottomPrice = $bottomPrice;
    
    	return $this;
    }
    
    /**
     * Get bottomPrice
     *
     * @return integer
     */
    public function getBottomPrice()
    {
    	return $this->bottomPrice;
    }
    
    /**
     * Set conditionType
     *
     * @param integer $conditionType
     * @return Coupon
     */
    public function setConditionType($conditionType)
    {
    	$this->conditionType = $conditionType;
    
    	return $this;
    }
    
    /**
     * Get conditionType
     *
     * @return integer
     */
    public function getConditionType()
    {
    	return $this->conditionType;
    }

    /**
     * Set conditionActionType
     *
     * @param integer $conditionActionType
     * @return Coupon
     */
     public function setConditionActionType($conditionActionType)
     {
         $this->conditionActionType = $conditionActionType;
     
         return $this;
     }
     
     /**
      * Get conditionActionType
      *
      * @return integer
      */
     public function getConditionActionType()
     {
         return $this->conditionActionType;
     }
    
    /**
     * Set status
     *
     * @param integer $status
     * @return Coupon
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
     * Set delFlg
     *
     * @param integer $delFlg
     * @return Coupon
     */
    public function setDelFlg($delFlg)
    {
    	$this->delFlg = $delFlg;
    
    	return $this;
    }
    
    /**
     * Get delFlg
     *
     * @return integer
     */
    public function getDelFlg()
    {
    	return $this->delFlg;
    }
    
    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Coupon
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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Coupon
     */
    public function setUpdateDate($updateDate)
    {
    	$this->updateDate = $updateDate;
    
    	return $this;
    }
    
    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
    	return $this->updateDate;
    }
    
    /**
     * Set orderCount
     *
     * @param integer $orderCount
     * @return Coupon
     */
    public function setOrderCount($orderCount)
    {
    	$this->orderCount = $orderCount;
    
    	return $this;
    }
    
    /**
     * Get orderCount
     *
     * @return integer
     */
    public function getOrderCount()
    {
    	return $this->orderCount;
    }
}
