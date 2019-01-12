<?php

/* __string_template__537320f771832136792063e8d8bc35c433e42e2bad52da1895e3495aeff672c8 */
class __TwigTemplate_436fd7e7efcfa7ef70222a27ffd701b675e64cfebbf717958d69d34f909b7115 extends Twig_Template
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
        echo "<div class=\"news_contents\">
    <div id=\"news_area\">
        <h2 class=\"heading01 slide-bottom\">新着情報</h2>
        <div class=\"accordion\">
            <div class=\"newslist slide-bottom\">
                ";
        // line 27
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["NewsList"]) ? $context["NewsList"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["News"]) {
            // line 28
            echo "                <dl>
                    <dt>
                        <span class=\"date\">";
            // line 30
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute($context["News"], "date", array()), "Y/m/d"), "html", null, true);
            echo "</span>
                        <span class=\"news_title\">
                            ";
            // line 32
            echo twig_escape_filter($this->env, $this->getAttribute($context["News"], "title", array()), "html", null, true);
            echo "
                        </span>
                        ";
            // line 34
            if (($this->getAttribute($context["News"], "comment", array()) || $this->getAttribute($context["News"], "url", array()))) {
                // line 35
                echo "                        <span class=\"angle-circle\"><svg class=\"cb cb-angle-down\"><use xlink:href=\"#cb-angle-down\" /></svg></span>
                        ";
            }
            // line 37
            echo "                    </dt>
                    ";
            // line 38
            if (($this->getAttribute($context["News"], "comment", array()) || $this->getAttribute($context["News"], "url", array()))) {
                // line 39
                echo "                    <dd>";
                echo nl2br($this->getAttribute($context["News"], "comment", array()));
                echo "
                        ";
                // line 40
                if ($this->getAttribute($context["News"], "url", array())) {
                    echo "<br>
                        <a href=\"";
                    // line 41
                    echo twig_escape_filter($this->env, $this->getAttribute($context["News"], "url", array()), "html", null, true);
                    echo "\" ";
                    if (($this->getAttribute($context["News"], "link_method", array()) == "1")) {
                        echo "target=\"_blank\"";
                    }
                    echo ">
                        詳しくはこちら
                        </a>";
                }
                // line 44
                echo "                    </dd>
                    ";
            }
            // line 46
            echo "                </dl>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['News'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 48
        echo "            </div>
        </div>
    </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "__string_template__537320f771832136792063e8d8bc35c433e42e2bad52da1895e3495aeff672c8";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 48,  78 => 46,  74 => 44,  64 => 41,  60 => 40,  55 => 39,  53 => 38,  50 => 37,  46 => 35,  44 => 34,  39 => 32,  34 => 30,  30 => 28,  26 => 27,  19 => 22,);
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
/* <div class="news_contents">*/
/*     <div id="news_area">*/
/*         <h2 class="heading01 slide-bottom">新着情報</h2>*/
/*         <div class="accordion">*/
/*             <div class="newslist slide-bottom">*/
/*                 {% for News in NewsList %}*/
/*                 <dl>*/
/*                     <dt>*/
/*                         <span class="date">{{ News.date|date("Y/m/d") }}</span>*/
/*                         <span class="news_title">*/
/*                             {{ News.title }}*/
/*                         </span>*/
/*                         {% if News.comment or News.url %}*/
/*                         <span class="angle-circle"><svg class="cb cb-angle-down"><use xlink:href="#cb-angle-down" /></svg></span>*/
/*                         {% endif %}*/
/*                     </dt>*/
/*                     {% if News.comment or News.url %}*/
/*                     <dd>{{ News.comment|raw|nl2br}}*/
/*                         {% if News.url %}<br>*/
/*                         <a href="{{ News.url }}" {% if News.link_method == '1' %}target="_blank"{% endif %}>*/
/*                         詳しくはこちら*/
/*                         </a>{% endif %}*/
/*                     </dd>*/
/*                     {% endif %}*/
/*                 </dl>*/
/*                 {% endfor %}*/
/*             </div>*/
/*         </div>*/
/*     </div>*/
/* </div>*/
/* */
