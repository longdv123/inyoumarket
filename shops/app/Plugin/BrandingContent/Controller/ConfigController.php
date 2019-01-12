<?php

/*
 * This file is part of the BrandingContent
 *
 * Copyright (C) 2016 有限会社めがね部
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\BrandingContent\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ConfigController
{

    /**
     * BrandingContent用設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        $form = $app['form.factory']->createBuilder('brandingcontent_config')->getForm();
		
		$BrandingContent = $app['branding_content.repository.branding_content']->find(1);
		
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
		       
		
		$form->get('client_logo')->setData($BrandingContent->getClientLogo());
		$form->get('production_logo')->setData($BrandingContent->getProductionLogo());
		$form->get('production_link')->setData($BrandingContent->getProductionLink());
		
		
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                
                //$BrandingContent = $app['branding_content.repository.branding_content']->find(1);
		        if (is_null($BrandingContent)) {
		            $BrandingContent = new \Plugin\BrandingContent\Entity\BrandingContent();
		        }
				
                // add code...
                // エンティティを更新
		        $BrandingContent
		            ->setId(1)
		            ->setClientLogo($data['client_logo'])
		            ->setProductionLogo($data['production_logo'])
		            ->setProductionLink($data['production_link']);
		
		        // DB更新
		        $app['orm.em']->persist($BrandingContent);
		        $app['orm.em']->flush($BrandingContent);
            }
        }

        return $app->render('BrandingContent/Resource/template/admin/config.twig', array(
            'form' => $form->createView(),
        ));
    }

}
