<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for javascript behaviors
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class MolajoHtmlBehavior
{
    /**
     * Method to load the MooTools framework into the document head
     *
     * If debugging mode is on an uncompressed version of MooTools is included for easier debugging.
     *
     * @param   string   $extras  MooTools file to load
     * @param   boolean  $debug   Is debugging mode on? [optional]
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function framework($extras = false, $debug = null)
    {
        static $loaded = array();

        $type = $extras ? 'more' : 'core';

        // Only load once
        if (!empty($loaded[$type])) {
            return;
        }

        MolajoHTML::core($debug);

        // If no debugging value is set, use the configuration setting
        if ($debug === null) {
            $config = MolajoFactory::getConfig();
            $debug = $config->get('debug');
        }

        $uncompressed = $debug ? '-uncompressed' : '';

        if ($type != 'core' && empty($loaded['core'])) {
            self::framework(false, $debug);
        }

        MolajoHTML::_('script', 'system/mootools-'.$type.$uncompressed.'.js', false, true, false, false);
        $loaded[$type] = true;

        return;
    }

    /**
     * Deprecated. Use MolajoHTMLBehavior::framework() instead.
     *
     * @param   boolean  $debug  Is debugging mode on? [optional]
     *
     * @return  void
     *
     * @since   11.1
     *
     * @deprecated    12.1
     */
    public static function mootools($debug = null)
    {
        // Deprecation warning.
        JLog::add('JBehavior::mootools is deprecated.', JLog::WARNING, 'deprecated');

        self::framework(true, $debug);
    }

    /**
     * Add unobtrusive javascript support for image captions.
     *
     * @param   string  $selector  The selector for which a caption behaviour is to be applied.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function caption($selector = 'img.caption')
    {
        static $caption;

        if (!isset($caption)) {
            $caption = array();
        }

        // Only load once
        if (isset($caption[$selector])) {
            return;
        }

        // Include MooTools framework
        self::framework();

        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('script', 'system/caption'.$uncompressed.'.js', true, true);

        // Attach caption to document
        MolajoFactory::getDocument()->addScriptDeclaration(
            "window.addEvent('load', function() {
				new JCaption('".$selector."');
			});"
        );

        // Set static array
        $tips[$selector] = true;
    }

    /**
     * Add unobtrusive javascript support for form validation.
     *
     * To enable form validation the form tag must have class="form-validate".
     * Each field that needs to be validated needs to have class="validate".
     * Additional handlers can be added to the handler for username, password,
     * numeric and email. To use these add class="validate-email" and so on.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function formvalidation()
    {
        static $loaded = false;

        // Only load once
        if ($loaded) {
            return;
        }

        // Include MooTools framework
        self::framework();

        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('script', 'system/validate'.$uncompressed.'.js', true, true);
        $loaded = true;
    }

    /**
     * Add unobtrusive javascript support for submenu switcher support in
     * Global Configuration and System Information.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function switcher()
    {
        static $loaded = false;

        // Only load once
        if ($loaded) {
            return;
        }

        // Include MooTools framework
        self::framework();

        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('script', 'system/switcher'.$uncompressed.'.js', true, true);

        $script = "
			document.switcher = null;
			window.addEvent('domready', function(){
				toggler = document.id('submenu');
				element = document.id('config-document');
				if (element) {
					document.switcher = new JSwitcher(toggler, element, {cookieName: toggler.getProperty('class')});
				}
			});";

        MolajoFactory::getDocument()->addScriptDeclaration($script);
        $loaded = true;
    }

    /**
     * Add unobtrusive javascript support for a combobox effect.
     *
     * Note that this control is only reliable in absolutely positioned elements.
     * Avoid using a combobox in a slider or dynamic pane.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function combobox()
    {
        static $loaded = false;

        // Only load once
        if ($loaded) {
            return;
        }

        // Include MooTools framework
        self::framework();

        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('script', 'system/combobox'.$uncompressed.'.js', true, true);
        $loaded = true;
    }

    /**
     * Add unobtrusive javascript support for a hover tooltips.
     *
     * Add a title attribute to any element in the form
     * title="title::text"
     *
     *
     * Uses the core Tips class in MooTools.
     *
     * @param   string  $selector  The class selector for the tooltip.
     * @param   array   $parameters    An array of options for the tooltip.
     *                             Options for the tooltip can be:
     *                             - maxTitleChars  integer   The maximum number of characters in the tooltip title (defaults to 50).
     *                             - offsets        object    The distance of your tooltip from the mouse (defaults to {'x': 16, 'y': 16}).
     *                             - showDelay      integr    The millisecond delay the show event is fired (defaults to 100).
     *                             - hideDelay      integer   The millisecond delay the hide hide is fired (defaults to 100).
     *                             - className      string    The className your tooltip container will get.
     *                             - fixed          boolean   If set to true, the toolTip will not follow the mouse.
     *                             - onShow         function  The default function for the show event, passes the tip element
     *                               and the currently hovered element.
     *                             - onHide         function  The default function for the hide event, passes the currently
     *                               hovered element.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function tooltip($selector = '.hasTip', $parameters = array())
    {
        static $tips;

        if (!isset($tips)) {
            $tips = array();
        }

        // Include MooTools framework
        self::framework(true);

        $sig = md5(serialize(array($selector, $parameters)));
        if (isset($tips[$sig]) && ($tips[$sig])) {
            return;
        }

        // Setup options object
        $opt['maxTitleChars'] = (isset($parameters['maxTitleChars']) && ($parameters['maxTitleChars']))
                ? (int)$parameters['maxTitleChars'] : 50;
        // offsets needs an array in the format: array('x'=>20, 'y'=>30)
        $opt['offset'] = (isset($parameters['offset']) && (is_array($parameters['offset']))) ? $parameters['offset'] : null;
        if (!isset($opt['offset'])) {
            // Suppporting offsets parameter which was working in mootools 1.2 (Joomla!1.5)
            $opt['offset'] = (isset($parameters['offsets']) && (is_array($parameters['offsets']))) ? $parameters['offsets'] : null;
        }
        $opt['showDelay'] = (isset($parameters['showDelay'])) ? (int)$parameters['showDelay'] : null;
        $opt['hideDelay'] = (isset($parameters['hideDelay'])) ? (int)$parameters['hideDelay'] : null;
        $opt['className'] = (isset($parameters['className'])) ? $parameters['className'] : null;
        $opt['fixed'] = (isset($parameters['fixed']) && ($parameters['fixed'])) ? true : false;
        $opt['onShow'] = (isset($parameters['onShow'])) ? '\\'.$parameters['onShow'] : null;
        $opt['onHide'] = (isset($parameters['onHide'])) ? '\\'.$parameters['onHide'] : null;

        $options = MolajoHTMLBehavior::_getJSObject($opt);

        // Attach tooltips to document
        MolajoFactory::getDocument()->addScriptDeclaration(
            "window.addEvent('domready', function() {
			$$('$selector').each(function(el) {
				var title = el.get('title');
				if (title) {
					var parts = title.split('::', 2);
					el.store('tip:title', parts[0]);
					el.store('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($$('$selector'), $options);
		});"
        );

        // Set static array
        $tips[$sig] = true;

        return;
    }

    /**
     * Add unobtrusive javascript support for modal links.
     *
     * @param   string  $selector  The selector for which a modal behaviour is to be applied.
     * @param   array   $parameters    An array of parameters for the modal behaviour.
     *                             Options for the modal behaviour can be:
     *                            - ajaxOptions
     *                            - size
     *                            - shadow
     *                            - overlay
     *                            - onOpen
     *                            - onClose
     *                            - onUpdate
     *                            - onResize
     *                            - onShow
     *                            - onHide
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function modal($selector = 'a.modal', $parameters = array())
    {
        static $modals;
        static $included;

        $document = MolajoFactory::getDocument();

        // Load the necessary files if they haven't yet been loaded
        if (!isset($included)) {
            // Include MooTools framework
            self::framework();

            // Load the javascript and css
            $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
            MolajoHTML::_('script', 'system/modal'.$uncompressed.'.js', true, true);
            MolajoHTML::_('stylesheet', 'system/modal.css', array(), true);

            $included = true;
        }

        if (!isset($modals)) {
            $modals = array();
        }

        $sig = md5(serialize(array($selector, $parameters)));
        if (isset($modals[$sig]) && ($modals[$sig])) {
            return;
        }

        // Setup options object
        $opt['ajaxOptions'] = (isset($parameters['ajaxOptions']) && (is_array($parameters['ajaxOptions'])))
                ? $parameters['ajaxOptions'] : null;
        $opt['handler'] = (isset($parameters['handler'])) ? $parameters['handler'] : null;
        $opt['fullScreen'] = (isset($parameters['fullScreen'])) ? (bool)$parameters['fullScreen'] : null;
        $opt['parseSecure'] = (isset($parameters['parseSecure'])) ? (bool)$parameters['parseSecure'] : null;
        $opt['closable'] = (isset($parameters['closable'])) ? (bool)$parameters['closable'] : null;
        $opt['closeBtn'] = (isset($parameters['closeBtn'])) ? (bool)$parameters['closeBtn'] : null;
        $opt['iframePreload'] = (isset($parameters['iframePreload'])) ? (bool)$parameters['iframePreload'] : null;
        $opt['iframeOptions'] = (isset($parameters['iframeOptions']) && (is_array($parameters['iframeOptions'])))
                ? $parameters['iframeOptions'] : null;
        $opt['size'] = (isset($parameters['size']) && (is_array($parameters['size']))) ? $parameters['size'] : null;
        $opt['shadow'] = (isset($parameters['shadow'])) ? $parameters['shadow'] : null;
        $opt['overlay'] = (isset($parameters['overlay'])) ? $parameters['overlay'] : null;
        $opt['onOpen'] = (isset($parameters['onOpen'])) ? $parameters['onOpen'] : null;
        $opt['onClose'] = (isset($parameters['onClose'])) ? $parameters['onClose'] : null;
        $opt['onUpdate'] = (isset($parameters['onUpdate'])) ? $parameters['onUpdate'] : null;
        $opt['onResize'] = (isset($parameters['onResize'])) ? $parameters['onResize'] : null;
        $opt['onMove'] = (isset($parameters['onMove'])) ? $parameters['onMove'] : null;
        $opt['onShow'] = (isset($parameters['onShow'])) ? $parameters['onShow'] : null;
        $opt['onHide'] = (isset($parameters['onHide'])) ? $parameters['onHide'] : null;

        $options = MolajoHTMLBehavior::_getJSObject($opt);

        // Attach modal behavior to document
        $document
                ->addScriptDeclaration(
            "
		window.addEvent('domready', function() {

			SqueezeBox.initialize(".$options.");
			SqueezeBox.assign($$('".$selector."'), {
				parse: 'rel'
			});
		});"
        );

        // Set static array
        $modals[$sig] = true;

        return;
    }

    /**
     * JavaScript behavior to allow shift select in grids
     *
     * @param   string  $id  The id of the form for which a multiselect behaviour is to be applied.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function multiselect($id = 'adminForm')
    {
        static $multiselect;

        if (!isset($multiselect)) {
            $multiselect = array();
        }

        // Only load once
        if (isset($multiselect[$id])) {
            return;
        }

        // Include MooTools framework
        self::framework();

        MolajoHTML::_('script', 'system/multiselect.js', true, true);

        // Attach multiselect to document
        MolajoFactory::getDocument()->addScriptDeclaration(
            "window.addEvent('domready', function() {
				new Joomla.JMultiSelect('".$id."');
			});"
        );

        // Set static array
        $multiselect[$id] = true;
        return;
    }

    /**
     * Add unobtrusive javascript support for the advanced uploader.
     *
     * @param   string  $id            An index.
     * @param   array   $parameters        An array of options for the uploader.
     * @param   string  $upload_queue  The HTML id of the upload queue element (??).
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function uploader($id = 'file-upload', $parameters = array(), $upload_queue = 'upload-queue')
    {
        // Include MooTools framework
        self::framework();

        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('script', 'system/swf'.$uncompressed.'.js', true, true);
        MolajoHTML::_('script', 'system/progressbar'.$uncompressed.'.js', true, true);
        MolajoHTML::_('script', 'system/uploader'.$uncompressed.'.js', true, true);

        $document = MolajoFactory::getDocument();

        static $uploaders;

        if (!isset($uploaders)) {
            $uploaders = array();

            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILENAME');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_UPLOAD_COMPLETED');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_PROGRESS_OVERALL');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_TITLE');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE_TITLE');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_FILE');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_PROGRESS');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_ERROR');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_DUPLICATE');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMIN');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMAX');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTMAX');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTSIZEMAX');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_HTTPSTATUS');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_SECURITYERROR');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_IOERROR');
            MolajoText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');
        }

        if (isset($uploaders[$id]) && ($uploaders[$id])) {
            return;
        }

        $onFileSuccess = '\\function(file, response) {
			var json = new Hash(JSON.decode(response, true) || {});

			if (json.get(\'status\') == \'1\') {
				file.element.addClass(\'file-success\');
				file.info.set(\'html\', \'<strong>\' + Joomla.MolajoText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED\') + \'</strong>\');
			} else {
				file.element.addClass(\'file-failed\');
				file.info.set(\'html\', \'<strong>\' +
					Joomla.MolajoText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED\',
						\'An Error Occurred\').substitute({ error: json.get(\'error\') }) + \'</strong>\');
			}
		}';

        // Setup options object
        $opt['verbose'] = true;
        $opt['url'] = (isset($parameters['targetURL'])) ? $parameters['targetURL'] : null;
        $opt['path'] = (isset($parameters['swf'])) ? $parameters['swf'] : JURI::root(true).'/media/system/swf/uploader.swf';
        $opt['height'] = (isset($parameters['height'])) && $parameters['height'] ? (int)$parameters['height'] : null;
        $opt['width'] = (isset($parameters['width'])) && $parameters['width'] ? (int)$parameters['width'] : null;
        $opt['multiple'] = (isset($parameters['multiple']) && !($parameters['multiple'])) ? false : true;
        $opt['queued'] = (isset($parameters['queued']) && !($parameters['queued'])) ? (int)$parameters['queued'] : null;
        $opt['target'] = (isset($parameters['target'])) ? $parameters['target'] : '\\document.id(\'upload-browse\')';
        $opt['instantStart'] = (isset($parameters['instantStart']) && ($parameters['instantStart'])) ? true : false;
        $opt['allowDuplicates'] = (isset($parameters['allowDuplicates']) && !($parameters['allowDuplicates'])) ? false : true;
        // limitSize is the old parameter name.  Remove in 1.7
        $opt['fileSizeMax'] = (isset($parameters['limitSize']) && ($parameters['limitSize'])) ? (int)$parameters['limitSize']
                : null;
        // fileSizeMax is the new name.  If supplied, it will override the old value specified for limitSize
        $opt['fileSizeMax'] = (isset($parameters['fileSizeMax']) && ($parameters['fileSizeMax'])) ? (int)$parameters['fileSizeMax']
                : $opt['fileSizeMax'];
        $opt['fileSizeMin'] = (isset($parameters['fileSizeMin']) && ($parameters['fileSizeMin'])) ? (int)$parameters['fileSizeMin']
                : null;
        // limitFiles is the old parameter name.  Remove in 1.7
        $opt['fileListMax'] = (isset($parameters['limitFiles']) && ($parameters['limitFiles'])) ? (int)$parameters['limitFiles']
                : null;
        // fileListMax is the new name.  If supplied, it will override the old value specified for limitFiles
        $opt['fileListMax'] = (isset($parameters['fileListMax']) && ($parameters['fileListMax'])) ? (int)$parameters['fileListMax']
                : $opt['fileListMax'];
        $opt['fileListSizeMax'] = (isset($parameters['fileListSizeMax']) && ($parameters['fileListSizeMax']))
                ? (int)$parameters['fileListSizeMax'] : null;
        // types is the old parameter name.  Remove in 1.7
        $opt['typeFilter'] = (isset($parameters['types'])) ? '\\'.$parameters['types']
                : '\\{Joomla.MolajoText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES\'): \'*.*\'}';
        $opt['typeFilter'] = (isset($parameters['typeFilter'])) ? '\\'.$parameters['typeFilter'] : $opt['typeFilter'];

        // Optional functions
        $opt['createReplacement'] = (isset($parameters['createReplacement'])) ? '\\'.$parameters['createReplacement'] : null;
        $opt['onFileComplete'] = (isset($parameters['onFileComplete'])) ? '\\'.$parameters['onFileComplete'] : null;
        $opt['onBeforeStart'] = (isset($parameters['onBeforeStart'])) ? '\\'.$parameters['onBeforeStart'] : null;
        $opt['onStart'] = (isset($parameters['onStart'])) ? '\\'.$parameters['onStart'] : null;
        $opt['onComplete'] = (isset($parameters['onComplete'])) ? '\\'.$parameters['onComplete'] : null;
        $opt['onFileSuccess'] = (isset($parameters['onFileSuccess'])) ? '\\'.$parameters['onFileSuccess'] : $onFileSuccess;

        if (!isset($parameters['startButton'])) {
            $parameters['startButton'] = 'upload-start';
        }

        if (!isset($parameters['clearButton'])) {
            $parameters['clearButton'] = 'upload-clear';
        }

        $opt['onLoad'] = '\\function() {
				document.id(\''.$id
                        .'\').removeClass(\'hide\'); // we show the actual UI
				document.id(\'upload-noflash\').destroy(); // ... and hide the plain form

				// We relay the interactions with the overlayed flash to the link
				this.target.addEvents({
					click: function() {
						return false;
					},
					mouseenter: function() {
						this.addClass(\'hover\');
					},
					mouseleave: function() {
						this.removeClass(\'hover\');
						this.blur();
					},
					mousedown: function() {
						this.focus();
					}
				});

				// Interactions for the 2 other buttons

				document.id(\''.$parameters['clearButton']
                        .'\').addEvent(\'click\', function() {
					Uploader.remove(); // remove all files
					return false;
				});

				document.id(\''.$parameters['startButton']
                        .'\').addEvent(\'click\', function() {
					Uploader.start(); // start upload
					return false;
				});
			}';

        $options = MolajoHTMLBehavior::_getJSObject($opt);

        // Attach tooltips to document
        $uploaderInit = 'window.addEvent(\'domready\', function(){
				var Uploader = new FancyUpload2(document.id(\''.$id.'\'), document.id(\''.$upload_queue.'\'), '.$options.' );
				});';
        $document->addScriptDeclaration($uploaderInit);

        // Set static array
        $uploaders[$id] = true;

        return;
    }

    /**
     * Add unobtrusive javascript support for a collapsible tree.
     *
     * @param   string  $id      An index
     * @param   array   $parameters  An array of options.
     * @param   array   $root    The root node
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function tree($id, $parameters = array(), $root = array())
    {
        static $trees;

        if (!isset($trees)) {
            $trees = array();
        }

        // Include MooTools framework
        self::framework();

        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('script', 'system/mootree'.$uncompressed.'.js', true, true, false, false);
        MolajoHTML::_('stylesheet', 'system/mootree.css', array(), true);

        if (isset($trees[$id]) && ($trees[$id])) {
            return;
        }

        // Setup options object
        $opt['div'] = (array_key_exists('div', $parameters)) ? $parameters['div'] : $id.'_tree';
        $opt['mode'] = (array_key_exists('mode', $parameters)) ? $parameters['mode'] : 'folders';
        $opt['grid'] = (array_key_exists('grid', $parameters)) ? '\\'.$parameters['grid'] : true;
        $opt['theme'] = (array_key_exists('theme', $parameters)) ? $parameters['theme']
                : MolajoHTML::_('image', 'system/mootree.gif', '', array(), true, true);

        // Event handlers
        $opt['onExpand'] = (array_key_exists('onExpand', $parameters)) ? '\\'.$parameters['onExpand'] : null;
        $opt['onSelect'] = (array_key_exists('onSelect', $parameters)) ? '\\'.$parameters['onSelect'] : null;
        $opt['onClick'] = (array_key_exists('onClick', $parameters)) ? '\\'.$parameters['onClick']
                : '\\function(node){  window.open(node.data.url, $chk(node.data.target) ? node.data.target : \'_self\'); }';

        $options = MolajoHTMLBehavior::_getJSObject($opt);

        // Setup root node
        $rt['text'] = (array_key_exists('text', $root)) ? $root['text'] : 'Root';
        $rt['id'] = (array_key_exists('id', $root)) ? $root['id'] : null;
        $rt['color'] = (array_key_exists('color', $root)) ? $root['color'] : null;
        $rt['open'] = (array_key_exists('open', $root)) ? '\\'.$root['open'] : true;
        $rt['icon'] = (array_key_exists('icon', $root)) ? $root['icon'] : null;
        $rt['openicon'] = (array_key_exists('openicon', $root)) ? $root['openicon'] : null;
        $rt['data'] = (array_key_exists('data', $root)) ? $root['data'] : null;
        $rootNode = MolajoHTMLBehavior::_getJSObject($rt);

        $treeName = (array_key_exists('treeName', $parameters)) ? $parameters['treeName'] : '';

        $js = '		window.addEvent(\'domready\', function(){
			tree'.$treeName.' = new MooTreeControl('.$options.','.$rootNode.');
			tree'.$treeName.'.adopt(\''.$id.'\');})';

        // Attach tooltips to document
        $document = MolajoFactory::getDocument();
        $document->addScriptDeclaration($js);

        // Set static array
        $trees[$id] = true;

        return;
    }

    /**
     * Add unobtrusive javascript support for a calendar control.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function calendar()
    {
        static $loaded = false;

        // Only load once
        if ($loaded) {
            return;
        }

        $document = MolajoFactory::getDocument();
        $tag = MolajoFactory::getLanguage()->getTag();

        //Add uncompressed versions when debug is enabled
        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('stylesheet', 'system/calendar-jos.css', array(' title' => MolajoText::_('JLIB_HTML_BEHAVIOR_GREEN'), ' media' => 'all'), true);
        MolajoHTML::_('script', $tag.'/calendar'.$uncompressed.'.js', false, true);
        MolajoHTML::_('script', $tag.'/calendar-setup'.$uncompressed.'.js', false, true);

        $translation = MolajoHTMLBehavior::_calendartranslation();
        if ($translation) {
            $document->addScriptDeclaration($translation);
        }
        $loaded = true;
    }

    /**
     * Add unobtrusive javascript support for a color picker.
     *
     * @return  void
     *
     * @since   11.2
     */
    public static function colorpicker()
    {
        static $loaded = false;

        // Only load once
        if ($loaded) {
            return;
        }

        // Include MooTools framework
        self::framework(true);

        //Add uncompressed versions when debug is enabled
        $uncompressed = MolajoFactory::getConfig()->get('debug') ? '-uncompressed' : '';
        MolajoHTML::_('stylesheet', 'system/mooRainbow.css', array('media' => 'all'), true);
        MolajoHTML::_('script', 'system/mooRainbow.js', false, true);

        MolajoFactory::getDocument()
                ->addScriptDeclaration(
            "window.addEvent('domready', function(){
				var nativeColorUi = false;
				if (Browser.opera && (Browser.version >= 11.5)) {
					nativeColorUi = true;
				}
				var elems = $$('.input-colorpicker');
				elems.each(function(item){
					if (nativeColorUi) {
						item.type = 'color';
					} else {
						new MooRainbow(item,
						{
							imgPath: '".JURI::root(true)
           ."/media/system/images/mooRainbow/',
							onComplete: function(color) {
								this.element.value = color.hex;
							},
							startColor: item.value.hexToRgb(true)
						});
					}
				});
			});
		"
        );

        $loaded = true;
    }

    /**
     * Keep session alive, for example, while editing or creating an article.
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function keepalive()
    {
        static $loaded = false;

        // Only load once
        if ($loaded) {
            return;
        }

        // Include MooTools framework
        self::framework();

        $config = MolajoFactory::getConfig();
        $lifetime = ($config->get('lifetime') * 60000);
        $refreshTime = ($lifetime <= 60000) ? 30000 : $lifetime - 60000;
        // Refresh time is 1 minute less than the liftime assined in the configuration.php file.

        // the longest refresh period is one hour to prevent integer overflow.
        if ($refreshTime > 3600000 || $refreshTime <= 0) {
            $refreshTime = 3600000;
        }

        $document = MolajoFactory::getDocument();
        $script = '';
        $script .= 'function keepAlive() {';
        $script .= '	var myAjax = new Request({method: "get", url: "index.php"}).send();';
        $script .= '}';
        $script .= ' window.addEvent("domready", function()';
        $script .= '{ keepAlive.periodical('.$refreshTime.'); }';
        $script .= ');';

        $document->addScriptDeclaration($script);
        $loaded = true;

        return;
    }

    /**
     * Break us out of any containing iframes
     *
     * @param   string  $location  Location to display in
     *
     * @return  void
     *
     * @since   11.1
     */
    public static function noframes($location = 'top.location.href')
    {
        static $loaded = false;

        // Only load once
        if ($loaded) {
            return;
        }

        // Include MooTools framework
        self::framework();

        $js = "window.addEvent('domready', function () {if (top == self) {document.documentElement.style.display = 'block'; }" .
              " else {top.location = self.location; }});";
        $document = MolajoFactory::getDocument();
        $document->addStyleDeclaration('html { display:none }');
        $document->addScriptDeclaration($js);

        JResponse::setHeader('X-Frames-Options', 'SAME-ORIGIN');

        $loaded = true;
    }

    /**
     * Internal method to get a JavaScript object notation string from an array
     *
     * @param   array  $array  The array to convert to JavaScript object notation
     *
     * @return  string  JavaScript object notation representation of the array
     *
     * @since   11.1
     */
    protected static function _getJSObject($array = array())
    {
        // Initialise variables.
        $object = '{';

        // Iterate over array to build objects
        foreach ((array)$array as $k => $v)
        {
            if (is_null($v)) {
                continue;
            }

            if (is_bool($v)) {
                if ($k === 'fullScreen') {
                    $object .= 'size: { ';
                    $object .= 'x: ';
                    $object .= 'window.getSize().x-80';
                    $object .= ',';
                    $object .= 'y: ';
                    $object .= 'window.getSize().y-80';
                    $object .= ' }';
                    $object .= ',';
                }
                else
                {
                    $object .= ' '.$k.': ';
                    $object .= ($v) ? 'true' : 'false';
                    $object .= ',';
                }
            }
            elseif (!is_array($v) && !is_object($v))
            {
                $object .= ' '.$k.': ';
                $object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1)
                        : "'".$v."'";
                $object .= ',';
            }
            else
            {
                $object .= ' '.$k.': '.MolajoHTMLBehavior::_getJSObject($v).',';
            }
        }

        if (substr($object, -1) == ',') {
            $object = substr($object, 0, -1);
        }

        $object .= '}';

        return $object;
    }

    /**
     * Internal method to translate the JavaScript Calendar
     *
     * @return  string  JavaScript that translates the object
     *
     * @since   11.1
     */
    protected static function _calendartranslation()
    {
        static $jsscript = 0;

        if ($jsscript == 0) {
            $return = 'Calendar._DN = new Array ("'.MolajoText::_('SUNDAY', true).'", "'.MolajoText::_('MONDAY', true).'", "'
                     .MolajoText::_('TUESDAY', true).'", "'.MolajoText::_('WEDNESDAY', true).'", "'.MolajoText::_('THURSDAY', true).'", "'
                     .MolajoText::_('FRIDAY', true).'", "'.MolajoText::_('SATURDAY', true).'", "'.MolajoText::_('SUNDAY', true).'");'
                     .' Calendar._SDN = new Array ("'.MolajoText::_('SUN', true).'", "'.MolajoText::_('MON', true).'", "'.MolajoText::_('TUE', true).'", "'
                     .MolajoText::_('WED', true).'", "'.MolajoText::_('THU', true).'", "'.MolajoText::_('FRI', true).'", "'.MolajoText::_('SAT', true).'", "'
                     .MolajoText::_('SUN', true).'");'.' Calendar._FD = 0;'.' Calendar._MN = new Array ("'.MolajoText::_('JANUARY', true).'", "'
                     .MolajoText::_('FEBRUARY', true).'", "'.MolajoText::_('MARCH', true).'", "'.MolajoText::_('APRIL', true).'", "'.MolajoText::_('MAY', true)
                     .'", "'.MolajoText::_('JUNE', true).'", "'.MolajoText::_('JULY', true).'", "'.MolajoText::_('AUGUST', true).'", "'
                     .MolajoText::_('SEPTEMBER', true).'", "'.MolajoText::_('OCTOBER', true).'", "'.MolajoText::_('NOVEMBER', true).'", "'
                     .MolajoText::_('DECEMBER', true).'");'.' Calendar._SMN = new Array ("'.MolajoText::_('JANUARY_SHORT', true).'", "'
                     .MolajoText::_('FEBRUARY_SHORT', true).'", "'.MolajoText::_('MARCH_SHORT', true).'", "'.MolajoText::_('APRIL_SHORT', true).'", "'
                     .MolajoText::_('MAY_SHORT', true).'", "'.MolajoText::_('JUNE_SHORT', true).'", "'.MolajoText::_('JULY_SHORT', true).'", "'
                     .MolajoText::_('AUGUST_SHORT', true).'", "'.MolajoText::_('SEPTEMBER_SHORT', true).'", "'.MolajoText::_('OCTOBER_SHORT', true).'", "'
                     .MolajoText::_('NOVEMBER_SHORT', true).'", "'.MolajoText::_('DECEMBER_SHORT', true).'");'
                     .' Calendar._TT = {};Calendar._TT["INFO"] = "'.MolajoText::_('JLIB_HTML_BEHAVIOR_ABOUT_THE_CALENDAR', true).'";'
                     .' Calendar._TT["ABOUT"] =
 "DHTML Date/Time Selector\n" +
 "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"'.MolajoText::_('JLIB_HTML_BEHAVIOR_DATE_SELECTION', true).'" +
"'.MolajoText::_('JLIB_HTML_BEHAVIOR_YEAR_SELECT', true).'" +
"'.MolajoText::_('JLIB_HTML_BEHAVIOR_MONTH_SELECT', true).'" +
"'.MolajoText::_('JLIB_HTML_BEHAVIOR_HOLD_MOUSE', true)
                     .'";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Time selection:\n" +
"- Click on any of the time parts to increase it\n" +
"- or Shift-click to decrease it\n" +
"- or click and drag for faster selection.";

		Calendar._TT["PREV_YEAR"] = "'.MolajoText::_('JLIB_HTML_BEHAVIOR_PREV_YEAR_HOLD_FOR_MENU', true).'";'.' Calendar._TT["PREV_MONTH"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_PREV_MONTH_HOLD_FOR_MENU', true).'";'.' Calendar._TT["GO_TODAY"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_GO_TODAY', true).'";'.' Calendar._TT["NEXT_MONTH"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_NEXT_MONTH_HOLD_FOR_MENU', true).'";'.' Calendar._TT["NEXT_YEAR"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_NEXT_YEAR_HOLD_FOR_MENU', true).'";'.' Calendar._TT["SEL_DATE"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_SELECT_DATE', true).'";'.' Calendar._TT["DRAG_TO_MOVE"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_DRAG_TO_MOVE', true).'";'.' Calendar._TT["PART_TODAY"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_TODAY', true).'";'.' Calendar._TT["DAY_FIRST"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_DISPLAY_S_FIRST', true).'";'.' Calendar._TT["WEEKEND"] = "0,6";'.' Calendar._TT["CLOSE"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_CLOSE', true).'";'.' Calendar._TT["TODAY"] = "'.MolajoText::_('JLIB_HTML_BEHAVIOR_TODAY', true)
                     .'";'.' Calendar._TT["TIME_PART"] = "'.MolajoText::_('JLIB_HTML_BEHAVIOR_SHIFT_CLICK_OR_DRAG_TO_CHANGE_VALUE', true).'";'
                     .' Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";'.' Calendar._TT["TT_DATE_FORMAT"] = "'
                     .MolajoText::_('JLIB_HTML_BEHAVIOR_TT_DATE_FORMAT', true).'";'.' Calendar._TT["WK"] = "'.MolajoText::_('JLIB_HTML_BEHAVIOR_WK', true).'";'
                     .' Calendar._TT["TIME"] = "'.MolajoText::_('JLIB_HTML_BEHAVIOR_TIME', true).'";';
            $jsscript = 1;
            return $return;
        }
        else
        {
            return false;
        }
    }
}
