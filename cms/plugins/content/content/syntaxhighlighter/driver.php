<?php
/**
 * @package     Molajo
 * @subpackage  Content Plugin
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoContentSyntaxHighlighter
{

    /**
     * code
     *
     * @var    string
     * @access    public
     */
    protected $code;

    /**
     * parameters
     *
     * @var    string
     * @access    public
     */
    protected $parameters;

    /**
     * parameters
     *
     * @var    string
     * @access    public
     */
    protected $languageAlias;

    /**
     * MolajoContentSyntaxHighlighter::driver
     *
     * Implements Alex Gorbatchev Syntax Highlighter
     *
     * From http://alexgorbatchev.com/SyntaxHighlighter/
     *
     * @param    string        The context for the content passed to the plugin.
     * @param    object        The content object.
     * @param    object        The content parameters
     * @param    string        The 'page' number
     * @param   string          Then name of the text field in the content object
     * @return    string
     * @since    1.6
     */
    function driver($context, &$content, &$parameters, $page, $location)
    {
        /** parameters **/
        $molajoSystemPlugin =& MolajoPlugin::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);
        if ($systemParameters->def('enable_syntax_highlighter', 0) == 0) {
            return;
        }

        $temp = $content->$location;

        /** simple performance check to determine whether to process further or not  **/
        if (JString::strpos($temp, '{code:') === false) {
            return;
        }

        /** this static variable will be used to make sure that the js command is only executed once.  **/
        $jsLoaded = false;

        /** this will hold all the class names of the pre tags.  **/
        $aliases = array();

        /** replace: {code:php}blabla{/code} with <pre name="code" class="php">blablaM/pre> **/
        while (preg_match('~{code:([^}]+)}(.*?){/code}~is', $temp, $codeMatches)) {

            /** model **/
            $this->languageAlias = $codeMatches[1];
            $aliases[] = $this->languageAlias;
            $this->code = $codeMatches[2];

            preg_match('~{parm:([^}]+)}(.*?)}~is', $this->code, $parmMatches);

            if (count($parmMatches) > 0) {
                $this->parameters = $parmMatches[1];
                $this->code = substr($parmMatches[0], strlen($parmMatches[1]) + 7, 99999);
            } else {
                $this->parameters = '';
            }

            MolajoContentSyntaxHighlighter::_processCode();

            /** layout **/
            $layoutPath = MolajoPlugin::getLayoutPath(array('type' => 'molajo', 'name' => 'content'), $layout = 'syntaxhighlighter');
            $renderedLayout = MolajoPlugin::generateLayout($layoutPath);

            $temp = str_replace($codeMatches[0], $renderedLayout, $temp);
        }

        /** first we take the necessary file names from our helper method  **/
        $jstoload = MolajoContentSyntaxHighlighter::_getFilenames($aliases);

        /** then we add the .css and the needed .js files  **/
        if (count($jstoload) > 0) {

            $document =& MolajoFactory::getDocument();
            $scriptsfolder = JURI::base() . 'media/syntaxhighlighter/scripts/';
            $stylesfolder = JURI::base() . 'media/syntaxhighlighter/styles/';

            if ($jsLoaded == false) {

                $jsLoaded = true;
                $document->addStyleSheet($stylesfolder . 'shCore.css');
                $document->addStyleSheet($stylesfolder . 'molajo.css');

                $selectedTemplate = $systemParameters->def('syntax_highlighter_template', 'shThemeDefault.css');
                if ($selectedTemplate == 'shThemeDjango.css') {
                } else if ($selectedTemplate == 'shThemeEclipse.css') {
                } else if ($selectedTemplate == 'shThemeEmacs.css') {
                } else if ($selectedTemplate == 'shThemeFadeToGrey.css') {
                } else if ($selectedTemplate == 'shThemeMDUltra.css') {
                } else if ($selectedTemplate == 'shThemeMidnight.css') {
                } else if ($selectedTemplate == 'shThemeRDark.css') {
                } else {
                    $selectedTemplate = 'shThemeDefault.css';
                }
                $document->addStyleSheet($stylesfolder . $selectedTemplate);
                $document->addScript($scriptsfolder . 'shCore.js');

                $js = "window.addEvent('domready', function() { " . "\n";
                $js .= '    SyntaxHighlighter.config.stripBrs = true;' . "\n";
                $js .= '    SyntaxHighlighter.all();' . "\n";
                $js .= '  });' . "\n";
                $document->addScriptDeclaration($js);
            }
            foreach ($jstoload as $j) {
                $document->addScript($scriptsfolder . 'shBrush' . $j . '.js');
            }
        }
        $content->$location = $temp;
        return true;
    }

    function _processCode()
    {
        $html_entities_match = array("|\<br \/\>|", "#<#", "#>#", "|&#39;|", '#&quot;#', '#&nbsp;#');
        $html_entities_replace = array("\n", '&lt;', '&gt;', "'", '"', ' ');
        $this->code = preg_replace($html_entities_match, $html_entities_replace, $this->code);
        $this->code = str_replace('&lt;', '<', $this->code);
        $this->code = str_replace('&gt;', '>', $this->code);
        $this->code = str_replace("\t", '  ', $this->code);
        $this->code = str_ireplace('</p>', '', $this->code);
        trim($this->code);

        return;
    }

    function _getFilenames($aliases)
    {
        $add = array();
        foreach ($aliases as $a) {

            switch ($a) {
                case 'cpp':
                case 'c':
                case 'c++':
                    $add[] = 'Cpp';
                    break;
                case 'c#':
                case 'c-sharp':
                case 'csharp':
                    $add[] = 'CSharp';
                    break;
                case 'css':
                    $add[] = 'Css';
                    break;
                case 'delphi':
                case 'pascal':
                    $add[] = 'Delphi';
                    break;
                case 'java':
                    $add[] = 'Java';
                    break;
                case 'js':
                case 'jscript':
                case 'javascript':
                    $add[] = 'JScript';
                    break;
                case 'php':
                    $add[] = 'Php';
                    break;
                case 'py':
                case 'python':
                    $add[] = 'Python';
                    break;
                case 'rb':
                case 'ruby';
                case 'rails';
                case 'ror';
                    $add[] = 'Ruby';
                    break;
                case 'sql':
                    $add[] = 'Sql';
                    break;
                case 'vb':
                case 'vb.net':
                    $add[] = 'Vb';
                    break;
                case 'xml':
                case 'html':
                case 'xhtml':
                case 'xslt':
                    $add[] = 'Xml';
            }
        }
        return array_unique($add);
    }
}