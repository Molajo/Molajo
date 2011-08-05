<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Layout Helper
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoLayoutHelper
{
    /**
     * @var array $_layouts  
     *
	 * @since  1.0
     */
	protected static $_layouts = array();
    
	/**
	 * getLayout
     * 
     * Get layout information.
	 *
     * @static
     * @param string $layout
     * @param string $type
     * @param bool $strict
     * @return stdClass
     *
     * @since 1.0
     */
	public function getLayout($layout, $type, $strict = false)
	{
		if (isset(self::$_layouts[$layout])) {
            $result = self::$_layouts[$layout];
        } else {
			if (self::_load($layout)){
				$result = self::$_layouts[$layout];
			} else {
				$result				= new stdClass;
				$result->enabled	= $strict ? false : true;
				$result->params		= new JRegistry;
			}
		}

		return $result;
	}

	/**
	 * isEnabled
     *
     * Checks if the layout is enabled
	 *
	 * @param   string   $layout  The layout option.
	 * @param   boolean  $string  If set and the layout does not exist, false will be returned
	 *
	 * @return  boolean
	 * @since  1.0
	 */
	public static function isEnabled($layout, $strict = false)
	{
		$result = self::getLayout($layout, $strict);

		return $result->enabled;
	}

	/**
	 * getParams
     *
     * Gets the parameter object for the layout
	 *
	 * @param   string   $layout  The option for the layout.
	 * @param   boolean  $strict  If set and the layout does not exist, false will be returned
	 *
	 * @return  JRegistry  A JRegistry object.
	 *
	 * @see     JRegistry
	 * @since  1.0
	 */
	public static function getParams($layout, $strict = false)
	{
		$layout = self::getLayout($layout, $strict);

		return $layout->params;
	}

	/**
     * _load
     *
	 * Load installed layouts into the _layouts array.
	 *
	 * @param   string  $layout  The element value for the extension
	 *
	 * @return  bool  True on success
	 * @since  1.0
	 */
	protected static function _load($layout, $type)
	{
		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->namequote('extension_id').' as "id"');
		$query->select($db->namequote('element').' as "option"');
		$query->select($db->namequote('params'));
		$query->select($db->namequote('enabled'));
		$query->select($db->namequote('access'));
		$query->select($db->namequote('asset_id'));
		$query->from($db->namequote('#__extensions'));
		$query->where($db->namequote('type').' = '.$db->quote('layout'));
		$query->where($db->namequote('element').' = '.$db->quote($layout));
		$query->where($db->namequote('folder').' = '.$db->quote($type));
		$query->where($db->namequote('enabled').' = 1');
		$query->where('application_id = '.MOLAJO_APPLICATION_ID);

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>''));

		$db->setQuery($query->__toString());

        if (JFactory::getConfig()->get('caching') > 0) {
            $cache = MolajoFactory::getCache('_system','callback');
		    self::$_layouts[$layout] = $cache->get(array($db, 'loadObject'), null, $layout, false);
        } else {
            self::$_layouts[$layout] = $db->loadObject();
        }

		if ($error = $db->getErrorMsg()
            || empty(self::$_layouts[$layout])) {
			JError::raiseWarning(500, JText::sprintf('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $layout, $error));
			return false;
		}

		if (is_string(self::$_layouts[$layout]->params)) {
			$temp = new JRegistry;
			$temp->loadString(self::$_layouts[$layout]->params);
			self::$_layouts[$layout]->params = $temp;
		}

		return true;
	}

    /**
     * findPath
     *
     * Looks for path of Request Layout as a layout folder, in this order:
     *
     *  1. [template]/html/[extension-name]/[viewname(if component)]/[layout-folder]
     *  2. [template]/html/[layout-folder]
     *  3. [extension_type]/[extension-name]/[views-viewname(if component)]/tmpl/[layout-folder]
     *  4. layouts/[$layout_type]/[layout-folder]
     *
     * @param string $layout
     * @param string $layout_type
     * @param string $extension_type
     * @param string $extension_name
     * @param string $view
     * @param string $folder
     * @return bool|string
     */
    protected function findPath ($layout='default', $layout_type='extensions', $extension_name='',
                                        $extension_type='component', $view='display', $folder='')
    {
        /** @var $template */
        $template = MOLAJO_PATH_THEMES.'/'.MolajoFactory::getApplication()->getTemplate().'/html';

        /** 1. @var $templateExtensionPath [template]/html/[extension-name]/[viewname(if component)]/[layout-folder] */
        if ($extension_type == 'plugins') {
            $templateExtensionPath = $template.'/'.$folder.'/'.$extension_name;

        } else if ($extension_type == 'modules') {
            $templateExtensionPath = $template.'/'.$extension_name;

        } else if ($extension_type == 'components') {
            $templateExtensionPath = $template.'/'.$extension_name.'/'.$view;
        }

        /** 2. @var $templateLayoutPath [template]/html/[layout-folder] */
        $templateLayoutPath = $template.'/html/'.$layout_type;

        /** 3. @var $extensionPath [extension_type]/[extension-name]/[views-viewname(if component)]/tmpl/[layout-folder] */
        if ($extension_type == 'plugins') {
            $extensionPath = MOLAJO_PATH_ROOT.'/plugins/'.$folder.'/'.$extension_name.'/tmpl/';

        } else if ($extension_type == 'modules') {
               $extensionPath = MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION_PATH.'/modules/'.$extension_name.'/tmpl/';

        } else {
               $extensionPath = MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION_PATH.'/components/'.$extension_name.'/views/'.$view.'/tmpl/';
        }

        /** 4. layouts/[$layout_type]/[layout-folder] */
        $corePath = MOLAJO_LAYOUTS_EXTENSIONS.'/'.$layout_type;

        /** template extension override **/
        if (is_dir($templateExtensionPath.'/'.$layout)) {
            return $templateExtensionPath.'/'.$layout;

        /** template layout override **/
        } else if (is_dir($templateExtensionPath.'/'.$layout)) {
            return $templateExtensionPath.'/'.$layout;

        /** extension layout **/
        } else if (is_dir($extensionPath.'/'.$layout)) {
            return $extensionPath.'/'.$layout;

        /** molajao library **/
        } else if (is_dir($corePath.'/'.$layout)) {
            return $corePath.'/'.$layout;
        }

        return false;
    }

    /**
    * renderLayout
    *
    * Can do one of two things:
    *
    * 1. Provide the entire set of query results in the $this->rowset object for the layout to process
    *      How? Include a layout file named custom.php (and no layout file and body.php)
    *
    * 2. Loop thru the $this->rowset object processing each row, one at a time.
    *      How? Include top.php, header.php, body.php, footer.php, and/or bottom.php
    *
    * Loops through rowset, one row at a time, including top, header, body, footer, and bottom files
    *
    * @param $layout
    * @param $layoutFolder
    * @return string
    *
    */
    protected function renderLayout ($layoutFolder, $layout)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->loadMedia ($layoutFolder);

        /** Language */
        $this->loadLanguage ($layoutFolder);

        /** start collecting output */
        ob_start();

        /**
        *  I. Rowset processed by Layout
        *
        *  If the custom.php file exists in layoutFolder, layout handles $this->rowset processing
        *
        */
        if (file_exists($layoutFolder.'/layouts/custom.php')) {
            include $layoutFolder.'/layouts/custom.php';

        } else {

        /**
        * II. Loop through each row, one at a time
        *
        * The following layoutFolder/layouts/ files are included, if existing
        *
        * 1. Before any rows and if there is a top.php file:
        *
        *       - beforeDisplayContent output is rendered;
        *
        *       - the top.php file is included.
        *
        * 2. For each row:
        *
        *      if there is a header.php file, it is included,
        *        and the event afterDisplayTitle output is rendered.
        *
        *      If there is a body.php file, it is included;
        *
        *      If there is a footer.php file, it is included;
        *
        * 3. After all rows and if there is a footer.php file:
        *      the footer.php file is included;
        *      afterDisplayContent output is rendered;
        *
        */
            foreach ($this->rowset as $this->row) {

                /** layout: top */
                if ($rowCount == 1 && (!$layout == 'system')) {

                    /** event: Before Content Display */
                    if (isset($this->row->event->beforeDisplayContent)) {
                        echo $this->row->event->beforeDisplayContent;
                    }

                    if (file_exists($layoutFolder.'/layouts/top.php')) {
                        include $layoutFolder.'/layouts/top.php';
                    }
                }

                /** item: header */
                if (file_exists($layoutFolder.'/layouts/header.php')) {
                    include $layoutFolder.'/layouts/header.php';

                    /** event: After Display of Title */
                    if (isset($this->row->event->afterDisplayTitle)) {
                        echo $this->row->event->afterDisplayTitle;
                    }
                }

                /** item: body */
                if (file_exists($layoutFolder.'/layouts/body.php')) {
                    include $layoutFolder.'/layouts/body.php';
                }

                /** item: footer */
                if (file_exists($layoutFolder.'/layouts/footer.php')) {
                    include $layoutFolder.'/layouts/footer.php';
                }

                $rowCount++;
            }

            /** layout: bottom */
            if (file_exists($layoutFolder.'/layouts/bottom.php')) {
                include $layoutFolder.'/layouts/bottom.php';

                /** event: After Layout is finished */
                if (isset($this->row->event->afterDisplayContent)) {
                    echo $this->row->event->afterDisplayContent;
                }
            }
        }

        /** collect output */
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * loadLanguage
     *
     * Language
     *
     * Automatically includes the following files (if existing)
     *
     * 1. Master Layout folder Language Files found in => layout/[current-language]/
     * 2. Current Layout folder Language Files found in => layout/current-layout/[current-language]/
     *
     * @param $layoutFolder
     * @return void
     */
    protected function loadLanguage ($layoutFolder)
    {
        $language = MolajoFactory::getLanguage();

        $language->load('layouts', MOLAJO_LAYOUTS_EXTENSIONS, $language->getDefault(), true, true);
        $language->load('layouts_'.$this->request['layout'], $layoutFolder, $language->getDefault(), true, true);
    }

    /**
     * loadMedia
     *
     * Automatically includes the following files (if existing)
     *
     * 1. Application-specific CSS and JS in => media/site/[application]/css[js]/XYZ.css[js]
     * 2. Component specific CSS and JS in => media/site/[application]/[com_component]/css[js]/XYZ.css[js]
     * 3. Asset ID specific CSS and JS in => media/site/[application]/[asset_id]/css[js]/XYZ.css[js]
     *
     * Note: Right-to-left css files should begin with rtl_
     *
     * @param $layoutFolder
     *
     * @return void
     */
    protected function loadMedia ($layoutFolder)
    {
        if (MOLAJO_APPLICATION_PATH == '') {
            $applicationName = 'frontend';
        } else {
            $applicationName = MOLAJO_APPLICATION_PATH;
        }

        /** Application-specific CSS and JS in => media/site/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_PATH_ROOT.'/media/site/'.$applicationName;
        $urlPath = JURI::root().'media/site/'.$applicationName;

        if ($this->params->get('load_application_css', true) === true) {
            $this->loadMediaCSS ($filePath, $urlPath);
        }
        if ($this->params->get('load_application_js', true) === true) {
            $this->loadMediaJS ($filePath, $urlPath);
        }

        /** Component specific CSS and JS in => media/site/[application]/[com_component]/css[js]/XYZ.css[js] */
        if ($this->params->get('load_component_css', true) === true) {
            $this->loadMediaCSS ($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }
        if ($this->params->get('load_component_js', true) === true) {
            $this->loadMediaJS ($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }

        /** Asset ID specific CSS and JS in => media/site/[application]/[asset_id]/css[js]/XYZ.css[js] */
        if ($this->params->get('load_asset_id_css', true) === true) {
//            $this->loadMediaCSS ($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        }
        if ($this->params->get('load_asset_id_js', true) === true) {
//            $this->loadMediaJS ($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        }

    }

    /**
     * loadMediaCSS
     *
     * Loads the CS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    protected function loadMediaCSS ($filePath, $urlPath)
    {
        if (JFolder::exists($filePath)) {
        } else {
            return;
        }

        $files = JFolder::files($filePath.'/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
                    if ($this->document->direction == 'rtl') {
                         $this->document->addStyleSheet($urlPath.'/css/'.$file);
                    }
                } else {
                    $this->document->addStyleSheet($urlPath.'/css/'.$file);
                }
            }
        }
    }

    /**
     * loadMediaJS
     *
     * Loads the JS located within the folder, as specified by the filepath
     *
     * @param $filePath
     * @param $urlPath
     * @return void
     */
    protected function loadMediaJS ($filePath, $urlPath)
    {
        if (JFolder::exists($filePath)) {
        } else {
            return;
        }

        $files = JFolder::files($filePath.'/js', '\.js$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->document->addScript($urlPath.'/js/'.$file);
            }
        }
    }
}