<?php

/* default_frame.twig */
class __TwigTemplate_39de925584b13f882a2ec4440c69c15802436e6414a4ae785d2364377e2e0ba2 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'stylesheet' => array($this, 'block_stylesheet'),
            'main' => array($this, 'block_main'),
            'javascript' => array($this, 'block_javascript'),
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
        if ((array_key_exists("subtitle", $context) &&  !twig_test_empty((isset($context["subtitle"]) ? $context["subtitle"] : null)))) {
            echo twig_escape_filter($this->env, (isset($context["subtitle"]) ? $context["subtitle"] : null), "html", null, true);
            echo " / ";
        } elseif ((array_key_exists("title", $context) &&  !twig_test_empty((isset($context["title"]) ? $context["title"] : null)))) {
            echo twig_escape_filter($this->env, (isset($context["title"]) ? $context["title"] : null), "html", null, true);
            echo " / ";
        }
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["BaseInfo"]) ? $context["BaseInfo"] : null), "shop_name", array()), "html", null, true);
        echo "</title>
    ";
        // line 28
        if ( !twig_test_empty($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "author", array()))) {
            // line 29
            echo "        <meta name=\"author\" content=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "author", array()), "html", null, true);
            echo "\">
    ";
        }
        // line 31
        echo "    ";
        if ( !twig_test_empty($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "description", array()))) {
            // line 32
            echo "        <meta name=\"description\" content=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "description", array()), "html", null, true);
            echo "\">
    ";
        }
        // line 34
        echo "    ";
        if ( !twig_test_empty($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "keyword", array()))) {
            // line 35
            echo "        <meta name=\"keywords\" content=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "keyword", array()), "html", null, true);
            echo "\">
    ";
        }
        // line 37
        echo "    ";
        if ( !twig_test_empty($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "meta_robots", array()))) {
            // line 38
            echo "        <meta name=\"robots\" content=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "meta_robots", array()), "html", null, true);
            echo "\">
    ";
        }
        // line 40
        echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <link rel=\"icon\" href=\"";
        // line 41
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/img/common/favicon.ico\">
    <link rel=\"stylesheet\" href=\"";
        // line 42
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/css/style.css?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\">
    <link rel=\"stylesheet\" href=\"";
        // line 43
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/css/slick.css?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\">
    <link rel=\"stylesheet\" href=\"";
        // line 44
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/css/slick-theme.css?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\">
    <link rel=\"stylesheet\" href=\"";
        // line 45
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/css/default.css?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\">
    <!-- for original theme CSS -->
    ";
        // line 47
        $this->displayBlock('stylesheet', $context, $blocks);
        // line 48
        echo "
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js\"></script>
        <script>window.jQuery || document.write('<script src=\"";
        // line 50
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/vendor/jquery-1.11.3.min.js?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"><\\/script>')</script>

        ";
        // line 53
        echo "        ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "Head", array())) {
            // line 54
            echo "            ";
            // line 55
            echo "            ";
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "Head", array())));
            echo "
            ";
            // line 57
            echo "        ";
        }
        // line 58
        echo "        ";
        // line 59
        echo "
    </head>
    <body id=\"page_";
        // line 61
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "_route"), "method"), "html", null, true);
        echo "\" class=\"";
        echo twig_escape_filter($this->env, ((array_key_exists("body_class", $context)) ? (_twig_default_filter((isset($context["body_class"]) ? $context["body_class"] : null), "other_page")) : ("other_page")), "html", null, true);
        echo "\">
        <div id=\"wrapper\">
            <header id=\"header\">
                <div class=\"container-fluid inner\">
                    ";
        // line 66
        echo "                    ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "Header", array())) {
            // line 67
            echo "                        ";
            // line 68
            echo "                        ";
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "Header", array())));
            echo "
                        ";
            // line 70
            echo "                    ";
        }
        // line 71
        echo "                    ";
        // line 72
        echo "                    <p id=\"btn_menu\"><a class=\"nav-trigger\" href=\"#nav\">Menu<span></span></a></p>
                </div>
                <!-- ▼default_frame.twigに記入 -->
                <nav class=\"header_nav pc\">
                    <ul>
                        <li><a href=\"";
        // line 77
        echo $this->env->getExtension('eccube')->getUrl("homepage");
        echo "\">HOME</a></li>
                        <li><a href=\"";
        // line 78
        echo $this->env->getExtension('eccube')->getUrl("product_list");
        echo "\">GOODS</a></li>
