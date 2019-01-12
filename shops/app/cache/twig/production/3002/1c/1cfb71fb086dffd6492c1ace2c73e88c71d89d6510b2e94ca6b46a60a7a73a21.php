<?php

/* Block/logo.twig */
class __TwigTemplate_909125360125481bde3707e613ba893a2c0694da31043a57e40cb21bb22da29a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 22
        echo "             <div class=\"header_logo_area\">
                <p class=\"copy\">Organic Life to all the people.<br>すべての人にオーガニックな暮らしを。 </p>
                <h1 class=\"header_logo\"><a href=\"";
        // line 24
        echo $this->env->getExtension('eccube')->getUrl("homepage");
        echo "\">
                \t";
        // line 26
        echo "                \t<img src=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/img/common/logo.png\">
                </a></h1>
            </div>
";
    }

    public function getTemplateName()
    {
        return "Block/logo.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  27 => 26,  23 => 24,  19 => 22,);
    }
}
/* {#*/
/* This file is part of EC-CUBE*/
/* */
/* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.*/
/* */
/* http://www.lockon.co.jp/*/
/* */
/* This program is free software; you can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License*/
/* as published by the Free Software Foundation; either version 2*/
/* of the License, or (at your option) any later version.*/
/* */
/* This program is distributed in the hope that it will be useful,*/
/* but WITHOUT ANY WARRANTY; without even the implied warranty of*/
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the*/
/* GNU General Public License for more details.*/
/* */
/* You should have received a copy of the GNU General Public License*/
/* along with this program; if not, write to the Free Software*/
/* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.*/
/* #}*/
/*              <div class="header_logo_area">*/
/*                 <p class="copy">Organic Life to all the people.<br>すべての人にオーガニックな暮らしを。 </p>*/
/*                 <h1 class="header_logo"><a href="{{ url('homepage') }}">*/
/*                 	{# {{ BaseInfo.shop_name }} #}*/
/*                 	<img src="{{ app.config.front_urlpath }}/img/common/logo.png">*/
/*                 </a></h1>*/
/*             </div>*/
/* */
