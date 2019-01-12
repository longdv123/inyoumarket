<?php

/* __string_template__b0f7143ed55ded5c63b158f8a388e5583fbb89f0939e13207d8ef271122eba64 */
class __TwigTemplate_19c3482b4d290ac62dfd90d6669582fb847145285717d364940d4af30ce5df8c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 25
        $this->parent = $this->loadTemplate("default_frame.twig", "__string_template__b0f7143ed55ded5c63b158f8a388e5583fbb89f0939e13207d8ef271122eba64", 25);
        $this->blocks = array(
            'javascript' => array($this, 'block_javascript'),
            'main' => array($this, 'block_main'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "default_frame.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 27
        $context["body_class"] = "front_page";
        // line 25
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 29
    public function block_javascript($context, array $blocks = array())
    {
        // line 30
        echo "<script>
    \$(function () {
        \$('.slide-bottom').matchHeight();
    });
</script>

";
    }

    // line 38
    public function block_main($context, array $blocks = array())
    {
        // line 39
        echo "    
";
    }

    public function getTemplateName()
    {
        return "__string_template__b0f7143ed55ded5c63b158f8a388e5583fbb89f0939e13207d8ef271122eba64";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 39,  45 => 38,  35 => 30,  32 => 29,  28 => 25,  26 => 27,  11 => 25,);
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
/* */
/* */
/*                */
/* {% extends 'default_frame.twig' %}*/
/* */
/* {% set body_class = 'front_page' %}*/
/* */
/* {% block javascript %}*/
/* <script>*/
/*     $(function () {*/
/*         $('.slide-bottom').matchHeight();*/
/*     });*/
/* </script>*/
/* */
/* {% endblock %}*/
/* */
/* {% block main %}*/
/*     */
/* {% endblock %}*/