\t\t\t\t\t\t<li><a href=\"http://inyoumarket.com/category/%e5%87%ba%e5%93%81%e8%80%85%e3%81%ae%e5%a3%b0/\">出品者の声</a></li>
                        <li><a href=\"http://inyoumarket.com/shops/user_data/about\">IN YOU Marketについて</a></li>
                        <li><a href=\"";
        // line 81
        echo $this->env->getExtension('eccube')->getUrl("contact");
        echo "\">CONTACT US</a></li>
                    </ul>
                </nav>
                <!-- ▲default_frame.twigに記入 -->
            </header>
            <p id=\"page-top\"><a href=\"#header\">
               <img src=\"";
        // line 87
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/img/common/pagetop.png\" alt=\"PAGE TOP\">
           </a></p>

           <div id=\"contents\" class=\"";
        // line 90
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "theme", array()), "html", null, true);
        echo "\">

            <div id=\"contents_top\">
                ";
        // line 94
        echo "                ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "ContentsTop", array())) {
            // line 95
            echo "                    ";
            // line 96
            echo "                    ";
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "ContentsTop", array())));
            echo "
                    ";
            // line 98
            echo "                ";
        }
        // line 99
        echo "                ";
        // line 100
        echo "            </div>

            <div class=\"container-fluid inner\">
               
                    ";
        // line 105
        echo "                    ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "SideLeft", array())) {
            // line 106
            echo "                        <div id=\"side_left\" class=\"side";
            if (($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "url", array()) == "homepage")) {
                echo " sp";
            }
            echo "\">
                            ";
            // line 108
            echo "                            ";
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "SideLeft", array())));
            echo "
                            ";
            // line 110
            echo "                        </div>
                    ";
        }
        // line 112
        echo "                    ";
        // line 113
        echo "               

                <div id=\"main\">
                    ";
        // line 117
        echo "                    ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "MainTop", array())) {
            // line 118
            echo "                        <div id=\"main_top\">
                            ";
            // line 119
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "MainTop", array())));
            echo "
                        </div>
                    ";
        }
        // line 122
        echo "                    ";
        // line 123
        echo "
                    <div id=\"main_middle\">
                        ";
        // line 125
        $this->displayBlock('main', $context, $blocks);
        // line 126
        echo "                        </div>

                        ";
        // line 129
        echo "                        ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "MainBottom", array())) {
            // line 130
            echo "                            <div id=\"main_bottom\">
                                ";
            // line 131
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "MainBottom", array())));
            echo "
                            </div>
                        ";
        }
        // line 134
        echo "                        ";
        // line 135
        echo "                    </div><!--#main-->

                    ";
        // line 138
        echo "                    ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "SideRight", array())) {
            // line 139
            echo "                        <div id=\"side_right\" class=\"side\">
                            ";
            // line 141
            echo "                            ";
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "SideRight", array())));
            echo "
                            ";
            // line 143
            echo "                        </div>
                    ";
        }
        // line 145
        echo "                    ";
        // line 146
        echo "
                    ";
        // line 148
        echo "                    ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "ContentsBottom", array())) {
            // line 149
            echo "                        <div id=\"contents_bottom\">
                            ";
            // line 151
            echo "                            ";
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "ContentsBottom", array())));
            echo "
                            ";
            // line 153
            echo "                        </div>
                    ";
        }
        // line 155
        echo "                    ";
        // line 156
        echo "
            </div>

            <footer id=\"footer\">
                ";
        // line 161
        echo "                ";
        if ($this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "Footer", array())) {
            // line 162
            echo "                    ";
            // line 163
            echo "                    ";
            echo twig_include($this->env, $context, "block.twig", array("Blocks" => $this->getAttribute((isset($context["PageLayout"]) ? $context["PageLayout"] : null), "Footer", array())));
            echo "
                    ";
            // line 165
            echo "                ";
        }
        // line 166
        echo "                ";
        // line 167
        echo "            </footer>
            
        </div>
        <div id=\"drawer\" class=\"drawer sp\">
            </div>
    </div>
       
        <div class=\"overlay\"></div>

        <script src=\"";
        // line 176
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/vendor/bootstrap.custom.min.js?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 177
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/vendor/slick.min.js?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 178
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/function.js?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"></script>
        <script src=\"";
        // line 179
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/eccube.js?v=";
        echo twig_escape_filter($this->env, twig_constant("Eccube\\Common\\Constant::VERSION"), "html", null, true);
        echo "\"></script>
        <script>
            \$(function () {
                \$('#drawer').append(\$('.drawer_block').clone(true).children());
                \$.ajax({
                    url: '";
        // line 184
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
        <script type=\"text/javascript\" src=\"";
        // line 193
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/jquery.matchHeight.js\"></script>
        <script type=\"text/javascript\" src=\"";
        // line 194
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "front_urlpath", array()), "html", null, true);
        echo "/js/jquery.fadethis.min.js\"></script>
        ";
        // line 195
        $this->displayBlock('javascript', $context, $blocks);
        // line 196
        echo "        </body>
        </html>
";
    }

    // line 47
    public function block_stylesheet($context, array $blocks = array())
    {
    }

    // line 125
    public function block_main($context, array $blocks = array())
    {
    }

    // line 195
    public function block_javascript($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "default_frame.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  427 => 195,  422 => 125,  417 => 47,  411 => 196,  409 => 195,  405 => 194,  401 => 193,  389 => 184,  379 => 179,  373 => 178,  367 => 177,  361 => 176,  350 => 167,  348 => 166,  345 => 165,  340 => 163,  338 => 162,  335 => 161,  329 => 156,  327 => 155,  323 => 153,  318 => 151,  315 => 149,  312 => 148,  309 => 146,  307 => 145,  303 => 143,  298 => 141,  295 => 139,  292 => 138,  288 => 135,  286 => 134,  280 => 131,  277 => 130,  274 => 129,  270 => 126,  268 => 125,  264 => 123,  262 => 122,  256 => 119,  253 => 118,  250 => 117,  245 => 113,  243 => 112,  239 => 110,  234 => 108,  227 => 106,  224 => 105,  218 => 100,  216 => 99,  213 => 98,  208 => 96,  206 => 95,  203 => 94,  197 => 90,  191 => 87,  182 => 81,  176 => 78,  172 => 77,  165 => 72,  163 => 71,  160 => 70,  155 => 68,  153 => 67,  150 => 66,  141 => 61,  137 => 59,  135 => 58,  132 => 57,  127 => 55,  125 => 54,  122 => 53,  115 => 50,  111 => 48,  109 => 47,  102 => 45,  96 => 44,  90 => 43,  84 => 42,  80 => 41,  77 => 40,  71 => 38,  68 => 37,  62 => 35,  59 => 34,  53 => 32,  50 => 31,  44 => 29,  42 => 28,  31 => 27,  25 => 23,  22 => 1,);
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
/*     <title>{% if subtitle is defined and subtitle is not empty %}{{ subtitle }} / {% elseif title is defined and title is not empty %}{{ title }} / {% endif %}{{ BaseInfo.shop_name }}</title>*/
/*     {% if PageLayout.author is not empty %}*/
/*         <meta name="author" content="{{ PageLayout.author }}">*/
/*     {% endif %}*/
/*     {% if PageLayout.description is not empty %}*/
/*         <meta name="description" content="{{ PageLayout.description }}">*/
/*     {% endif %}*/
/*     {% if PageLayout.keyword is not empty %}*/
/*         <meta name="keywords" content="{{ PageLayout.keyword }}">*/
/*     {% endif %}*/
/*     {% if PageLayout.meta_robots is not empty %}*/
/*         <meta name="robots" content="{{ PageLayout.meta_robots }}">*/
/*     {% endif %}*/
/*     <meta name="viewport" content="width=device-width, initial-scale=1">*/
/*     <link rel="icon" href="{{ app.config.front_urlpath }}/img/common/favicon.ico">*/
/*     <link rel="stylesheet" href="{{ app.config.front_urlpath }}/css/style.css?v={{ constant('Eccube\\Common\\Constant::VERSION') }}">*/
/*     <link rel="stylesheet" href="{{ app.config.front_urlpath }}/css/slick.css?v={{ constant('Eccube\\Common\\Constant::VERSION') }}">*/
/*     <link rel="stylesheet" href="{{ app.config.front_urlpath }}/css/slick-theme.css?v={{ constant('Eccube\\Common\\Constant::VERSION') }}">*/
/*     <link rel="stylesheet" href="{{ app.config.front_urlpath }}/css/default.css?v={{ constant('Eccube\\Common\\Constant::VERSION') }}">*/
/*     <!-- for original theme CSS -->*/
/*     {% block stylesheet %}{% endblock %}*/
/* */
/*         <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>*/
/*         <script>window.jQuery || document.write('<script src="{{ app.config.front_urlpath }}/js/vendor/jquery-1.11.3.min.js?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"><\/script>')</script>*/
/* */
/*         {# ▼Head COLUMN #}*/
/*         {% if PageLayout.Head %}*/
/*             {# ▼上ナビ #}*/
/*             {{ include('block.twig', {'Blocks': PageLayout.Head}) }}*/
/*             {# ▲上ナビ #}*/
/*         {% endif %}*/
/*         {# ▲Head COLUMN #}*/
/* */
/*     </head>*/
/*     <body id="page_{{ app.request.get('_route') }}" class="{{ body_class|default('other_page') }}">*/
/*         <div id="wrapper">*/
/*             <header id="header">*/
/*                 <div class="container-fluid inner">*/
/*                     {# ▼HeaderInternal COLUMN #}*/
/*                     {% if PageLayout.Header %}*/
/*                         {# ▼上ナビ #}*/
/*                         {{ include('block.twig', {'Blocks': PageLayout.Header}) }}*/
/*                         {# ▲上ナビ #}*/
/*                     {% endif %}*/
/*                     {# ▲HeaderInternal COLUMN #}*/
/*                     <p id="btn_menu"><a class="nav-trigger" href="#nav">Menu<span></span></a></p>*/
/*                 </div>*/
/*                 <!-- ▼default_frame.twigに記入 -->*/
/*                 <nav class="header_nav pc">*/
/*                     <ul>*/
/*                         <li><a href="{{ url('homepage') }}">HOME</a></li>*/
/*                         <li><a href="{{ url('product_list') }}">GOODS</a></li>*/
/* 						<li><a href="http://inyoumarket.com/category/%e5%87%ba%e5%93%81%e8%80%85%e3%81%ae%e5%a3%b0/">出品者の声</a></li>*/
/*                         <li><a href="http://inyoumarket.com/shops/user_data/about">IN YOU Marketについて</a></li>*/
/*                         <li><a href="{{ url('contact') }}">CONTACT US</a></li>*/
/*                     </ul>*/
/*                 </nav>*/
/*                 <!-- ▲default_frame.twigに記入 -->*/
/*             </header>*/
/*             <p id="page-top"><a href="#header">*/
/*                <img src="{{ app.config.front_urlpath }}/img/common/pagetop.png" alt="PAGE TOP">*/
/*            </a></p>*/
/* */
/*            <div id="contents" class="{{ PageLayout.theme }}">*/
/* */
/*             <div id="contents_top">*/
/*                 {# ▼TOP COLUMN #}*/
/*                 {% if PageLayout.ContentsTop %}*/
/*                     {# ▼上ナビ #}*/
/*                     {{ include('block.twig', {'Blocks': PageLayout.ContentsTop}) }}*/
/*                     {# ▲上ナビ #}*/
/*                 {% endif %}*/
/*                 {# ▲TOP COLUMN #}*/
/*             </div>*/
/* */
/*             <div class="container-fluid inner">*/
/*                */
/*                     {# ▼LEFT COLUMN #}*/
/*                     {% if PageLayout.SideLeft %}*/
/*                         <div id="side_left" class="side{% if PageLayout.url == 'homepage' %} sp{% endif %}">*/
/*                             {# ▼左ナビ #}*/
/*                             {{ include('block.twig', {'Blocks': PageLayout.SideLeft}) }}*/
/*                             {# ▲左ナビ #}*/
/*                         </div>*/
/*                     {% endif %}*/
/*                     {# ▲LEFT COLUMN #}*/
/*                */
/* */
/*                 <div id="main">*/
/*                     {# ▼メイン上部 #}*/
/*                     {% if PageLayout.MainTop %}*/
/*                         <div id="main_top">*/
/*                             {{ include('block.twig', {'Blocks': PageLayout.MainTop}) }}*/
/*                         </div>*/
/*                     {% endif %}*/
/*                     {# ▲メイン上部 #}*/
/* */
/*                     <div id="main_middle">*/
/*                         {% block main %}{% endblock %}*/
/*                         </div>*/
/* */
/*                         {# ▼メイン下部 #}*/
/*                         {% if PageLayout.MainBottom %}*/
/*                             <div id="main_bottom">*/
/*                                 {{ include('block.twig', {'Blocks': PageLayout.MainBottom}) }}*/
/*                             </div>*/
/*                         {% endif %}*/
/*                         {# ▲メイン下部 #}*/
/*                     </div><!--#main-->*/
/* */
/*                     {# ▼RIGHT COLUMN #}*/
/*                     {% if PageLayout.SideRight %}*/
/*                         <div id="side_right" class="side">*/
/*                             {# ▼右ナビ #}*/
/*                             {{ include('block.twig', {'Blocks': PageLayout.SideRight}) }}*/
/*                             {# ▲右ナビ #}*/
/*                         </div>*/
/*                     {% endif %}*/
/*                     {# ▲RIGHT COLUMN #}*/
/* */
/*                     {# ▼BOTTOM COLUMN #}*/
/*                     {% if PageLayout.ContentsBottom %}*/
/*                         <div id="contents_bottom">*/
/*                             {# ▼下ナビ #}*/
/*                             {{ include('block.twig', {'Blocks': PageLayout.ContentsBottom}) }}*/
/*                             {# ▲下ナビ #}*/
/*                         </div>*/
/*                     {% endif %}*/
/*                     {# ▲BOTTOM COLUMN #}*/
/* */
/*             </div>*/
/* */
/*             <footer id="footer">*/
/*                 {# ▼Footer COLUMN#}*/
/*                 {% if PageLayout.Footer %}*/
/*                     {# ▼上ナビ #}*/
/*                     {{ include('block.twig', {'Blocks': PageLayout.Footer}) }}*/
/*                     {# ▲上ナビ #}*/
/*                 {% endif %}*/
/*                 {# ▲Footer COLUMN#}*/
/*             </footer>*/
/*             */
/*         </div>*/
/*         <div id="drawer" class="drawer sp">*/
/*             </div>*/
/*     </div>*/
/*        */
/*         <div class="overlay"></div>*/
/* */
/*         <script src="{{ app.config.front_urlpath }}/js/vendor/bootstrap.custom.min.js?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"></script>*/
/*         <script src="{{ app.config.front_urlpath }}/js/vendor/slick.min.js?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"></script>*/
/*         <script src="{{ app.config.front_urlpath }}/js/function.js?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"></script>*/
/*         <script src="{{ app.config.front_urlpath }}/js/eccube.js?v={{ constant('Eccube\\Common\\Constant::VERSION') }}"></script>*/
/*         <script>*/
/*             $(function () {*/
/*                 $('#drawer').append($('.drawer_block').clone(true).children());*/
/*                 $.ajax({*/
/*                     url: '{{ app.config.front_urlpath }}/img/common/svg.html',*/
/*                     type: 'GET',*/
/*                     dataType: 'html',*/
/*                 }).done(function(data){*/
/*                     $('body').prepend(data);*/
/*                 }).fail(function(data){*/
/*                 });*/
/*             });*/
/*         </script>*/
/*         <script type="text/javascript" src="{{ app.config.front_urlpath }}/js/jquery.matchHeight.js"></script>*/
/*         <script type="text/javascript" src="{{ app.config.front_urlpath }}/js/jquery.fadethis.min.js"></script>*/
/*         {% block javascript %}{% endblock %}*/
/*         </body>*/
/*         </html>*/
/* */
