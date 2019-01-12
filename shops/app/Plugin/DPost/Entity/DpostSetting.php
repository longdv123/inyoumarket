<?php

namespace Plugin\DPost\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DpostSetting
 */
class DpostSetting extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $project_id;

    /**
     * @var string
     */
    private $api_key;

    /**
     * @var integer
     */
    private $product_flg;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set project_id
     *
     * @param string $projectId
     * @return DpostSetting
     */
    public function setProjectId($projectId)
    {
        $this->project_id = $projectId;

        return $this;
    }

    /**
     * Get project_id
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * Set api_key
     *
     * @param string $apiKey
     * @return DpostSetting
     */
    public function setApiKey($apiKey)
    {
        $this->api_key = $apiKey;

        return $this;
    }

    /**
     * Get api_key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Set product_flg
     *
     * @param integer $productFlg
     * @return DpostSetting
     */
    public function setProductFlg($productFlg)
    {
        $this->product_flg = $productFlg;

        return $this;
    }

    /**
     * Get product_flg
     *
     * @return integer
     */
    public function getProductFlg()
    {
        return $this->product_flg;
    }
}
