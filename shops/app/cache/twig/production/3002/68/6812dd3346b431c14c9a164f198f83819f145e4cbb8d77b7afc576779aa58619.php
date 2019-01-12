<?php

/* __string_template__de4618dfa4fa977636f2bf2aeb00e665f041b7f7b7c72e22e89554399bc29ac9 */
class __TwigTemplate_c37768854d50f67b50469fd1b53993ad9a604c6b251d7bdca9cdcceb0183d5da extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 22
        $this->parent = $this->loadTemplate("default_frame.twig", "__string_template__de4618dfa4fa977636f2bf2aeb00e665f041b7f7b7c72e22e89554399bc29ac9", 22);
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
        // line 24
        $context["body_class"] = "registration_page";
        // line 22
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 26
    public function block_javascript($context, array $blocks = array())
    {
        // line 27
        echo "<script src=\"//ajaxzip3.github.io/ajaxzip3.js\" charset=\"UTF-8\"></script>
<script>
    \$(function() {
        \$('#zip-search').click(function() {
            AjaxZip3.zip2addr('entry[zip][zip01]', 'entry[zip][zip02]', 'entry[address][pref]', 'entry[address][addr01]');
        });
    });
</script>
";
    }

    // line 37
    public function block_main($context, array $blocks = array())
    {
        // line 38
        echo "<h1 class=\"page-heading\">新規会員登録</h1>
<div id=\"top_wrap\" class=\"container-fluid\">
<p><font color=\"#FF0000\">新規会員ご登録のお客様へ　ご注意<br>
<br>
お客様のメールソフトやメールサービスのセキュリティ設定の関係上、仮登録メールが届かないという声をいただいております。<br>お手数ですが、万が一「仮登録」メールが届かなかった場合は、お客様の環境にてcustomer@inyoumarket.comからのメールが届く設定に変更していただきますよう、お願い申し上げます。</font></p>
<p<font color=\"\"></font></p>
    <div id=\"top_box\" class=\"row\">
        <div id=\"top_box__body\" class=\"col-md-12 col-md-offset-0\">
            <form method=\"post\" action=\"";
        // line 46
        echo $this->env->getExtension('eccube')->getUrl("entry");
        echo "\">
                ";
        // line 47
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "_token", array()), 'widget');
        echo "
                <div id=\"top_box__body_inner\" class=\"dl_table\">
                    <dl id=\"top_box__name\">
                        <dt>";
        // line 50
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "name", array()), 'label');
        echo "</dt>
                        <dd class=\"form-group input_name\">
                            ";
        // line 52
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "name", array()), "name01", array()), 'widget');
        echo "
                            ";
        // line 53
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "name", array()), "name02", array()), 'widget');
        echo "
                            ";
        // line 54
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "name", array()), "name01", array()), 'errors');
        echo "
                            ";
        // line 55
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "name", array()), "name02", array()), 'errors');
        echo "
                        </dd>
                    </dl>
                    <dl id=\"top_box__kana\">
                        <dt>";
        // line 59
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "kana", array()), 'label');
        echo "</dt>
                        <dd class=\"form-group input_name\">
                            ";
        // line 61
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "kana", array()), "kana01", array()), 'widget');
        echo "
                            ";
        // line 62
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "kana", array()), "kana02", array()), 'widget');
        echo "
                            ";
        // line 63
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "kana", array()), "kana01", array()), 'errors');
        echo "
                            ";
        // line 64
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "kana", array()), "kana02", array()), 'errors');
        echo "
                        </dd>
                    </dl>
                    <dl id=\"top_box__company_name\">
                        <dt>";
        // line 68
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "company_name", array()), 'label');
        echo "</dt>
                        <dd class=\"form-group input_name\">
                            ";
        // line 70
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "company_name", array()), 'widget');
        echo "
                            ";
        // line 71
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "company_name", array()), 'errors');
        echo "
                        </dd>
                    </dl>
                    <dl id=\"top_box__address_detail\">
                        <dt>";
        // line 75
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "address", array()), 'label');
        echo "</dt>
                        <dd>
                            <div id=\"top_box__zip\" class=\"form-group form-inline input_zip ";
        // line 77
        if (( !twig_test_empty($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "zip", array()), "zip01", array()), "vars", array()), "errors", array())) ||  !twig_test_empty($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "zip", array()), "zip02", array()), "vars", array()), "errors", array())))) {
            echo "has-error";
        }
        echo "\">";
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "zip", array()), 'widget');
        echo "</div>
                            <div id=\"top_box__address\" class=\"";
        // line 78
        if ((( !twig_test_empty($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "address", array()), "pref", array()), "vars", array()), "errors", array())) ||  !twig_test_empty($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "address", array()), "addr01", array()), "vars", array()), "errors", array()))) ||  !twig_test_empty($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "address", array()), "addr02", array()), "vars", array()), "errors", array())))) {
            echo "has-error";
        }
        echo "\">
                                ";
        // line 79
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "address", array()), 'widget');
        echo "
                                ";
        // line 80
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "address", array()), 'errors');
        echo "
                            </div>
                        </dd>
                    </dl>
                    <dl id=\"top_box__tel\">
                        <dt>";
        // line 85
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "tel", array()), 'label');
        echo "</dt>
                        <dd>
                            <div class=\"form-inline form-group input_tel\">
                                ";
        // line 88
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "tel", array()), 'widget', array("attr" => array("class" => "short")));
        echo "
                                ";
        // line 89
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "tel", array()), 'errors');
        echo "
                            </div>
                        </dd>
                    </dl>
                    <dl id=\"top_box__fax\">
                        <dt>";
        // line 94
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "fax", array()), 'label');
        echo "</dt>
                        <dd>
                            <div class=\"form-inline form-group input_tel\">
                                ";
        // line 97
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "fax", array()), 'widget', array("attr" => array("class" => "short")));
        echo "
                                ";
        // line 98
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "fax", array()), 'errors');
        echo "
                            </div>
                        </dd>
                    </dl>
                    <dl id=\"top_box__email\">
                        <dt>";
        // line 103
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "email", array()), 'label');
        echo "</dt>
                        <dd>
                            ";
        // line 105
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "email", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["emailField"]) {
            // line 106
            echo "                            <div class=\"form-group ";
            if ( !twig_test_empty($this->getAttribute($this->getAttribute($context["emailField"], "vars", array()), "errors", array()))) {
                echo "has-error";
            }
            echo "\">
                                ";
            // line 107
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["emailField"], 'widget');
            echo "
                                ";
            // line 108
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["emailField"], 'errors');
            echo "
                            </div>
                            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['emailField'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 111
        echo "                        </dd>
                    </dl>
                    <dl id=\"top_box__password\">
                        <dt>";
        // line 114
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "password", array()), 'label');
        echo "</dt>
                        <dd>
                            ";
        // line 116
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "password", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["passwordField"]) {
            // line 117
            echo "                            <div class=\"form-group ";
            if ( !twig_test_empty($this->getAttribute($this->getAttribute($context["passwordField"], "vars", array()), "errors", array()))) {
                echo "has-error";
            }
            echo "\">
                                ";
            // line 118
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["passwordField"], 'widget', array("type" => "password"));
            echo "
                                ";
            // line 119
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["passwordField"], 'errors');
            echo "
                            </div>
                            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['passwordField'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 122
        echo "                        </dd>
                    </dl>
                </div>
                <div id=\"top_box__birth\" class=\"dl_table not_required\">
                    <dl>
                        <dt>";
        // line 127
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "birth", array()), 'label');
        echo "</dt>
                        <dd>
                            <div class=\"form-group form-inline\">
                                ";
        // line 130
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "birth", array()), 'widget');
        echo "
                                ";
        // line 131
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "birth", array()), 'errors');
        echo "
                            </div>
                        </dd>
                    </dl>
                    <dl>
                        <dt id=\"top_box__sex\">";
        // line 136
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "sex", array()), 'label');
        echo "</dt>
                        <dd>
                            <div class=\"form-group form-inline\">
                                ";
        // line 139
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "sex", array()), 'widget');
        echo "
                                ";
        // line 140
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "sex", array()), 'errors');
        echo "
                            </div>
                        </dd>
                    </dl>
                    <dl id=\"top_box__job\">
                        <dt>";
        // line 145
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "job", array()), 'label');
        echo "</dt>
                        <dd>
                            <div class=\"form-group form-inline\">
                                ";
        // line 148
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "job", array()), 'widget');
        echo "
                                ";
        // line 149
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "job", array()), 'errors');
        echo "
                            </div>
                        </dd>
                    </dl>";
        // line 173
        echo "<dl>
