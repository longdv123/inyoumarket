<?php

namespace Plugin\SimpleCoupon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 */
class Config extends \Eccube\Entity\AbstractEntity
{
	
	
	
	
    /**
     * @var integer
     */
    private $configId;    

    /**
     * @var integer
     */
    private $couponPaymentId;
    
    /**
     * @var \DateTime
     */
    private $createDate;
    
    /**
     * @var \DateTime
     */
    private $updateDate;
        
    
    /**
     * Get configId
     *
     * @return integer 
     */
    public function getConfigId()
    {
        return $this->configId;
    }
    
    /**
     * Set couponPaymentId
     *
     * @param integer $couponPaymentId
     * @return Config
     */
    public function setCouponPaymentId($couponPaymentId)
    {
        $this->couponPaymentId = $couponPaymentId;

        return $this;
    }

    /**
     * Get couponPaymentId
     *
     * @return integer 
     */
    public function getCouponPaymentId()
    {
        return $this->couponPaymentId;
    }
    
    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Config
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
     * @return Config
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
    
}
