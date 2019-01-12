<?php

/* __string_template__2ec0df20be9d43ba2f9e17b0dee5c53ac05df3d3c9d6fc6b742e56eedcf691da */
class __TwigTemplate_2a2a413683797c1b0fc5370d95bfb4e111ce7ef34934e5c6cfa4dae72ffdf85d extends Twig_Template
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
        echo "<div id=\"cart_area\">
    <p class=\"clearfix cart-trigger\">
        <a href=\"#cart\">
            <svg class=\"cb\">
                <use xlink:href=\"#ico_cart\"/>
            </svg>
            <span class=\"badge\">";
        // line 28
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["Cart"]) ? $context["Cart"] : null), "total_quantity", array()), "html", null, true);
        echo "</span>
            <svg class=\"cb cb-close\">
                <use xlink:href=\"#cb-close\"/>
            </svg>
        </a>
        <!--
        <span class=\"cart_price pc\">合計 <span class=\"price\">";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('eccube')->getPriceFilter($this->getAttribute((isset($context["Cart"]) ? $context["Cart"] : null), "total_price", array())), "html", null, true);
        echo "</span></span></p>
    -->
    </p>
    <div id=\"cart\" class=\"cart\">
        <div class=\"inner\">
        ";
        // line 39
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "session", array()), "flashbag", array()), "get", array(0 => "eccube.front.cart.error"), "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
            // line 40
            echo "            <div class=\"message\">
                <p class=\"errormsg bg-danger\">
                    <svg class=\"cb cb-warning\"><use xlink:href=\"#cb-warning\" /></svg>";
            // line 42
            echo nl2br(twig_escape_filter($this->env, $this->env->getExtension('translator')->trans($context["error"]), "html", null, true));
            echo "
                </p>
            </div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        echo "        ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["Cart"]) ? $context["Cart"] : null), "CartItems", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["CartItem"]) {
            // line 47
            echo "            ";
            $context["ProductClass"] = $this->getAttribute($context["CartItem"], "Object", array());
            // line 48
            echo "                ";
            $context["Product"] = $this->getAttribute((isset($context["ProductClass"]) ? $context["ProductClass"] : null), "Product", array());
            // line 49
            echo "                <div class=\"item_box clearfix\">
                    <div class=\"item_photo\">
                        <img
                        src=\"";
            // line 52
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "config", array()), "image_save_urlpath", array()), "html", null, true);
            echo "/";
            echo twig_escape_filter($this->env, $this->env->getExtension('eccube')->getNoImageProduct($this->getAttribute((isset($context["Product"]) ? $context["Product"] : null), "MainListImage", array())), "html", null, true);
            echo "\"
                        alt=\"";
            // line 53
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["Product"]) ? $context["Product"] : null), "name", array()), "html", null, true);
            echo "\">
                    </div>
                    <dl class=\"item_detail\">
                        <dt class=\"item_name\">";
            // line 56
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["Product"]) ? $context["Product"] : null), "name", array()), "html", null, true);
            echo "</dt>
                        <dd class=\"item_pattern small\">";
            // line 58
            if ($this->getAttribute((isset($context["ProductClass"]) ? $context["ProductClass"] : null), "ClassCategory1", array())) {
                // line 59
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["ProductClass"]) ? $context["ProductClass"] : null), "ClassCategory1", array()), "ClassName", array()), "html", null, true);
                echo "：";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["ProductClass"]) ? $context["ProductClass"] : null), "ClassCategory1", array()), "html", null, true);
                // line 60
                if ($this->getAttribute((isset($context["ProductClass"]) ? $context["ProductClass"] : null), "ClassCategory2", array())) {
                    // line 61
                    echo "<br>";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["ProductClass"]) ? $context["ProductClass"] : null), "ClassCategory2", array()), "ClassName", array()), "html", null, true);
                    echo "：";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["ProductClass"]) ? $context["ProductClass"] : null), "ClassCategory2", array()), "html", null, true);
                }
            }
            // line 64
            echo "</dd>
                        <dd class=\"item_price\">";
            // line 65
            echo twig_escape_filter($this->env, $this->env->getExtension('eccube')->getPriceFilter($this->getAttribute($context["CartItem"], "price", array())), "html", null, true);
            echo "<span class=\"small\">税込</span></dd>
                        <dd class=\"item_quantity form-group form-inline\">数量：";
            // line 66
            echo twig_escape_filter($this->env, $this->getAttribute($context["CartItem"], "quantity", array()), "html", null, true);
            echo "</dd>
                    </dl>
                </div><!--/item_box-->
                <p class=\"cart_price sp\">合計 <span class=\"price\">";
            // line 69
            echo twig_escape_filter($this->env, $this->env->getExtension('eccube')->getPriceFilter($this->getAttribute((isset($context["Cart"]) ? $context["Cart"] : null), "total_price", array())), "html", null, true);
            echo "</span>
                </p>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['CartItem'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 72
        echo "        ";
        if ((twig_length_filter($this->env, $this->getAttribute((isset($context["Cart"]) ? $context["Cart"] : null), "CartItems", array())) > 0)) {
            // line 73
            echo "            <div class=\"btn_area\">
                <ul>
                    <li>
                        <a href=\"";
            // line 76
            echo $this->env->getExtension('eccube')->getUrl("cart");
            echo "\" class=\"btn btn-primary\">カートへ進む</a>
                    </li>
                    <li>
                        <button type=\"button\" class=\"btn btn-default btn-sm cart-trigger\">キャンセル</button>
                    </li>
                </ul>
            </div>
        ";
        } else {
            // line 84
            echo "            <div class=\"btn_area\">
                <div class=\"message\">
                    <p class=\"errormsg bg-danger\" style=\"margin-bottom: 20px;\">
                        現在カート内に<br>商品はございません。
                    </p>
                </div>
            </div>
        ";
        }
        // line 92
        echo "        </div>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "__string_template__2ec0df20be9d43ba2f9e17b0dee5c53ac05df3d3c9d6fc6b742e56eedcf691da";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  160 => 92,  150 => 84,  139 => 76,  134 => 73,  131 => 72,  122 => 69,  116 => 66,  112 => 65,  109 => 64,  102 => 61,  100 => 60,  96 => 59,  94 => 58,  90 => 56,  84 => 53,  78 => 52,  73 => 49,  70 => 48,  67 => 47,  62 => 46,  52 => 42,  48 => 40,  44 => 39,  36 => 34,  27 => 28,  19 => 22,);
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
/* <div id="cart_area">*/
/*     <p class="clearfix cart-trigger">*/
/*         <a href="#cart">*/
/*             <svg class="cb">*/
/*                 <use xlink:href="#ico_cart"/>*/
/*             </svg>*/
/*             <span class="badge">{{ Cart.total_quantity }}</span>*/
/*             <svg class="cb cb-close">*/
/*                 <use xlink:href="#cb-close"/>*/
/*             </svg>*/
/*         </a>*/
/*         <!--*/
/*         <span class="cart_price pc">合計 <span class="price">{{ Cart.total_price|price }}</span></span></p>*/
/*     -->*/
/*     </p>*/
/*     <div id="cart" class="cart">*/
/*         <div class="inner">*/
/*         {% for error in app.session.flashbag.get('eccube.front.cart.error')  %}*/
/*             <div class="message">*/
/*                 <p class="errormsg bg-danger">*/
/*                     <svg class="cb cb-warning"><use xlink:href="#cb-warning" /></svg>{{ error|trans|nl2br }}*/
/*                 </p>*/
/*             </div>*/
/*         {% endfor %}*/
/*         {% for CartItem in Cart.CartItems %}*/
/*             {% set ProductClass = CartItem.Object %}*/
/*                 {% set Product = ProductClass.Product %}*/
/*                 <div class="item_box clearfix">*/
/*                     <div class="item_photo">*/
/*                         <img*/
/*                         src="{{ app.config.image_save_urlpath }}/{{ Product.MainListImage|no_image_product }}"*/
/*                         alt="{{ Product.name }}">*/
/*                     </div>*/
/*                     <dl class="item_detail">*/
/*                         <dt class="item_name">{{ Product.name }}</dt>*/
/*                         <dd class="item_pattern small">*/
/*                             {%- if ProductClass.ClassCategory1 -%}*/
/*                                 {{ ProductClass.ClassCategory1.ClassName }}：{{ ProductClass.ClassCategory1 }}*/
/*                                 {%- if ProductClass.ClassCategory2 -%}*/
/*                                     <br>{{ ProductClass.ClassCategory2.ClassName }}：{{ ProductClass.ClassCategory2 }}*/
/*                                 {%- endif -%}*/
/*                             {%- endif -%}*/
/*                         </dd>*/
/*                         <dd class="item_price">{{ CartItem.price|price }}<span class="small">税込</span></dd>*/
/*                         <dd class="item_quantity form-group form-inline">数量：{{ CartItem.quantity }}</dd>*/
/*                     </dl>*/
/*                 </div><!--/item_box-->*/
/*                 <p class="cart_price sp">合計 <span class="price">{{ Cart.total_price|price }}</span>*/
/*                 </p>*/
/*         {% endfor %}*/
/*         {% if Cart.CartItems|length > 0 %}*/
/*             <div class="btn_area">*/
/*                 <ul>*/
/*                     <li>*/
/*                         <a href="{{ url('cart') }}" class="btn btn-primary">カートへ進む</a>*/
/*                     </li>*/
/*                     <li>*/
/*                         <button type="button" class="btn btn-default btn-sm cart-trigger">キャンセル</button>*/
/*                     </li>*/
/*                 </ul>*/
/*             </div>*/
/*         {% else %}*/
/*             <div class="btn_area">*/
/*                 <div class="message">*/
/*                     <p class="errormsg bg-danger" style="margin-bottom: 20px;">*/
/*                         現在カート内に<br>商品はございません。*/
/*                     </p>*/
/*                 </div>*/
/*             </div>*/
/*         {% endif %}*/
/*         </div>*/
/*     </div>*/
/* </div>*/