<dt>メールマガジン送付について<span class=\"required\">必須</span></dt>
<dd>
    <div class=\"form-group form-inline\">
        ";
        // line 177
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "mailmaga_flg", array()), 'widget');
        echo "
        ";
        // line 178
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "mailmaga_flg", array()), 'errors');
        echo "
    </div>
</dd>
</dl>

                </div>
                ";
        // line 184
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["f"]) {
            // line 185
            echo "                    ";
            if (preg_match("[^plg*]", $this->getAttribute($this->getAttribute($context["f"], "vars", array()), "name", array()))) {
                // line 186
                echo "                        <div class=\"extra-form dl_table\">
                            ";
                // line 187
                echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["f"], 'row');
                echo "
                        </div>
                    ";
            }
            // line 190
            echo "                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['f'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 191
        echo "                <input id=\"top_box__hidden_mode\" type=\"hidden\" name=\"mode\" value=\"confirm\">
                <p id=\"top_box__agreement\" class=\"form_terms_link\"><a href=\"";
        // line 192
        echo $this->env->getExtension('eccube')->getUrl("help_agreement");
        echo "\" target=\"_blank\">利用規約</a>に同意してお進みください
                </p>

                <div id=\"top_box__footer\" class=\"row no-padding\">
                    <div id=\"top_box__button_menu\" class=\"btn_group col-sm-offset-4 col-sm-4\">
                        <p>
                            <button type=\"submit\" class=\"btn btn-primary btn-block\">同意する</button>
                        </p>
                        <p><a href=\"";
        // line 200
        echo $this->env->getExtension('eccube')->getUrl("index");
        echo "\" class=\"btn btn-info btn-block\">同意しない</a></p>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
