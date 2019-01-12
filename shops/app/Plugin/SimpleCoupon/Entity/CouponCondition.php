<?php

namespace Plugin\SimpleCoupon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CouponCondition
 */
class CouponCondition extends \Eccube\Entity\AbstractEntity
{
	
	
    /**
     * @var integer
     */
    private $couponId;

    /**
     * @var string
     */
    private $couponCode;

    /**
     * @var integer
     */
    private $conditionType;

    /**
     * @var integer
     */
     private $conditionActionType;
    
    /**
     * @var string
     */
    private $targetCustomer;

    /**
     * @var string
     */
    private $targetProduct;
    
    /**
     * @var string
     */
    private $targetCategory;


    /**
     * Set couponId
     *
     * @param string $couponId
     * @return CouponCondition
     */
    public function setCouponId($couponId)
    {
    	$this->couponId = $couponId;
    
    	return $this;
    }
    
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
     * Set couponCode
     *
     * @param string $couponCode
     * @return CouponCondition
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
     * Set conditionType
     *
     * @param integer $conditionType
     * @return CoupCouponConditionon
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
     * Set targetCustomer
     *
     * @param string $targetCustomer
     * @return CouponCondition
     */
    public function setTargetCustomer($targetCustomer)
    {
    	$this->targetCustomer = $targetCustomer;
    
    	return $this;
    }
    
    /**
     * Get targetCustomer
     *
     * @return string
     */
    public function getTargetCustomer()
    {
    	return $this->targetCustomer;
    }

    /**
     * Set targetProduct
     *
     * @param string $targetProduct
     * @return CouponCondition
     */
     public function setTargetProduct($targetProduct)
     {
         $this->targetProduct = $targetProduct;
     
         return $this;
     }
     
     /**
      * Get targetProduct
      *
      * @return string
      */
     public function getTargetProduct()
     {
         return $this->targetProduct;
     }

     /**
     * Set targetCategory
     *
     * @param string $targetCategory
     * @return CouponCondition
     */
    public function setTargetCategory($targetCategory)
    {
    	$this->targetCategory = $targetCategory;
    
    	return $this;
    }
    
    /**
     * Get targetCategory
     *
     * @return string
     */
    public function getTargetCategory()
    {
    	return $this->targetCategory;
    }
    
    
}
