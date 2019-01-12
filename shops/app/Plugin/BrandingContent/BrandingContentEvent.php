<?php

/*
 * This file is part of the BrandingContent
 *
 * Copyright (C) 2016 有限会社めがね部
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\BrandingContent;

use Eccube\Event\EventArgs;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BrandingContentEvent
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onAdminAdminLoginInitialize(FilterResponseEvent $event)
    {

	    
	    $request = $event->getRequest();
        $response = $event->getResponse();
        
       
        //$BrandingContent = new \Plugin\BrandingContent\Entity\BrandingContent();
        $BrandingContent = $this->app['branding_content.repository.branding_content']->find(1);
        
        
        if (is_null($BrandingContent)) {
		    $BrandingContent = new \Plugin\BrandingContent\Entity\BrandingContent();
		    $BrandingContent
	            ->setId(1)
	            ->setClientLogo('http://customize.systems/wp-content/uploads/2015/10/logo1.jpg')
	            ->setProductionLogo('http://meganebu.com/wp-content/themes/grandportfolio/images/logo@2x.png')
	            ->setProductionLink('http://meganebu.com/');
		
		    // DB更新
		    $app['orm.em']->persist($BrandingContent);
		    $app['orm.em']->flush($BrandingContent);
		        
		    $BrandingContent = $app['branding_content.repository.branding_content']->find(1);
		}
        
        
        $clientLogo = ($BrandingContent->getClientLogo()) ? $BrandingContent->getClientLogo() : 'http://customize.systems/wp-content/uploads/2015/10/logo1.jpg';
        $productionLink = ($BrandingContent->getProductionLink()) ? $BrandingContent->getProductionLink() : 'http://meganebu.com/';
        $productionLogo = ($BrandingContent->getProductionLogo()) ? $BrandingContent->getProductionLogo() : 'http://meganebu.com/wp-content/themes/grandportfolio/images/logo@2x.png';
        
         
        // 書き換えhtmlの初期化
        $html = $response->getContent();
		
		$crawler = new Crawler($html);
		$oldElement = $crawler->filter('.login-logo2');
        $oldHtml = $oldElement->html();
        $newHtml = '<img src="'.$clientLogo.'" width="100%">';
        $html = $crawler->html();
        $html = str_replace($oldHtml, $newHtml, $html);
        
        

        $crawler = new Crawler($html);
		$oldElement = $crawler->filter('#wrapper');
		$oldHtml = $oldElement->html();
        $newHtml = $oldHtml;
        $newHtml .= '<a href="'.$productionLink.'" target="_blank"><img src="'.$productionLogo.'" style="position: absolute;bottom: 10px;right: 80px;"></a>';
        $newHtml .= '<a href="https://www.ec-cube.net" target="_blank"><img src="/shops/html/template/admin/assets/img/logo2.png" width="60" style="position: absolute;bottom: 10px;right: 10px;"></a>';
        $html = $crawler->html();
        $html = str_replace($oldHtml, $newHtml, $html);

		
        $response->setContent($html);
        $event->setResponse($response);

        
    }


}
