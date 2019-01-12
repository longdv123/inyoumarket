<?php

/* Form/form_layout.twig */
class __TwigTemplate_b482cbbe3cf3c4977656cac73789e2a08e2400c475a86737ca511d2edcdad9ed extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 22
        $this->parent = $this->loadTemplate("bootstrap_3_layout.html.twig", "Form/form_layout.twig", 22);
        $this->blocks = array(
            'form_widget_compound' => array($this, 'block_form_widget_compound'),
            'form_row' => array($this, 'block_form_row'),
            'form_errors' => array($this, 'block_form_errors'),
            'form_widget' => array($this, 'block_form_widget'),
            'form_widget_simple' => array($this, 'block_form_widget_simple'),
            'hidden_row' => array($this, 'block_hidden_row'),
            'textarea_widget' => array($this, 'block_textarea_widget'),
            'choice_widget_collapsed' => array($this, 'block_choice_widget_collapsed'),
            'choice_widget_expanded' => array($this, 'block_choice_widget_expanded'),
            'checkbox_widget' => array($this, 'block_checkbox_widget'),
            'radio_widget' => array($this, 'block_radio_widget'),
            'password_widget' => array($this, 'block_password_widget'),
            'date_widget' => array($this, 'block_date_widget'),
            'name_widget' => array($this, 'block_name_widget'),
            'tel_widget' => array($this, 'block_tel_widget'),
            'fax_widget' => array($this, 'block_fax_widget'),
            'zip_widget' => array($this, 'block_zip_widget'),
            'address_widget' => array($this, 'block_address_widget'),
            'form_label' => array($this, 'block_form_label'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "bootstrap_3_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 24
    public function block_form_widget_compound($context, array $blocks = array())
    {
        // line 25
        if ($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "freeze_display_text", array())) {
            // line 26
            echo "<div class=\"dl_table\" ";
            $this->displayBlock("widget_container_attributes", $context, $blocks);
            echo ">";
            // line 27
            if ((twig_test_empty($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "parent", array())) && (twig_length_filter($this->env, (isset($context["errors"]) ? $context["errors"] : null)) > 0))) {
                // line 28
                echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'errors');
            }
            // line 30
            $this->displayBlock("form_rows", $context, $blocks);
            // line 31
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'rest');
            // line 32
            echo "</div>";
        } else {
            // line 34
            $this->displayBlock("form_rows", $context, $blocks);
            // line 35
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'rest');
        }
    }

    // line 39
    public function block_form_row($context, array $blocks = array())
    {
        // line 40
        if ($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "freeze_display_text", array())) {
            // line 41
            $context["attr"] = twig_array_merge((isset($context["attr"]) ? $context["attr"] : null), array("class" => (($this->getAttribute((isset($context["attr"]) ? $context["attr"] : null), "class", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["attr"]) ? $context["attr"] : null), "class", array()), "")) : (""))));
            // line 42
            echo "    <dl>
        <dt>";
            // line 43
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'label');
            echo "</dt>
        <dd class=\"form-group";
            // line 44
            if ( !(isset($context["valid"]) ? $context["valid"] : null)) {
                echo " has-error";
            }
            if (twig_in_filter("middle", $this->getAttribute((isset($context["attr"]) ? $context["attr"] : null), "class", array()))) {
                echo " input_name";
            }
            if (twig_in_filter("short", $this->getAttribute((isset($context["attr"]) ? $context["attr"] : null), "class", array()))) {
                echo " input_tel";
            }
            if (twig_in_filter("zip", $this->getAttribute((isset($context["attr"]) ? $context["attr"] : null), "class", array()))) {
                echo " input_zip";
            }
            echo "\">
            ";
            // line 45
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'widget');
            echo "
            ";
            // line 46
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'errors');
            echo "
        </dd>
    </dl>";
        } else {
            // line 50
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'errors');
            // line 51
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'widget');
        }
    }

    // line 55
    public function block_form_errors($context, array $blocks = array())
    {
        // line 56
        if ((twig_length_filter($this->env, (isset($context["errors"]) ? $context["errors"] : null)) > 0)) {
            // line 57
            if ($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "parent", array())) {
                // line 58
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable((isset($context["errors"]) ? $context["errors"] : null));
                foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                    // line 59
                    echo "<p class=\"errormsg text-danger\">";
                    echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans($this->getAttribute($context["error"], "message", array())), "html", null, true);
                    echo "</p>";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
            }
        }
    }

    // line 67
    public function block_form_widget($context, array $blocks = array())
    {
        // line 68
        $this->displayParentBlock("form_widget", $context, $blocks);
        // line 69
        if (((isset($context["freeze"]) ? $context["freeze"] : null) == false)) {
            // line 70
            if ((array_key_exists("help", $context) &&  !twig_test_empty((isset($context["help"]) ? $context["help"] : null)))) {
                // line 71
                echo "<p class=\"mini\"><span class=\"attention\">";
                echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans((isset($context["help"]) ? $context["help"] : null), array(), (isset($context["translation_domain"]) ? $context["translation_domain"] : null)), "html", null, true);
                echo "</span></p>";
            }
        }
    }

    // line 76
    public function block_form_widget_simple($context, array $blocks = array())
    {
        // line 77
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 78
            $context["type"] = "hidden";
            // line 79
            if ((isset($context["freeze_display_text"]) ? $context["freeze_display_text"] : null)) {
                // line 80
                echo nl2br(twig_escape_filter($this->env, ((array_key_exists("value", $context)) ? (_twig_default_filter((isset($context["value"]) ? $context["value"] : null), "")) : ("")), "html", null, true));
            }
        }
        // line 83
        $this->displayParentBlock("form_widget_simple", $context, $blocks);
    }

    // line 86
    public function block_hidden_row($context, array $blocks = array())
    {
        // line 87
        if ((isset($context["freeze_display_text"]) ? $context["freeze_display_text"] : null)) {
            // line 88
            echo "<div style=\"display: none\">";
            // line 89
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'widget');
            // line 90
            echo "</div>";
        } else {
            // line 92
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : null), 'widget');
        }
    }

    // line 96
    public function block_textarea_widget($context, array $blocks = array())
    {
        // line 97
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 98
            $this->displayBlock("form_widget_simple", $context, $blocks);
        } else {
            // line 100
            $this->displayParentBlock("textarea_widget", $context, $blocks);
            // line 101
            if ((array_key_exists("help", $context) &&  !twig_test_empty((isset($context["help"]) ? $context["help"] : null)))) {
                // line 102
                echo "<p class=\"mini\"><span class=\"attention\">";
                echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans((isset($context["help"]) ? $context["help"] : null), array(), (isset($context["translation_domain"]) ? $context["translation_domain"] : null)), "html", null, true);
                echo "</span></p>";
            }
        }
    }

    // line 107
    public function block_choice_widget_collapsed($context, array $blocks = array())
    {
        // line 108
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 109
            echo "        ";
            $context["flag"] = false;
            // line 110
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["choices"]) ? $context["choices"] : null));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["choice"]) {
                // line 111
                echo "            ";
                if ($this->env->getExtension('form')->isSelectedChoice($context["choice"], (isset($context["value"]) ? $context["value"] : null))) {
                    // line 112
                    if ((isset($context["freeze_display_text"]) ? $context["freeze_display_text"] : null)) {
                        // line 113
                        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans($this->getAttribute($context["choice"], "label", array()), array(), (isset($context["translation_domain"]) ? $context["translation_domain"] : null)), "html", null, true);
                        echo "
                ";
                    }
                    // line 115
                    echo "                <input type=\"hidden\" value=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["choice"], "value", array()), "html", null, true);
                    echo "\" ";
                    $this->displayBlock("widget_attributes", $context, $blocks);
                    echo ">
                ";
                    // line 116
                    $context["flag"] = true;
                    // line 117
                    echo "            ";
                }
                // line 118
                echo "        ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['choice'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 119
            echo "        ";
            if (((isset($context["flag"]) ? $context["flag"] : null) == false)) {
                echo "<input type=\"hidden\" value=\"\" ";
                $this->displayBlock("widget_attributes", $context, $blocks);
                echo ">";
            }
        } else {
            // line 121
            $this->displayParentBlock("choice_widget_collapsed", $context, $blocks);
        }
    }

    // line 126
    public function block_choice_widget_expanded($context, array $blocks = array())
    {
        // line 127
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 128
            if ((isset($context["freeze_display_text"]) ? $context["freeze_display_text"] : null)) {
                // line 129
                echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array(), "any", false, true), "data", array(), "any", false, true), "name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array(), "any", false, true), "data", array(), "any", false, true), "name", array()), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "data", array()))) : ($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "data", array()))), "html", null, true);
            }
            // line 131
            echo "<input type=\"hidden\" value=\"";
            echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array(), "any", false, true), "data", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array(), "any", false, true), "data", array(), "any", false, true), "id", array()), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "data", array()))) : ($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "data", array()))), "html", null, true);
            echo "\" ";
            $this->displayBlock("widget_attributes", $context, $blocks);
            echo ">";
        } else {
            // line 133
            $this->displayParentBlock("choice_widget_expanded", $context, $blocks);
        }
    }

    // line 138
    public function block_checkbox_widget($context, array $blocks = array())
    {
        // line 139
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 140
            if ((isset($context["checked"]) ? $context["checked"] : null)) {
                // line 141
                if ((isset($context["freeze_display_text"]) ? $context["freeze_display_text"] : null)) {
                    // line 142
                    $this->displayBlock("form_label", $context, $blocks);
                }
                // line 144
                echo "<input type=\"hidden\" ";
                $this->displayBlock("widget_attributes", $context, $blocks);
                if (array_key_exists("value", $context)) {
                    echo " value=\"";
                    echo twig_escape_filter($this->env, (isset($context["value"]) ? $context["value"] : null), "html", null, true);
                    echo "\"";
                }
                echo " />";
            }
        } else {
            // line 147
            $this->displayParentBlock("checkbox_widget", $context, $blocks);
        }
    }

    // line 151
    public function block_radio_widget($context, array $blocks = array())
    {
        // line 152
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 153
            if ((isset($context["checked"]) ? $context["checked"] : null)) {
                // line 154
                if ((isset($context["freeze_display_text"]) ? $context["freeze_display_text"] : null)) {
                    // line 155
                    $this->displayBlock("form_label", $context, $blocks);
                }
                // line 157
                echo "<input type=\"hidden\" ";
                $this->displayBlock("widget_attributes", $context, $blocks);
                if (array_key_exists("value", $context)) {
                    echo " value=\"";
                    echo twig_escape_filter($this->env, (isset($context["value"]) ? $context["value"] : null), "html", null, true);
                    echo "\"";
                }
                echo " />";
            }
        } else {
            // line 160
            $this->displayParentBlock("radio_widget", $context, $blocks);
        }
    }

    // line 165
    public function block_password_widget($context, array $blocks = array())
    {
        // line 166
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 167
            echo "<input type=\"hidden\" ";
            $this->displayBlock("widget_attributes", $context, $blocks);
            echo " value=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "data", array()), "html", null, true);
            echo "\" />";
        } else {
            // line 169
            $this->displayParentBlock("password_widget", $context, $blocks);
        }
    }

    // line 174
    public function block_date_widget($context, array $blocks = array())
    {
        // line 175
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 176
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                // line 177
                if ( !twig_test_empty($this->getAttribute($this->getAttribute($context["child"], "vars", array()), "value", array()))) {
                    // line 178
                    echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'widget');
                    // line 179
                    if (($this->getAttribute($context["loop"], "last", array()) == false)) {
                        echo "/";
                    }
                }
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } else {
            // line 183
            $this->displayParentBlock("date_widget", $context, $blocks);
        }
    }

    // line 192
    public function block_name_widget($context, array $blocks = array())
    {
        // line 193
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 194
            if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
                // line 195
                echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'widget');
            } else {
                // line 197
                echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'widget');
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 200
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 201
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'errors');
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    // line 205
    public function block_tel_widget($context, array $blocks = array())
    {
        // line 206
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 207
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'widget', array("style" => "ime-mode: disabled;"));
            // line 208
            if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
                // line 209
                if ( !twig_test_empty($this->getAttribute($this->getAttribute($context["child"], "vars", array()), "value", array()))) {
                    // line 210
                    if (($this->getAttribute($context["loop"], "last", array()) == false)) {
                        echo "&nbsp;-&nbsp;";
                    }
                }
            } else {
                // line 213
                if (($this->getAttribute($context["loop"], "last", array()) == false)) {
                    echo "&nbsp;-&nbsp;";
                }
            }
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 216
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 217
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'errors');
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    // line 221
    public function block_fax_widget($context, array $blocks = array())
    {
        // line 222
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 223
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'widget', array("attr" => array("style" => "ime-mode: disabled;")));
            // line 224
            if (($this->getAttribute($context["loop"], "last", array()) == false)) {
                echo "&nbsp;-&nbsp;";
            }
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 226
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 227
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'errors');
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    // line 231
    public function block_zip_widget($context, array $blocks = array())
    {
        // line 232
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 233
            echo "〒&nbsp;";
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "zip01_name", array()), array(), "array"), 'widget');
            echo "&nbsp;-&nbsp;";
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "zip02_name", array()), array(), "array"), 'widget');
        } else {
            // line 235
            echo "〒";
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "zip01_name", array()), array(), "array"), 'widget', array("id" => "zip01", "attr" => array("class" => "form-control", "style" => "ime-mode: disabled;")));
            echo "-";
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "zip02_name", array()), array(), "array"), 'widget', array("id" => "zip02", "attr" => array("class" => "form-control", "style" => "ime-mode: disabled;")));
            echo " <span class=\"question-circle\"><svg class=\"cb cb-question\"><use xlink:href=\"#cb-question\" /></svg></span> <a href=\"http://www.post.japanpost.jp/zipcode/\" target=\"_blank\">郵便番号検索</a>";
            // line 236
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                // line 237
                echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'errors');
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 239
            echo "<div class=\"zip-search\"><button type=\"button\" id=\"zip-search\" class=\"btn btn-default btn-sm\">郵便番号から自動入力</button></div>";
        }
    }

    // line 243
    public function block_address_widget($context, array $blocks = array())
    {
        // line 244
        echo "<div class=\"form-inline form-group input_zip\">";
        // line 245
        echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "pref_name", array()), array(), "array"), 'widget', array("id" => "pref"));
        // line 246
        echo "</div>";
        // line 247
        if ((isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 248
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "addr01_name", array()), array(), "array"), 'widget');
            // line 249
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "addr02_name", array()), array(), "array"), 'widget');
        } else {
            // line 251
            echo "<div class=\"form-group\">";
            // line 252
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "addr01_name", array()), array(), "array"), 'widget', array("id" => "addr01", "attr" => array("style" => "ime-mode: active;", "placeholder" => "form.address1.help")));
            echo "<br />
        </div>
        <div class=\"form-group\">";
            // line 255
            echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($this->getAttribute((isset($context["form"]) ? $context["form"] : null), $this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : null), "vars", array()), "addr02_name", array()), array(), "array"), 'widget', array("id" => "addr02", "attr" => array("style" => "ime-mode: active;", "placeholder" => "form.address2.help")));
            echo "<br />
        </div>";
            // line 257
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["form"]) ? $context["form"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
                // line 258
                echo $this->env->getExtension('form')->renderer->searchAndRenderBlock($context["child"], 'errors');
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
    }

    // line 263
    public function block_form_label($context, array $blocks = array())
    {
        // line 264
        $this->displayParentBlock("form_label", $context, $blocks);
        // line 265
        if ( !(isset($context["freeze"]) ? $context["freeze"] : null)) {
            // line 266
            if ((isset($context["required"]) ? $context["required"] : null)) {
                echo "<span class=\"required\">必須</span>";
            }
        }
    }

    public function getTemplateName()
    {
        return "Form/form_layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  654 => 266,  652 => 265,  650 => 264,  647 => 263,  638 => 258,  634 => 257,  630 => 255,  625 => 252,  623 => 251,  620 => 249,  618 => 248,  616 => 247,  614 => 246,  612 => 245,  610 => 244,  607 => 243,  602 => 239,  596 => 237,  592 => 236,  586 => 235,  580 => 233,  578 => 232,  575 => 231,  567 => 227,  563 => 226,  547 => 224,  545 => 223,  528 => 222,  525 => 221,  517 => 217,  513 => 216,  496 => 213,  490 => 210,  488 => 209,  486 => 208,  484 => 207,  467 => 206,  464 => 205,  456 => 201,  452 => 200,  445 => 197,  442 => 195,  440 => 194,  436 => 193,  433 => 192,  428 => 183,  410 => 179,  408 => 178,  406 => 177,  389 => 176,  387 => 175,  384 => 174,  379 => 169,  372 => 167,  370 => 166,  367 => 165,  362 => 160,  351 => 157,  348 => 155,  346 => 154,  344 => 153,  342 => 152,  339 => 151,  334 => 147,  323 => 144,  320 => 142,  318 => 141,  316 => 140,  314 => 139,  311 => 138,  306 => 133,  299 => 131,  296 => 129,  294 => 128,  292 => 127,  289 => 126,  284 => 121,  276 => 119,  262 => 118,  259 => 117,  257 => 116,  250 => 115,  245 => 113,  243 => 112,  240 => 111,  222 => 110,  219 => 109,  217 => 108,  214 => 107,  206 => 102,  204 => 101,  202 => 100,  199 => 98,  197 => 97,  194 => 96,  189 => 92,  186 => 90,  184 => 89,  182 => 88,  180 => 87,  177 => 86,  173 => 83,  169 => 80,  167 => 79,  165 => 78,  163 => 77,  160 => 76,  152 => 71,  150 => 70,  148 => 69,  146 => 68,  143 => 67,  131 => 59,  127 => 58,  125 => 57,  123 => 56,  120 => 55,  115 => 51,  113 => 50,  107 => 46,  103 => 45,  88 => 44,  84 => 43,  81 => 42,  79 => 41,  77 => 40,  74 => 39,  69 => 35,  67 => 34,  64 => 32,  62 => 31,  60 => 30,  57 => 28,  55 => 27,  51 => 26,  49 => 25,  46 => 24,  11 => 22,);
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
/* {%- extends 'bootstrap_3_layout.html.twig' -%}*/
/* */
/* {%- block form_widget_compound -%}*/
/*     {%- if form.vars.freeze_display_text -%}*/
/*         <div class="dl_table" {{ block('widget_container_attributes') }}>*/
/*             {%- if form.parent is empty and errors|length > 0 -%}*/
/*                     {{- form_errors(form) -}}*/
/*             {%- endif -%}*/
/*             {{- block('form_rows') -}}*/
/*             {{- form_rest(form) -}}*/
/*         </div>*/
/*     {%- else -%}*/
/*         {{- block('form_rows') -}}*/
/*         {{- form_rest(form) -}}*/
/*     {%- endif -%}*/
/* {%- endblock form_widget_compound -%}*/
/* */
/* {% block form_row -%}*/
/*     {%- if form.vars.freeze_display_text -%}*/
/*     {% set attr = attr|merge({class: attr.class|default('')}) %}*/
/*     <dl>*/
/*         <dt>{{ form_label(form) }}</dt>*/
/*         <dd class="form-group{% if not valid %} has-error{% endif %}{% if 'middle' in attr.class %} input_name{% endif %}{% if 'short' in attr.class %} input_tel{% endif %}{% if 'zip' in attr.class %} input_zip{% endif %}">*/
/*             {{ form_widget(form) }}*/
/*             {{ form_errors(form) }}*/
/*         </dd>*/
/*     </dl>*/
/*     {%- else -%}*/
/*         {{- form_errors(form) -}}*/
/*         {{- form_widget(form) -}}*/
/*     {%- endif -%}*/
/* {%- endblock form_row %}*/
/* */
/* {% block form_errors -%}*/
/*     {% if errors|length > 0 -%}*/
/*         {% if form.parent %}*/
/*             {%- for error in errors -%}*/
/*                 <p class="errormsg text-danger">{{ error.message |trans }}</p>*/
/*             {%- endfor -%}*/
/*         {%- endif %}*/
/*     {%- endif %}*/
/* {%- endblock form_errors %}*/
/* */
/* {# Widgets #}*/
/* */
/* {%- block form_widget -%}*/
/*     {{- parent() -}}*/
/*     {%- if freeze == false -%}*/
/*         {%- if help is defined and help is not empty -%}*/
/*             <p class="mini"><span class="attention">{{ help|trans({}, translation_domain) }}</span></p>*/
/*         {%- endif -%}*/
/*     {%- endif -%}*/
/* {%- endblock form_widget -%}*/
/* */
/* {%- block form_widget_simple -%}*/
/*     {%- if freeze -%}*/
/*         {%- set type = 'hidden' -%}*/
/*         {%- if freeze_display_text -%}*/
/*             {{- value|default('')|nl2br -}}*/
/*         {%- endif -%}*/
/*     {%- endif -%}*/
/*     {{- parent() -}}*/
/* {%- endblock form_widget_simple -%}*/
/* */
/* {%- block hidden_row -%}*/
/*     {%- if freeze_display_text -%}*/
/*         <div style="display: none">*/
/*             {{- form_widget(form) -}}*/
/*         </div>*/
/*     {%- else -%}*/
/*         {{- form_widget(form) -}}*/
/*     {%- endif -%}*/
/* {%- endblock hidden_row -%}*/
/* */
/* {%- block textarea_widget -%}*/
/*     {%- if freeze -%}*/
/*         {{- block('form_widget_simple') -}}*/
/*     {%- else -%}*/
/*         {{- parent() -}}*/
/*         {%- if help is defined and help is not empty -%}*/
/*             <p class="mini"><span class="attention">{{ help|trans({}, translation_domain) }}</span></p>*/
/*         {%- endif -%}*/
/*     {%- endif -%}*/
/* {%- endblock textarea_widget -%}*/
/* */
/* {%- block choice_widget_collapsed -%}*/
/*     {% if freeze %}*/
/*         {% set flag = false %}*/
/*         {% for choice in choices %}*/
/*             {% if choice is selectedchoice(value) %}*/
/*                 {%- if freeze_display_text -%}*/
/*                     {{ choice.label|trans({}, translation_domain) }}*/
/*                 {% endif %}*/
/*                 <input type="hidden" value="{{ choice.value }}" {{ block('widget_attributes') }}>*/
/*                 {% set flag = true %}*/
/*             {% endif %}*/
/*         {% endfor %}*/
/*         {% if flag == false %}<input type="hidden" value="" {{ block('widget_attributes') }}>{% endif %}*/
/*     {%- else -%}*/
/*         {{- parent() -}}*/
/*     {%- endif -%}*/
/* {%- endblock choice_widget_collapsed -%}*/
/* */
/* */
/* {%- block choice_widget_expanded -%}*/
/*     {% if freeze %}*/
/*         {%- if freeze_display_text -%}*/
/*             {{ form.vars.data.name|default(form.vars.data) }}*/
/*         {%- endif -%}*/
/*         <input type="hidden" value="{{ form.vars.data.id|default(form.vars.data) }}" {{ block('widget_attributes') }}>*/
/*     {%- else -%}*/
/*         {{- parent() -}}*/
/*     {%- endif -%}*/
/* {%- endblock choice_widget_expanded -%}*/
/* */
/* */
/* {%- block checkbox_widget -%}*/
/*     {%- if freeze -%}*/
/*         {%- if checked -%}*/
/*             {%- if freeze_display_text -%}*/
/*                 {{ block('form_label') }}*/
/*             {%- endif -%}*/
/*             <input type="hidden" {{ block('widget_attributes') }}{% if value is defined %} value="{{ value }}"{% endif %} />*/
/*         {%- endif -%}*/
/*     {%- else -%}*/
/*         {{- parent() -}}*/
/*     {%- endif -%}*/
/* {%- endblock checkbox_widget -%}*/
/* */
/* {%- block radio_widget -%}*/
/*     {%- if freeze -%}*/
/*         {%- if checked -%}*/
/*             {%- if freeze_display_text -%}*/
/*                 {{ block('form_label') }}*/
/*             {%- endif -%}*/
/*             <input type="hidden" {{ block('widget_attributes') }}{% if value is defined %} value="{{ value }}"{% endif %} />*/
/*         {%- endif -%}*/
/*     {%- else -%}*/
/*         {{- parent() -}}*/
/*     {%- endif -%}*/
/* {%- endblock radio_widget -%}*/
/* */
/* */
/* {%- block password_widget -%}*/
/*     {%- if freeze -%}*/
/*         <input type="hidden" {{ block('widget_attributes') }} value="{{ form.vars.data }}" />*/
/*         {%- else -%}*/
/*         {{- parent() -}}*/
/*     {%- endif -%}*/
/* {%- endblock password_widget -%}*/
/* */
/* */
/* {%- block date_widget -%}*/
/*     {%- if freeze -%}*/
/*     {%- for child in form %}*/
/*             {%- if child.vars.value is not empty -%}*/
/*             {{ form_widget(child) }}*/
/*             {%- if loop.last == false%}/{% endif -%}*/
/*             {%- endif -%}*/
/*     {% endfor -%}*/
/*     {%- else -%}*/
/*         {{- parent() -}}*/
/*     {%- endif -%}*/
/* {%- endblock date_widget -%}*/
/* */
/* */
/* {# EC-CUBE Widget #}*/
/* */
/* */
/* */
/* {%- block name_widget -%}*/
/*     {%- for child in form %}*/
/*         {%- if freeze -%}*/
/*             {{ form_widget(child) }}*/
/*         {%- else -%}*/
/*             {{ form_widget(child) -}}*/
/*         {%- endif -%}*/
/*     {% endfor -%}*/
/*     {%- for child in form %}*/
/*         {{- form_errors(child) -}}*/
/*     {% endfor -%}*/
/* {%- endblock name_widget -%}*/
/* */
/* {%- block tel_widget -%}*/
/*     {%- for child in form %}*/
/*         {{- form_widget(child, {'style': 'ime-mode: disabled;'}) -}}*/
/*         {%- if freeze -%}*/
/*             {%- if child.vars.value is not empty -%}*/
/*                 {%- if loop.last == false%}&nbsp;-&nbsp;{% endif -%}*/
/*             {%- endif -%}*/
/*         {%- else -%}*/
/*             {%- if loop.last == false%}&nbsp;-&nbsp;{% endif -%}*/
/*         {%- endif -%}*/
/*     {% endfor -%}*/
/*     {%- for child in form %}*/
/*         {{- form_errors(child) -}}*/
/*     {% endfor -%}*/
/* {%- endblock tel_widget -%}*/
/* */
/* {%- block fax_widget -%}*/
/*     {%- for child in form %}*/
/*         {{- form_widget(child, {'attr': {'style': 'ime-mode: disabled;'}}) -}}*/
/*         {%- if loop.last == false%}&nbsp;-&nbsp;{% endif -%}*/
/*     {% endfor -%}*/
/*     {%- for child in form %}*/
/*         {{- form_errors(child) -}}*/
/*     {% endfor -%}*/
/* {%- endblock fax_widget -%}*/
/* */
/* {%- block zip_widget -%}*/
/*     {%- if freeze -%}*/
/*         〒&nbsp;{{ form_widget(form[form.vars.zip01_name]) }}&nbsp;-&nbsp;{{- form_widget(form[form.vars.zip02_name]) }}*/
/*     {%- else -%}*/
/*         〒{{- form_widget(form[form.vars.zip01_name], {'id': 'zip01', 'attr': {'class': 'form-control', 'style': 'ime-mode: disabled;'}}) }}-{{- form_widget(form[form.vars.zip02_name], {'id': 'zip02', 'attr': {'class': 'form-control', 'style': 'ime-mode: disabled;'}}) }} <span class="question-circle"><svg class="cb cb-question"><use xlink:href="#cb-question" /></svg></span> <a href="http://www.post.japanpost.jp/zipcode/" target="_blank">郵便番号検索</a>*/
/*         {%- for child in form %}*/
/*             {{- form_errors(child) -}}*/
/*         {% endfor -%}*/
/*         <div class="zip-search"><button type="button" id="zip-search" class="btn btn-default btn-sm">郵便番号から自動入力</button></div>*/
/*     {%- endif -%}*/
/* {%- endblock zip_widget -%}*/
/* */
/* {%- block address_widget -%}*/
/*     <div class="form-inline form-group input_zip">*/
/*     {{- form_widget(form[form.vars.pref_name], {'id': 'pref'}) -}}*/
/*     </div>*/
/*     {%- if freeze -%}*/
/*         {{- form_widget(form[form.vars.addr01_name]) -}}*/
/*         {{- form_widget(form[form.vars.addr02_name]) -}}*/
/*     {%- else -%}*/
/*         <div class="form-group">*/
/*             {{- form_widget(form[form.vars.addr01_name], {'id': 'addr01', 'attr': {'style': 'ime-mode: active;', 'placeholder': 'form.address1.help' }}) -}}<br />*/
/*         </div>*/
/*         <div class="form-group">*/
/*             {{- form_widget(form[form.vars.addr02_name], {'id': 'addr02', 'attr': {'style': 'ime-mode: active;', 'placeholder': 'form.address2.help'}}) -}}<br />*/
/*         </div>*/
/*         {%- for child in form %}*/
/*             {{- form_errors(child) -}}*/
/*         {% endfor -%}*/
/*     {%- endif -%}*/
/* {%- endblock address_widget -%}*/
/* */
/* {%- block form_label -%}*/
/*     {{- parent() -}}*/
/*     {%- if not freeze -%}*/
/*         {%- if required -%}<span class="required">必須</span>{%- endif -%}*/
/*     {%- endif -%}*/
/* {%- endblock -%}*/
