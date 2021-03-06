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

class BrandingContentController
{

    /**
     * BrandingContent画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // add code...

        return $app->render('BrandingContent/Resource/template/index.twig', array(
            // add parameter...
        ));
    }

}
