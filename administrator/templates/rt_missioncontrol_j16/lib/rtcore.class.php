<?php
/**
 * @version � 1.6.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted index access');
//define('_COOKIENAME', 'mc-redirect');

class RTCore
{

    var $document;
    var $language;
    var $session;
    var $basePath;
    var $adminPath;
    var $baseUrl;
    var $currentUrl;
    var $templateUrl;
    var $templateUrlAbsolute;
    var $templatePath;
    var $templateName;
    var $user;
    var $toolbar;
    var $toolbar_output;
    var $help;
    var $actions;
    var $first;
    var $bodytags;
    var $updateUrl;
    var $updateSlug;
    var $params;
	var $browser;
	var $_browser_params = array();

    function __construct()
    {
        require_once('rtbrowser.class.php');
        //$this->checkRedirect();
        $this->browser = new RTBrowser();

        // some more init
        $this->basePath = JPATH_ROOT;
        $this->adminPath = $this->basePath . DS . 'administrator';
        $this->templateName = $this->_getCurrentAdminTemplate();
        $this->templatePath = $this->adminPath . DS . 'templates' . DS . $this->templateName;
        $this->templateUrl = $this->baseUrl . 'templates/' . $this->templateName;
        $this->templateUrlAbsolute = JURI::root(true) . '/administrator/' .$this->templateUrl;


        // Set the main class vars to match the call
        JHTML::_('behavior.mootools');
        $doc =& JFactory::getDocument();
        $this->document = $doc;
        $this->user =& JFactory::getUser();
        $this->language = $doc->language;
        $this->direction = $doc->direction;
        $this->session =& JFactory::getSession();
        $this->baseUrl = JURI::root(true) . "/";
        $uri = JURI::getInstance();
        $this->currentUrl = $uri->toString();
        $this->params = $this->getTemplateParams();
    }

    function initRenderer()
    {
        $this->_initToolbar();
        $this->toolbar_output = $this->toolbar->render('toolbar');
        $this->_injectClasses();
	}

	/* not needed */ 
    function getTemplateParams() {

		require_once(dirname(__FILE__) . "/rtparameter.php");

        $app =& JFactory::getApplication();
        $template =  $app->getTemplate(true);
        $params = new RTParameter( $template->params, $this->templatePath);

        return $params;
    }
	
    /* ------ Stylesheet Funcitons  ----------- */

    function addStyle($filename = '')
    {
        if (is_array($filename)) return RTCore::addStyles($filename);

        return $this->_parseBrowserFromName($filename, 'css');
    }

    function addStyles($styles = array())
    {
        foreach ($styles as $style) RTCore::addStyle($style);
    }

    function addInlineStyle($css = '')
    {
        $doc =& $this->document;
        return $doc->addStyleDeclaration($css);
    }

    /* ------ Script Funcitons  ----------- */

    function addScript($filename = '')
    {
        if (is_array($filename)) return RTCore::addScripts($filename);

        return $this->_parseBrowserFromName(RTCore::getMooScriptVersion($filename), 'js');
    }

    function addScripts($scripts = array())
    {
        foreach ($scripts as $script) RTCore::addScript($script);
    }


    function addInlineScript($js = '')
    {
        $doc =& $this->document;
        return $doc->addScriptDeclaration($js);
    }

    function getMooScriptVersion($filename) {
        global $moo_override;

        return str_replace('.js','-mt1.2.js',$filename);

    }

    function addOverrideStyles()
    {
        global $option;

        $override = 'override.css.php';

        $override_file = $this->templatePath . DS . 'overrides' . DS . $option . DS . $override;
        $override_url = $this->templateUrl . '/overrides/' . $option . '/' . $override;

        jimport('joomla.filesystem.file');
        if (JFile::exists($override_file)) {
            $this->document->addStylesheet($override_url);
        }

    }

    function processAjax()
    {
        if (JRequest::getString('process') == 'ajax' && JRequest::getString('model')) {

            $model = $this->getAjaxModel(JRequest::getString('model'));
            if ($model === false) die();
            include_once($model);
            exit;
        } else {
            return true;
        }
        return false;
    }

    function getAjaxModel($model_name)
    {

        $model_path = $this->templatePath . DS . 'ajax-models' . DS . $model_name . '.php';

        if (file_exists($model_path)) {
            return $model_path;
        } else {
            return false;
        }
    }