";
    }

    public function getTemplateName()
    {
        return "__string_template__de4618dfa4fa977636f2bf2aeb00e665f041b7f7b7c72e22e89554399bc29ac9";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  378 => 200,  367 => 192,  364 => 191,  358 => 190,  352 => 187,  349 => 186,  346 => 185,  342 => 184,  333 => 178,  329 => 177,  323 => 173,  317 => 149,  313 => 148,  307 => 145,  299 => 140,  295 => 139,  289 => 136,  281 => 131,  277 => 130,  271 => 127,  264 => 122,  255 => 119,  251 => 118,  244 => 117,  240 => 116,  235 => 114,  230 => 111,  221 => 108,  217 => 107,  210 => 106,  206 => 105,  201 => 103,  193 => 98,  189 => 97,  183 => 94,  175 => 89,  171 => 88,  165 => 85,  157 => 80,  153 => 79,  147 => 78,  139 => 77,  134 => 75,  127 => 71,  123 => 70,  118 => 68,  111 => 64,  107 => 63,  103 => 62,  99 => 61,  94 => 59,  87 => 55,  83 => 54,  79 => 53,  75 => 52,  70 => 50,  64 => 47,  60 => 46,  50 => 38,  47 => 37,  35 => 27,  32 => 26,  28 => 22,  26 => 24,  11 => 22,);
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
/* {% extends 'default_frame.twig' %}*/
/* */
/* {% set body_class = 'registration_page' %}*/
/* */
/* {% block javascript %}*/
/* <script src="//ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>*/
/* <script>*/
/*     $(function() {*/
/*         $('#zip-search').click(function() {*/
/*             AjaxZip3.zip2addr('entry[zip][zip01]', 'entry[zip][zip02]', 'entry[address][pref]', 'entry[address][addr01]');*/
/*         });*/
/*     });*/
/* </script>*/
/* {% endblock javascript %}*/
/* */
/* {% block main %}*/
/* <h1 class="page-heading">新規会員登録</h1>*/
/* <div id="top_wrap" class="container-fluid">*/
/* <p><font color="#FF0000">新規会員ご登録のお客様へ　ご注意<br>*/
/* <br>*/
/* お客様のメールソフトやメールサービスのセキュリティ設定の関係上、仮登録メールが届かないという声をいただいております。<br>お手数ですが、万が一「仮登録」メールが届かなかった場合は、お客様の環境にてcustomer@inyoumarket.comからのメールが届く設定に変更していただきますよう、お願い申し上げます。</font></p>*/
/* <p<font color=""></font></p>*/
/*     <div id="top_box" class="row">*/
/*         <div id="top_box__body" class="col-md-12 col-md-offset-0">*/
/*             <form method="post" action="{{ url('entry') }}">*/
/*                 {{ form_widget(form._token) }}*/
/*                 <div id="top_box__body_inner" class="dl_table">*/
/*                     <dl id="top_box__name">*/
/*                         <dt>{{ form_label(form.name) }}</dt>*/
/*                         <dd class="form-group input_name">*/
/*                             {{ form_widget(form.name.name01) }}*/
/*                             {{ form_widget(form.name.name02) }}*/
/*                             {{ form_errors(form.name.name01) }}*/
/*                             {{ form_errors(form.name.name02) }}*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__kana">*/
/*                         <dt>{{ form_label(form.kana) }}</dt>*/
/*                         <dd class="form-group input_name">*/
/*                             {{ form_widget(form.kana.kana01) }}*/
/*                             {{ form_widget(form.kana.kana02) }}*/
/*                             {{ form_errors(form.kana.kana01) }}*/
/*                             {{ form_errors(form.kana.kana02) }}*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__company_name">*/
/*                         <dt>{{ form_label(form.company_name) }}</dt>*/
/*                         <dd class="form-group input_name">*/
/*                             {{ form_widget(form.company_name) }}*/
/*                             {{ form_errors(form.company_name) }}*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__address_detail">*/
/*                         <dt>{{ form_label(form.address) }}</dt>*/
/*                         <dd>*/
/*                             <div id="top_box__zip" class="form-group form-inline input_zip {% if form.zip.zip01.vars.errors is not empty or form.zip.zip02.vars.errors is not empty %}has-error{% endif %}">{{ form_widget(form.zip) }}</div>*/
/*                             <div id="top_box__address" class="{% if form.address.pref.vars.errors is not empty or form.address.addr01.vars.errors is not empty or form.address.addr02.vars.errors is not empty %}has-error{% endif %}">*/
/*                                 {{ form_widget(form.address) }}*/
/*                                 {{ form_errors(form.address) }}*/
/*                             </div>*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__tel">*/
/*                         <dt>{{ form_label(form.tel) }}</dt>*/
/*                         <dd>*/
/*                             <div class="form-inline form-group input_tel">*/
/*                                 {{ form_widget(form.tel, {attr : {class : 'short'}}) }}*/
/*                                 {{ form_errors(form.tel) }}*/
/*                             </div>*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__fax">*/
/*                         <dt>{{ form_label(form.fax) }}</dt>*/
/*                         <dd>*/
/*                             <div class="form-inline form-group input_tel">*/
/*                                 {{ form_widget(form.fax, {attr : {class : 'short'}}) }}*/
/*                                 {{ form_errors(form.fax) }}*/
/*                             </div>*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__email">*/
/*                         <dt>{{ form_label(form.email) }}</dt>*/
/*                         <dd>*/
/*                             {% for emailField in form.email %}*/
/*                             <div class="form-group {% if emailField.vars.errors is not empty %}has-error{% endif %}">*/
/*                                 {{ form_widget(emailField) }}*/
/*                                 {{ form_errors(emailField) }}*/
/*                             </div>*/
/*                             {% endfor %}*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__password">*/
/*                         <dt>{{ form_label(form.password) }}</dt>*/
/*                         <dd>*/
/*                             {% for passwordField in form.password %}*/
/*                             <div class="form-group {% if passwordField.vars.errors is not empty %}has-error{% endif %}">*/
/*                                 {{ form_widget(passwordField, { type : 'password' }) }}*/
/*                                 {{ form_errors(passwordField) }}*/
/*                             </div>*/
/*                             {% endfor %}*/
/*                         </dd>*/
/*                     </dl>*/
/*                 </div>*/
/*                 <div id="top_box__birth" class="dl_table not_required">*/
/*                     <dl>*/
/*                         <dt>{{ form_label(form.birth) }}</dt>*/
/*                         <dd>*/
/*                             <div class="form-group form-inline">*/
/*                                 {{ form_widget(form.birth) }}*/
/*                                 {{ form_errors(form.birth) }}*/
/*                             </div>*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl>*/
/*                         <dt id="top_box__sex">{{ form_label(form.sex) }}</dt>*/
/*                         <dd>*/
/*                             <div class="form-group form-inline">*/
/*                                 {{ form_widget(form.sex) }}*/
/*                                 {{ form_errors(form.sex) }}*/
/*                             </div>*/
/*                         </dd>*/
/*                     </dl>*/
/*                     <dl id="top_box__job">*/
/*                         <dt>{{ form_label(form.job) }}</dt>*/
/*                         <dd>*/
/*                             <div class="form-group form-inline">*/
/*                                 {{ form_widget(form.job) }}*/
/*                                 {{ form_errors(form.job) }}*/
/*                             </div>*/
/*                         </dd>*/
/*                     </dl>{#*/
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
/* <dl>*/
/* <dt>メールマガジン送付について<span class="required">必須</span></dt>*/
/* <dd>*/
/*     <div class="form-group form-inline">*/
/*         {{ form_widget(form.mailmaga_flg) }}*/
/*         {{ form_errors(form.mailmaga_flg) }}*/
/*     </div>*/
/* </dd>*/
/* </dl>*/
/* */
/*                 </div>*/
/*                 {% for f in form %}*/
/*                     {% if f.vars.name matches '[^plg*]' %}*/
/*                         <div class="extra-form dl_table">*/
/*                             {{ form_row(f) }}*/
/*                         </div>*/
/*                     {% endif %}*/
/*                 {% endfor %}*/
/*                 <input id="top_box__hidden_mode" type="hidden" name="mode" value="confirm">*/
/*                 <p id="top_box__agreement" class="form_terms_link"><a href="{{ url('help_agreement') }}" target="_blank">利用規約</a>に同意してお進みください*/
/*                 </p>*/
/* */
/*                 <div id="top_box__footer" class="row no-padding">*/
/*                     <div id="top_box__button_menu" class="btn_group col-sm-offset-4 col-sm-4">*/
/*                         <p>*/
/*                             <button type="submit" class="btn btn-primary btn-block">同意する</button>*/
/*                         </p>*/
/*                         <p><a href="{{ url('index') }}" class="btn btn-info btn-block">同意しない</a></p>*/
/*                     </div>*/
/*                 </div>*/
/*             </form>*/
/*         </div>*/
/*         <!-- /.col -->*/
/*     </div>*/
/*     <!-- /.row -->*/
/* </div>*/
/* {% endblock %}*/
