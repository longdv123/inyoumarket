<?php

namespace Plugin\BrandingContent\Entity;

class BrandingContent extends \Eccube\Entity\AbstractEntity
{
    private $id;

    private $client_logo;
    private $production_logo;
    private $production_link;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getClientLogo()
    {
        return $this->client_logo;
    }

    public function setClientLogo($client_logo)
    {
        $this->client_logo = $client_logo;

        return $this;
    }
    
    public function getProductionLogo()
    {
        return $this->production_logo;
    }

    public function setProductionLogo($production_logo)
    {
        $this->production_logo = $production_logo;

        return $this;
    }
    
    public function getProductionLink()
    {
        return $this->production_link;
    }

    public function setProductionLink($production_link)
    {
        $this->production_link = $production_link;

        return $this;
    }
}