//    function checkRedirect()
//    {
//        $mainframe =& JFactory::getApplication();
//
//
//        $redirect = Jrequest::getVar(_COOKIENAME, '', 'COOKIE');
//
//        if (isset($redirect) && $redirect != '') {
//            setcookie(_COOKIENAME, '', time() - 3600);
//
//            $messages =& $mainframe->getMessageQueue();
//
//            if (strpos($redirect, 'com_login')===false
//                && empty($messages)
//                && strtolower(JRequest::getString('process')) != 'ajax')
//                JApplication::redirect($redirect);
//        }
//    }
//
//    function storeRedirect()
//    {
//        setcookie(_COOKIENAME, $this->_getCurrentPageURL());
//    }

    function _injectClasses()
    {

        $option = JRequest::getString('option');
        $task = JRequest::getString('task');
        $view = JRequest::getString('view');
		$layout = JRequest::getString('layout');

        $buffer =& $this->document->getBuffer('component');
        
        $filter = "";

        jimport('joomla.filesystem.file');
        $override_replace = $this->templatePath . DS . 'overrides' . DS . $option . DS . 'injections-replace.php';
        $override_post = $this->templatePath . DS . 'overrides' . DS . $option . DS . 'injections-post.php';


        //use phpQuery
        if (!class_exists('phpQuery')) {
            require_once(dirname(__FILE__) . "/phpQuery.php");
        }

        // include any post pq injections for component
        if (JFile::exists($override_replace)) {
            include($override_replace);

        } else {
            $pq = phpQuery::newDocument($buffer);

            // add filter-table class for filter
            ///pq('form[name=adminForm] td:contains("Filter") > input[type=text]')->parents('table')->addClass('mc-filter-table');
            pq('form[name=adminForm] fieldset#filter-bar')->wrapInner('<div class="mc-filter-bar" />');

            // add legend-table class for legend
            //pq('form[name=adminForm] td:contains("toggle state")')->parents('table')->addClass('mc-legend-table');

            // add list table class for main list
            
            pq('table.adminlist ')->addClass('mc-list-table');
           	pq('table.adminlist a[href*="a.ordering"]')->addClass('mc-ordering-label')->parent('th')->addClass('mc-ordering-col'); 
           	
           	pq('fieldset.batch')->wrap('<div class="mc-form-frame mc-padding mc-second-block" />');	
			pq('#component-form')->addClass('adminform');            
            // special cases
            //pq('table.adminlist')->prev('table')->addClass('mc-filter-table');
			

            // edit forms
          	pq('form[name=adminForm] fieldset.adminform:first')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');

            pq('div.col:last')->addClass('mc-last-column');
            pq('.mc-form-frame .fltlft, .mc-form-frame .fltrt')->wrapInner('<div class="mc-panel-padding" />');
            pq('.mc-form-frame .fltlft .mc-panel-padding > input')->parent()->removeClass('mc-panel-padding');
            //pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
            //pq('form[name=adminForm] .mc-form-frame')->addClass('mc-padding');
            pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');
            pq('<div class="clr"></div>')->appendTo('form[name=adminForm] .mc-form-frame');

            //pq('table.mc-filter-table')->parent('div.mc-form-frame')->removeClass('mc-form-frame');
			pq('input#position.combobox')->wrapAll('<div class="mc-position-relative" />');


            // generic first/last classes
            pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
            pq('form[name=adminForm] fieldset#filter-bar')->nextAll('table.mc-first-table')->addClass('mc-second-table')->removeClass('mc-first-table');
            pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
            pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

            //pq('div.pane-sliders > div[style="display:none;"]:last')->parent('div.pane-sliders')->addClass('no-border')

            // non-list tables
            //$filter = pq('form[name=adminForm] table.adminform');
            //$filter->parents('table')->removeClass('mc-first-table')->addClass('mc-nolist-table');

            //$filter = pq('form[name=adminForm] fieldset table.admintable')->removeClass('mc-first-table');

            //save image
            
            

            // include any post pq injections for component
            if (JFile::exists($override_post)) {
                include($override_post);
            }

            $buffer = $pq->getDocument()->htmlOuter();
        }
        //$buffer = "...".$buffer;
        $this->document->setBuffer($buffer, 'component');

    }

    function _initToolbar()
    {
//		if (!class_exists('JToolBar')) {
//            JLoader::register('JToolBar', JPATH_ADMINISTRATOR.'/libraries/joomla/html/toolbar.php');
//            jimport('joomla.html.toolbar');
//        }
        //require_once('rttoolbar.class.php');

        $bar = JToolBar::getInstance('toolbar');
        
        $newbar = array();
        $newhelp = array();
        $actions = array();
        $first = array();
        foreach ($bar->getItems() as $button) {

            if (strtolower($button[0]) == 'help') {
                $newhelp[] = $button;
            } elseif (strtolower($button[1]) == 'unarchive' or
                      strtolower($button[1]) == 'archive' or
                      strtolower($button[1]) == 'publish' or
                      strtolower($button[1]) == 'unpublish' or
                      strtolower($button[1]) == 'move' or
                      strtolower($button[1]) == 'copy' or
                      strtolower($button[1]) == 'trash') {
                $actions[] = $button;
            } else if (strtolower($button[1]) == 'new' or
                       strtolower($button[1]) == 'apply' or
                       strtolower($button[1]) == 'save') {
                $first[] = $button;
            } else {
                $newbar[] = $button;
            }
        }
        //create new toolbar object
        $toolbar = new JToolbar('toolbar');
       
        //$toolbar->_buttonPath[0] = $this->basePath.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'toolbar'.DS.'button';
        
        $toolbar->setToolBar($newbar);
        
        //$toolbar->_bar = $newbar;
        $toolbar->setButtonPath($bar->getButtonPath());
        $toolbar->_actions = $actions;
        $toolbar->_first = $first; 
        $this->toolbar = $toolbar;
        $this->actions = $actions;
        $this->first = $first;

        //new help button
        $helpbar = new JToolbar('help');
        //$helpbar->_buttonPath[0] = $this->basePath.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'toolbar'.DS.'button';
        $helpbar->setToolbar($newhelp);

        $this->help = $helpbar;

    }
    
    function _addListItem($item, $class = null, $link = null, $badge=null)
    {
		if ($item == '___') {$item = '';$class="divider";}
        if ($link != null) $item = '<a href="' . $link . '">' . $item . '</a>';

		if ($badge) $item .= $badge;
        if ($class == null) return $item;

        $chunk = array();
        $chunk[0] = $item;
        $chunk[1] = $class;
        return $chunk;

    }

    function _listify($list, $class = null)
    {

        if (isset($class)) $output = '<ul class="' . $class . '">';
        else $output = '<ul>';

        foreach ($list as $item) {
            if (is_array($item)) {
                $value = $item[0];
                $iclass = $item[1];
            } else {
                $value = $item;
                $iclass = null;
            }
            if (isset($iclass)) $output .= '<li class="' . $iclass . '">' . $value . '</li>';
            else $output .= '<li>' . $value . '</li>';
        }
        $output .= '</ul>';

        return $output;

    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    function _getGravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
         // Added to setect whether to use HTTP or HTTPS:
        $mode = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
		$url = $mode.'://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val)
            $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }

    function _parseBrowserFromName($filename, $type = 'css')
    {

        $ext = substr($filename, strrpos($filename, '.'));
        $filename = substr($filename, 0, strrpos($filename, '.'));

        if (!preg_match("/^http(s?):/", $filename)) $filename = $this->templateUrl . '/' . $type . '/' . $filename;
        else return true;

        $checks = $this->browser->_checks;

        // Add RTL if enabled
        if ($this->document->direction == 'rtl') $checks[] = '-rtl';

        foreach ($checks as $check) {

            if (file_exists($this->adminPath . DS . $filename . $check . $ext)) {
                if ($type == 'js') $this->document->addScript($filename . $check . $ext);
                else $this->document->addStylesheet($filename . $check . $ext);
            }
        }

        return true;
    }

    function _getTools()
    {

        $user = & JFactory::getUser();

        // cache some acl checks
        $canCheckin = $user->authorise('admin', 'com_checkin');
        $canCache = $user->authorise('manage', 'com_cache');
        $canAdmin = $user->authorise('admin');

        if ($canCheckin || $canMassMail) {
            $tools = array();

            if ($canCheckin) {
				//index.php?process=ajax&model=quickcheckin
				require_once('rtcheckin.class.php');
				$tools[] = $this->_addListItem(JText::_('Quick-Checkin'), 'qci', '#', '<span class="badge number">'.RTCheckin::getCheckouts(true).'</span>');
                $tools[] = $this->_addListItem(JText::_('Checkin Manager'), 'checkin', 'index.php?option=com_checkin');
				$tools[] = $this->_addListItem('___');
            }
            if ($canCache) {
				// index.php?process=ajax&model=quickcachecleaner
				require_once('rtcachecleaner.class.php');
				$tools[] = $this->_addListItem(JText::_('Quick-Cache-Clean'), 'qcc', '#', '<span class="badge number">'.RTCacheClean::getCount().'</span>');
	            $tools[] = $this->_addListItem(JText::_('Cache Manager'), 'config', 'index.php?option=com_cache');
	            $tools[] = $this->_addListItem(JText::_('Purge Expired Cache'), 'config', 'index.php?option=com_cache&view=purge');
				$tools[] = $this->_addListItem('___');
	
			}
			if ($canAdmin) {
				$tools[] = $this->_addListItem(JText::_('System Information'), 'sysinfo', 'index.php?option=com_admin&view=sysinfo');
			}
            return $this->_listify($tools, 'mc-dropdown');
        }
        return false;

    }

    /**
     * @return
     */
    function _getTemplateName()
    {
        $cid = JRequest::getVar('cid');
        if (is_array($cid))
            return $cid[0];
        else
            return null;
    }

    function _getAdminTemplate()
    {
        global $mainframe, $option;
        $template = null;
        $task = JRequest::getCmd('task');
        $client =& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

        if ($option == 'com_templates' && $task == 'edit' && $client->id == 1 && array_key_exists('cid', $_REQUEST)) {
            $template = $_REQUEST['cid'][0];
        }
        else {
            $template = $mainframe->getTemplate();
        }

        return $template;
    }

    function _getCurrentAdminTemplate()
    {

        $db =& JFactory::getDBO();
        $db->setQuery('select template from #__template_styles where application_id = 1 and home = 1');
        $template = $db->loadResult();

        return $template;
    }

    function _getCurrentSiteTemplate()
    {

        $db =& JFactory::getDBO();
        $db->setQuery('select template from #__template_styles where application_id = 0 and home = 1');
        $template = $db->loadResult();

        return $template;
    }

    function _isGantrySiteTemplate()
    {

        $libPath = $this->basePath . DS . 'templates' . DS . $this->_getCurrentSiteTemplate() . DS . 'lib' . DS . 'gantry' . DS . 'gantry.php';

        if (file_exists($libPath)) return true;
        else return false;

    }

    function _isGantryTemplate()
    {

        $cid = JRequest::getVar('cid');
        if (is_array($cid)) {

            $libPath = $this->basePath . DS . 'templates' . DS . $cid[0] . DS . 'lib' . DS . 'gantry' . DS . 'gantry.php';

            if (file_exists($libPath)) return true;

        }
        return false;
    }


    function _getCurrentPageURL()
    {
        $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
        $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
        $port = ($port) ? ':' . $_SERVER["SERVER_PORT"] : '';
        $url = ($isHTTPS ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"] . $port . $_SERVER["REQUEST_URI"];
        return $url;
    }


}