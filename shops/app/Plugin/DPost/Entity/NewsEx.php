<?php

namespace Plugin\DPost\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsEx
 */
class NewsEx extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $newsExId;

    /**
     * @var integer
     */
    private $news_id;

    /**
     * @var integer
     */
    private $push_status;

    /**
     * @var integer
     */
    private $push_count;

    /**
     * @var integer
     */
    private $click;

    /**
     * @var integer
     */
    private $end_number;

    /**
     * @var \Eccube\Entity\News
     */
    private $News;


    /**
     * Get newsExId
     *
     * @return integer
     */
    public function getNewsExId()
    {
        return $this->newsExId;
    }

    /**
     * Set news_id
     *
     * @param integer $newsId
     * @return NewsEx
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
     * Set push_status
     *
     * @param integer $pushStatus
     * @return NewsEx
     */
    public function setPushStatus($pushStatus)
    {
        $this->push_status = $pushStatus;

        return $this;
    }

    /**
     * Get push_status
     *
     * @return integer
     */
    public function getPushStatus()
    {
        return $this->push_status;
    }

    /**
     * Set push_count
     *
     * @param integer $pushCount
     * @return NewsEx
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
     * @return NewsEx
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
     * Set end_number
     *
     * @param integer $endNumber
     * @return NewsEx
     */
    public function setEndNumber($endNumber)
    {
        $this->end_number = $endNumber;

        return $this;
    }

    /**
     * Get end_number
     *
     * @return integer
     */
    public function getEndNumber()
    {
        return $this->end_number;
    }

    /**
     * Set News
     *
     * @param \Eccube\Entity\News $news
     * @return NewsEx
     */
    public function setNews(\Eccube\Entity\News $news = null)
    {
        $this->News = $news;

        return $this;
    }

    /**
     * Get News
     *
     * @return \Eccube\Entity\News
     */
    public function getNews()
    {
        return $this->News;
    }
}
