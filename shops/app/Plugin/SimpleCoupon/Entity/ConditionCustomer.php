<?php

namespace Plugin\SimpleCoupon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConditionCustomer
 */
class ConditionCustomer extends \Eccube\Entity\AbstractEntity
{
	
	/**
	 * @var integer
	 */
	private $conditionId;
	
    /**
     * @var integer
     */
    private $couponId;

    /**
     * @var integer
     */
    private $customerId;
    
    /**
     * @var \Eccube\Entity\Customer
     */
    private $Customer;
    

    /**
     * Get conditionId
     *
     * @return integer
     */
    public function getConditionId()
    {
    	return $this->conditionId;
    }
    
    /**
     * Set couponId
     *
     * @param string $couponId
     * @return ConditionCustomer
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
     * Set customerId
     *
     * @param string $customerId
     * @return ConditionCustomer
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
     * Set Customer
     *
     * @param \Eccube\Entity\Customer $Customer
     * @return ConditionCustomer
     */
    public function setCustomer(\Eccube\Entity\Customer $Customer = null)
    {
    	$this->Customer = $Customer;
    	return $this;
    }
    
    /**
     * Get Customer
     *
     * @return \Eccube\Entity\Customer
     */
    public function getCustomer()
    {
    	return $this->Customer;
    }
    
}
