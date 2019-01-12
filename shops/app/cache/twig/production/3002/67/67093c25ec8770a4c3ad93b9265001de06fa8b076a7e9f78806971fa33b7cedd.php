<?php

/* __string_template__192b54dd29f7bb303002da1cd8c515a4f9287488795d504ea6fda84b3b4db8b9 */
class __TwigTemplate_32754799ebde0c2fc191e4fad37bed4e699aa1e1ff36a47c3a1ad5476e436b36 extends Twig_Template
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
        if ($this->env->getExtension('security')->isGranted("ROLE_USER")) {
            // line 23
            echo "    <div id=\"member\" class=\"member drawer_block pc\">
        <ul class=\"member_link\">
            <li>
                <a href=\"";
            // line 26
            echo $this->env->getExtension('eccube')->getUrl("mypage");
            echo "\">
                    <svg class=\"cb\">
                        <use xlink:href=\"#ico_mypage\" />
                    </svg>
                    <span class=\"sp\">マイページ</span>
                </a>
            </li>
            ";
            // line 33
            if (($this->getAttribute((isset($context["BaseInfo"]) ? $context["BaseInfo"] : null), "option_favorite_product", array()) == 1)) {
                // line 34
                echo "                <li>
                    <a href=\"";
                // line 35
                echo $this->env->getExtension('eccube')->getUrl("mypage_favorite");
                echo "\">
                        <svg class=\"cb\">
                        <use xlink:href=\"#ico_favorite\"></use>
                    </svg>
                 <span class=\"sp\">お気に入り</span>
             </a></li>
            ";
            }
            // line 42
            echo "            <li>
                <a href=\"";
            // line 43
            echo $this->env->getExtension('eccube')->getUrl("logout");
            echo "\">
                    <svg class=\"cb\">
                        <use xlink:href=\"#ico_logout\" />
                    </svg>
                    <span class=\"sp\">ログアウト</span>
                </a>
            </li>
        </ul>
    </div>
";
        } else {
            // line 53
            echo "    <div id=\"member\" class=\"member drawer_block pc\">
        <ul class=\"member_link\">
            <li>
                <a href=\"";
            // line 56
            echo $this->env->getExtension('eccube')->getUrl("entry");
            echo "\">
                    <svg class=\"cb\">
                        <use xlink:href=\"#ico_mypage\" />
                    </svg>
                    <span class=\"sp\">新規登録</span>
                </a>
            </li>
            ";
            // line 63
            if (($this->getAttribute((isset($context["BaseInfo"]) ? $context["BaseInfo"] : null), "option_favorite_product", array()) == 1)) {
                // line 64
                echo "                <li><a href=\"";
                echo $this->env->getExtension('eccube')->getUrl("mypage_favorite");
                echo "\">
                    <svg class=\"cb\">
                        <use xlink:href=\"#ico_favorite\"></use>
                </svg>
                <span class=\"sp\">お気に入り</span>
            </a></li>
            ";
            }
            // line 71
            echo "            <li>
                <a href=\"";
            // line 72
            echo $this->env->getExtension('eccube')->getUrl("mypage_login");
            echo "\">
                    <svg class=\"cb\">
                        <use xlink:href=\"#ico_login\" />
                    </svg>
                    <span class=\"sp\">ログイン</span>
                </a>
            </li>
        </ul>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "__string_template__192b54dd29f7bb303002da1cd8c515a4f9287488795d504ea6fda84b3b4db8b9";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  98 => 72,  95 => 71,  84 => 64,  82 => 63,  72 => 56,  67 => 53,  54 => 43,  51 => 42,  41 => 35,  38 => 34,  36 => 33,  26 => 26,  21 => 23,  19 => 22,);
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
/* {% if is_granted('ROLE_USER') %}*/
/*     <div id="member" class="member drawer_block pc">*/
/*         <ul class="member_link">*/
/*             <li>*/
/*                 <a href="{{ url('mypage') }}">*/
/*                     <svg class="cb">*/
/*                         <use xlink:href="#ico_mypage" />*/
/*                     </svg>*/
/*                     <span class="sp">マイページ</span>*/
/*                 </a>*/
/*             </li>*/
/*             {% if BaseInfo.option_favorite_product == 1 %}*/
/*                 <li>*/
/*                     <a href="{{ url('mypage_favorite') }}">*/
/*                         <svg class="cb">*/
/*                         <use xlink:href="#ico_favorite"></use>*/
/*                     </svg>*/
/*                  <span class="sp">お気に入り</span>*/
/*              </a></li>*/
/*             {% endif %}*/
/*             <li>*/
/*                 <a href="{{ url('logout') }}">*/
/*                     <svg class="cb">*/
/*                         <use xlink:href="#ico_logout" />*/
/*                     </svg>*/
/*                     <span class="sp">ログアウト</span>*/
/*                 </a>*/
/*             </li>*/
/*         </ul>*/
/*     </div>*/
/* {% else %}*/
/*     <div id="member" class="member drawer_block pc">*/
/*         <ul class="member_link">*/
/*             <li>*/
/*                 <a href="{{ url('entry') }}">*/
/*                     <svg class="cb">*/
/*                         <use xlink:href="#ico_mypage" />*/
/*                     </svg>*/
/*                     <span class="sp">新規登録</span>*/
/*                 </a>*/
/*             </li>*/
/*             {% if BaseInfo.option_favorite_product == 1 %}*/
/*                 <li><a href="{{ url('mypage_favorite') }}">*/
/*                     <svg class="cb">*/
/*                         <use xlink:href="#ico_favorite"></use>*/
/*                 </svg>*/
/*                 <span class="sp">お気に入り</span>*/
/*             </a></li>*/
/*             {% endif %}*/
/*             <li>*/
/*                 <a href="{{ url('mypage_login') }}">*/
/*                     <svg class="cb">*/
/*                         <use xlink:href="#ico_login" />*/
/*                     </svg>*/
/*                     <span class="sp">ログイン</span>*/
/*                 </a>*/
/*             </li>*/
/*         </ul>*/
/*     </div>*/
/* {% endif %}*/
