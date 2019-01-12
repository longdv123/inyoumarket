<?php

/* __string_template__53a9aac114a6d775f090c0062a9d9d923ba7cf6212fb9b19850d606c2e52ca06 */
class __TwigTemplate_9f5dd8c56f754639edabd37af7560f67e9d4df83901296c282abb6bcfa96bb81 extends Twig_Template
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
        // line 1
        echo "<!doctype html>
";
        // line 23
        echo "<html lang=\"ja\">
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <title>";
        // line 27
        echo twig_escape_filter($this->env, (isset($context["error_title"]) ? $context["error_title"] : null), "html", null, true);
        echo "</title>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <link rel=\"icon\" href=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/img/common/favicon.ico\">
    <link rel=\"stylesheet\" href=\"";
        // line 30
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/css/style.css?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\">
    <link rel=\"stylesheet\" href=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/css/default.css?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"><!-- for original theme CSS -->

</head>
<body>
<div id=\"wrapper\" class=\"error_page\">
    <div id=\"\" class=\"theme_main_only\">
        <div class=\"container-fluid inner\">
            <div id=\"\">
                <div id=\"default_error_wrap\" class=\"container-fluid\">
                    <div id=\"default_error_box\" class=\"message_box\">
                        <div id=\"default_error_box__body\" class=\"row no-padding\">
                            <div id=\"default_error__message\" class=\"col-sm-6 col-sm-offset-3\">
                                <div class=\"icon\"><svg class=\"cb cb-warning\"><use xlink:href=\"#cb-warning\" /></svg></div>
                                <h1>";
        // line 44
        echo twig_escape_filter($this->env, (isset($context["error_title"]) ? $context["error_title"] : null), "html", null, true);
        echo "</h1>
                                <p>";
        // line 45
        echo twig_escape_filter($this->env, (isset($context["error_message"]) ? $context["error_message"] : null), "html", null, true);
        echo "</p>
                            </div>
                        </div>
                        <div id=\"default_error__footer\" class=\"row\">
                            <div id=\"default_error__top_button\" class=\"btn_group col-sm-offset-4 col-sm-4\">
                                <a href=\"";
        // line 50
        echo $this->env->getExtension('eccube')->getUrl("homepage");
        echo "\"><p><button type=\"button\" class=\"btn btn-info btn-block\">トップページへ</button></p></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js\"></script>
<script>window.jQuery || document.write('<script src=\"";
        // line 61
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/vendor/jquery-1.11.3.min.js?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"><\\/script>')</script>
<script src=\"";
        // line 62
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/function.js?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"></script>
<script>
    \$(function () {
        \$.ajax({
            url: '";
        // line 66
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/img/common/svg.html',
            type: 'GET',
            dataType: 'html',
        }).done(function(data){
            \$('body').prepend(data);
        }).fail(function(data){
        });
    });
</script>

</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "__string_template__53a9aac114a6d775f090c0062a9d9d923ba7cf6212fb9b19850d606c2e52ca06";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 66,  93 => 62,  87 => 61,  73 => 50,  65 => 45,  61 => 44,  43 => 31,  37 => 30,  33 => 29,  28 => 27,  22 => 23,  19 => 1,);
    }
}
/* <!doctype html>*/
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
/* <html lang="ja">*/
/* <head>*/
/*     <meta charset="utf-8">*/
/*     <meta http-equiv="X-UA-Compatible" content="IE=edge">*/
/*     <title>{{ error_title }}</title>*/
/*     <meta name="viewport" content="width=device-width, initial-scale=1">*/
/*     <link rel="icon" href="{{ app.config.front_urlpath }}/img/common/favicon.ico">*/
/*     <link rel="stylesheet" href="{{ app.config.front_urlpath }}/css/style.css?v={{ constant('Eccube\\Common\\Constant::VERSION') }}">*/
/*     <link rel="stylesheet" href="{{ app.config.front_urlpath }}/css/default.css?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"><!-- for original theme CSS -->*/
/* */
/* </head>*/
/* <body>*/
/* <div id="wrapper" class="error_page">*/
/*     <div id="" class="theme_main_only">*/
/*         <div class="container-fluid inner">*/
/*             <div id="">*/
/*                 <div id="default_error_wrap" class="container-fluid">*/
/*                     <div id="default_error_box" class="message_box">*/
/*                         <div id="default_error_box__body" class="row no-padding">*/
/*                             <div id="default_error__message" class="col-sm-6 col-sm-offset-3">*/
/*                                 <div class="icon"><svg class="cb cb-warning"><use xlink:href="#cb-warning" /></svg></div>*/
/*                                 <h1>{{ error_title }}</h1>*/
/*                                 <p>{{ error_message }}</p>*/
/*                             </div>*/
/*                         </div>*/
/*                         <div id="default_error__footer" class="row">*/
/*                             <div id="default_error__top_button" class="btn_group col-sm-offset-4 col-sm-4">*/
/*                                 <a href="{{ url('homepage') }}"><p><button type="button" class="btn btn-info btn-block">トップページへ</button></p></a>*/
/*                             </div>*/
/*                         </div>*/
/*                     </div>*/
/*                 </div>*/
/*             </div>*/
/*         </div>*/
/*     </div>*/
/* </div>*/
/* */
/* <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>*/
/* <script>window.jQuery || document.write('<script src="{{ app.config.front_urlpath }}/js/vendor/jquery-1.11.3.min.js?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"><\/script>')</script>*/
/* <script src="{{ app.config.front_urlpath }}/js/function.js?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"></script>*/
/* <script>*/
/*     $(function () {*/
/*         $.ajax({*/
/*             url: '{{ app.config.front_urlpath }}/img/common/svg.html',*/
/*             type: 'GET',*/
/*             dataType: 'html',*/
/*         }).done(function(data){*/
/*             $('body').prepend(data);*/
/*         }).fail(function(data){*/
/*         });*/
/*     });*/
/* </script>*/
/* */
/* </body>*/
/* </html>*/
/* */
