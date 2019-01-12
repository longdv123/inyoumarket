<?php

namespace Plugin\DPost\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerEx
 */
class CustomerEx extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $customerExId;

    /**
     * @var integer
     */
    private $customer_id;

    /**
     * @var string
     */
    private $regist_id;


    /**
     * Get customerExId
     *
     * @return integer 
     */
    public function getCustomerExId()
    {
        return $this->customerExId;
    }

    /**
     * Set customer_id
     *
     * @param integer $customerId
     * @return CustomerEx
     */
    public function setCustomerId($customerId)
    {
        $this->customer_id = $customerId;

        return $this;
    }

    /**
     * Get customer_id
     *
     * @return integer 
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Set regist_id
     *
     * @param string $registId
     * @return CustomerEx
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
}
