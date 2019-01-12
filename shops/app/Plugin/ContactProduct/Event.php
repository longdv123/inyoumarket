<?php
/*
* Plugin Name : ContactProduct
*
* Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
* http://www.bratech.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ContactProduct;

use Eccube\Event\RenderEvent;
use Eccube\Common\Constant;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class Event
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function insertContactButton(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();
        $html = $response->getContent();
        
        $product_id = $request->get('id');
        
        $crawler = new Crawler($html);
        
        $oldCrawler = $crawler
            ->filter('form#form1')
            ->eq(0);
        $html = $this->getHtml($crawler);
        $oldHtml = '';
        $newHtml = '';
        
        if (count($oldCrawler) > 0) {
            $oldHtml = $oldCrawler->html() . '</form>';
            $oldHtml = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');
            
            $twig = $app->renderView(
                'ContactProduct/Resource/template/default/Product/contact_button.twig',
                array(
                    'product_id' => $product_id,
                    )
            );
            
            $newHtml = $oldHtml.$twig;
        }

        $html = str_replace($oldHtml, $newHtml, $html);
        
        $response->setContent($html);
        $event->setResponse($response);
    }
    
    public function setContents(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $app['request'];
        if(strlen($request->get('contents')) > 0)return;
        
        $product_id = $request->get('product_id');
        if(!isset($product_id))return;
        if(empty($product_id))return;
        
        $response = $event->getResponse();
        $html = $response->getContent();
        
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . mb_convert_encoding($html,'HTML-ENTITIES', 'UTF-8'));
        $dom->encoding = "UTF-8";
        $dom->formatOutput = true;

        $formElement = $dom->getElementById('contact_contents');
        if($formElement){
            $Product = $app['eccube.repository.product']->find($product_id);
            $formElement->nodeValue = '[商品ID:'. $Product->getId(). ']' .$Product->getName() . "についての問い合わせ\n";
        }

        
        $response->setContent(mb_convert_encoding($dom->saveHTML(), 'UTF-8', 'HTML-ENTITIES'));
        $event->setResponse($response);

    }

    private function getHtml(Crawler $crawler)
    {
        $html = '';
        foreach ($crawler as $domElement) {
            $domElement->ownerDocument->formatOutput = true;
            $html .= $domElement->ownerDocument->saveHTML();
        }
        return html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
    }
}