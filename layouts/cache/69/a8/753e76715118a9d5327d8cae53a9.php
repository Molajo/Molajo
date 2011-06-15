<?php

/* /twig/layouts/twigtest.php */
class __TwigTemplate_69a8753e76715118a9d5327d8cae53a9 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo "  <div id=\"content\">";
        $this->displayBlock('content', $context, $blocks);
        echo "</div>
  <div id=\"footer\">
    ";
        // line 3
        $this->displayBlock('footer', $context, $blocks);
        // line 6
        echo "  </div>";
    }

    // line 1
    public function block_content($context, array $blocks = array())
    {
    }

    // line 3
    public function block_footer($context, array $blocks = array())
    {
        // line 4
        echo "      &copy; Copyright 2009 by <a href=\"http://domain.invalid/\">you</a>.
    ";
    }

    public function getTemplateName()
    {
        return "/twig/layouts/twigtest.php";
    }

    public function isTraitable()
    {
        return false;
    }
}
