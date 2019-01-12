<?php

/* __string_template__23a8f863cede9264db3afea31920970e74310e90274d95e1c5e814d113d1bbba */
class __TwigTemplate_de66fc6c0b9f223547e920e116c3fe54aff145cb717a7aa2248c8f530cd7ea68 extends Twig_Template
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
        // line 42
        echo "<div id=\"side_category\" class=\"drawer_block pc\">

    ";
        // line 44
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["Categories"]) ? $context["Categories"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["Category"]) {
            // line 45
            echo "        ";
            echo $this->getAttribute($this, "tree", array(0 => $context["Category"]), "method");
            echo "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['Category'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 47
        echo "<div class=\"bnr\"> <a href=\"http://macrobiotic-daisuki.jp/\" target=\"_blank\"><img src=\"http://inyoumarket.com/shops/html/template/img/inyou_logo.png\"></a>
</div>
<div class=\"bnr\"> <a href=\"http://inyoumarket.com/category/出品者の声/\"><img src=\"http://inyoumarket.com/shops/html/template/img/sov.png\"></a>
</div>
<div class=\"bnr\"> <a href=\"https://inyoumarket.com/shops/user_data/entrypage\"><img src=\"http://inyoumarket.com/shops/html/template/img/syupin.png\"></a>
</div>
<div class=\"bnr\"> <a href=\"http://line.me/ti/p/@vva0224k\"><img src=\"http://inyoumarket.com/shops/html/template/img/linebanner01.jpg\"></a>
</div>
<div class=\"bnr\"> <a href=\"https://inyoumarket.com/shops/products/list\"><img src=\"http://inyoumarket.com/shops/html/template/3002/img/top/img14.jpg\"></a>
</div>



</div>";
    }

    // line 22
    public function gettree($__Category__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "Category" => $__Category__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 23
            echo "    ";
            if (($this->getAttribute((isset($context["Category"]) ? $context["Category"] : null), "level", array()) == 1)) {
                // line 24
                echo "
        <h3 class=\"cat_h3\"><a href=\"";
                // line 25
                echo $this->env->getExtension('eccube')->getUrl("product_list");
                echo "?category_id=";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["Category"]) ? $context["Category"] : null), "id", array()), "html", null, true);
                echo "\">
            ";
                // line 26
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["Category"]) ? $context["Category"] : null), "name", array()), "html", null, true);
                echo "
        </a></h3>
    ";
            } else {
                // line 29
                echo "        <li><a href=\"";
                echo $this->env->getExtension('eccube')->getUrl("product_list");
                echo "?category_id=";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["Category"]) ? $context["Category"] : null), "id", array()), "html", null, true);
                echo "\">
            ";
                // line 30
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["Category"]) ? $context["Category"] : null), "name", array()), "html", null, true);
                echo "
        </a></li>
    ";
            }
            // line 33
            echo "        ";
            if ((twig_length_filter($this->env, $this->getAttribute((isset($context["Category"]) ? $context["Category"] : null), "children", array())) > 0)) {
                // line 34
                echo "            <ul class=\"cat_list\">
            ";
                // line 35
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["Category"]) ? $context["Category"] : null), "children", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["ChildCategory"]) {
                    // line 36
                    echo "                    ";
                    echo $this->getAttribute($this, "tree", array(0 => $context["ChildCategory"]), "method");
                    echo "
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['ChildCategory'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 38
                echo "            </ul>
        ";
            }
            // line 40
            echo "
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "__string_template__23a8f863cede9264db3afea31920970e74310e90274d95e1c5e814d113d1bbba";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  119 => 40,  115 => 38,  106 => 36,  102 => 35,  99 => 34,  96 => 33,  90 => 30,  83 => 29,  77 => 26,  71 => 25,  68 => 24,  65 => 23,  53 => 22,  36 => 47,  27 => 45,  23 => 44,  19 => 42,);
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
/* {% macro tree(Category) %}*/
/*     {% if Category.level==1 %}*/
/* */
/*         <h3 class="cat_h3"><a href="{{ url('product_list') }}?category_id={{ Category.id }}">*/
/*             {{ Category.name }}*/
/*         </a></h3>*/
/*     {% else %}*/
/*         <li><a href="{{ url('product_list') }}?category_id={{ Category.id }}">*/
/*             {{ Category.name }}*/
/*         </a></li>*/
/*     {% endif %}*/
/*         {% if Category.children|length > 0 %}*/
/*             <ul class="cat_list">*/
/*             {% for ChildCategory in Category.children %}*/
/*                     {{ _self.tree(ChildCategory) }}*/
/*             {% endfor %}*/
/*             </ul>*/
/*         {% endif %}*/
/* */
/* {% endmacro %}*/
/* <div id="side_category" class="drawer_block pc">*/
/* */
/*     {% for Category in Categories %}*/
/*         {{ _self.tree(Category) }}*/
/*     {% endfor %}*/
/* <div class="bnr"> <a href="http://macrobiotic-daisuki.jp/" target="_blank"><img src="http://inyoumarket.com/shops/html/template/img/inyou_logo.png"></a>*/
/* </div>*/
/* <div class="bnr"> <a href="http://inyoumarket.com/category/出品者の声/"><img src="http://inyoumarket.com/shops/html/template/img/sov.png"></a>*/
/* </div>*/
/* <div class="bnr"> <a href="https://inyoumarket.com/shops/user_data/entrypage"><img src="http://inyoumarket.com/shops/html/template/img/syupin.png"></a>*/
/* </div>*/
/* <div class="bnr"> <a href="http://line.me/ti/p/@vva0224k"><img src="http://inyoumarket.com/shops/html/template/img/linebanner01.jpg"></a>*/
/* </div>*/
/* <div class="bnr"> <a href="https://inyoumarket.com/shops/products/list"><img src="http://inyoumarket.com/shops/html/template/3002/img/top/img14.jpg"></a>*/
/* </div>*/
/* */
/* */
/* */
/* </div>*/
