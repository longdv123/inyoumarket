<?php

namespace Plugin\DPost\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostQueue
 */
class PostQueue extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $post_id;

    /**
     * @var string
     */
    private $regist_id;

    /**
     * @var integer
     */
    private $news_id;

    /**
     * @var integer
     */
    private $customer_id;

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
     * Get post_id
     *
     * @return integer 
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * Set regist_id
     *
     * @param string $registId
     * @return PostQueue
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
     * Set news_id
     *
     * @param integer $newsId
     * @return PostQueue
     */
    public function setNewsId($newsId)
    {
        $this->news_id = $newsId;

        return $this;
    }

    /**
     * Get news_id
     *
     * @return integer 
     */
    public function getNewsId()
    {
        return $this->news_id;
    }

    /**
     * Set customer_id
     *
     * @param integer $customerId
     * @return PostQueue
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
     * Set queue_status
     *
     * @param integer $queueStatus
     * @return PostQueue
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
     * @return PostQueue
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
     * @return PostQueue
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
}
