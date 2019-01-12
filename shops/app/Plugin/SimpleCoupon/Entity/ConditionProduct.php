<?php

namespace Plugin\SimpleCoupon\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConditionProduct
 */
class ConditionProduct extends \Eccube\Entity\AbstractEntity
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
    private $productId;

    /**
     * @var integer
     */
     private $productClassId;

     /**
     * @var integer
     */
    private $categoryId;
    
    /**
     * @var \Eccube\Entity\Product
     */
    private $Product;

    /**
     * @var \Eccube\Entity\ProductClass
     */
     private $ProductClass;

     /**
     * @var \Eccube\Entity\Category
     */
    private $Category;
    

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
     * @return ConditionProduct
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
     * Set productId
     *
     * @param string $productId
     * @return ConditionProduct
     */
    public function setProductId($productId)
    {
    	$this->productId = $productId;
    
    	return $this;
    }
    
    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    /**
     * Set productClassId
     *
     * @param string $productClassId
     * @return ConditionProduct
     */
     public function setProductClassId($productClassId)
     {
         $this->productClassId = $productClassId;
     
         return $this;
     }
     
     /**
     * Get productClassId
     *
     * @return integer
     */
    public function getProductClassId()
    {
    	return $this->productClassId;
    }

    /**
     * Set categoryId
     *
     * @param string $categoryId
     * @return ConditionProduct
     */
     public function setCategoryId($categoryId)
     {
         $this->categoryId = $categoryId;
     
         return $this;
     }
     
     /**
      * Get categoryId
      *
      * @return integer
      */
     public function getCategoryId()
     {
         return $this->categoryId;
     }

    /**
     * Set Product
     *
     * @param \Eccube\Entity\Product $Product
     * @return ConditionProduct
     */
    public function setProduct(\Eccube\Entity\Product $Product = null)
    {
    	$this->Product = $Product;
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

    /**
     * Set ProductClass
     *
     * @param \Eccube\Entity\ProductClass $ProductClass
     * @return ConditionProduct
     */
     public function setProductClass(\Eccube\Entity\ProductClass $ProductClass = null)
     {
         $this->ProductClass = $ProductClass;
         return $this;
     }
     
     /**
      * Get ProductClass
      *
      * @return \Eccube\Entity\ProductClass
      */
     public function getProductClass()
     {
         return $this->ProductClass;
     }
    
    /**
     * Set Category
     *
     * @param \Eccube\Entity\Category $Category
     * @return ConditionProduct
     */
     public function setCategory(\Eccube\Entity\Category $Category = null)
     {
         $this->Category = $Category;
         return $this;
     }
     
     /**
      * Get Category
      *
      * @return \Eccube\Entity\Category
      */
     public function getCategory()
     {
         return $this->Category;
     }
 
}
