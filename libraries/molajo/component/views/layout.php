<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Layout
 *
 * @package     Molajo
 * @subpackage  Layout
 * @since       1.0
 */
class MolajoLayout extends JView
{
    /**
     * @var object $app
     * @since 1.0
     */
    protected $app;

    /**
     * @var object $system
     * @since 1.0
     */
    protected $system;

    /**
     * @var object $document
     * @since 1.0
     */
    protected $document;

    /**
     * @var object $user
     * @since 1.0
     */
    protected $user;

    /**
     * @var object $request
     *
     * Populated:
     *
     * Components (Page) -  MolajoApplication::getRequest ()
     * Modules
     * Plugins
     * Formfields
     * Wraps
     *
     * @since 1.0
     */
    protected $request;

    /**
     * @var object $state
     * @since 1.0
     */
    protected $state;

    /**
     * @var object $params
     * @since 1.0
     */
    protected $params;

    /**
     * @var object $rowset
     * @since 1.0
     */
    protected $rowset;

    /**
     * @var array $row
     * @since 1.0
     */
    protected $row;

    /**
     * @var array $pagination
     * @since 1.0
     */
    protected $pagination;

    /**
     * @var array $layout_path
     * @since 1.0
     */
    protected $layout_path;

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
     * @return bool|string
     */
    protected function findPath ($layout, $layout_type)
    {
        /** @var $template */
        $template = MOLAJO_PATH_THEMES.'/'.MolajoFactory::getApplication()->getTemplate().'/html';

        /** 1. @var $templateExtensionPath [template]/html/[extension-name]/[viewname(if component)]/[layout-folder] */
        $templateExtensionPath = '';
        if ($layout_type == 'extension') {
            if ($this->request['extension_type'] == 'plugin') {
                $templateExtensionPath = $template.'/'.$this->request['plugin_folder'].'/'.$this->request['option'];
    
            } else if ($this->request['extension_type'] == 'module') {
                $templateExtensionPath = $template.'/'.$this->request['option'];
    
            } else if ($this->request['extension_type'] == 'component') {
                $templateExtensionPath = $template.'/'.$this->request['option'].'/'.$this->request['view'];
            }
        }
        
        /** 2. @var $templateLayoutPath [template]/html/[layout-folder] */
        $templateLayoutPath = $template.'/'.$layout_type.'s';

        /** 3. @var $extensionPath [extension_type]/[extension-name]/[views-viewname(if component)]/tmpl/[layout-folder] */
        $extensionPath = '';
        if ($layout_type == 'extension') {
            if ($this->request['extension_type'] == 'plugins') {
                $extensionPath = MOLAJO_PATH_ROOT.'/plugins/'.$this->request['plugin_folder'].'/'.$this->request['option'].'/tmpl/';
    
            } else if ($this->request['extension_type'] == 'modules') {
                $extensionPath = MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION_PATH.'/modules/'.$this->request['option'].'/tmpl/';
    
            } else {
                $extensionPath = MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION_PATH.'/components/'.$this->request['option'].'/views/'.$this->request['view'].'/tmpl/';
            }
        }

        /** 4. $corePath layouts/[layout_type]/[layout-folder] */
        if ($layout_type == 'extension') {
            $corePath = MOLAJO_LAYOUTS_EXTENSIONS;    
        } else if ($layout_type == 'form') {
            $corePath = MOLAJO_LAYOUTS_FORMS;    
        } else if ($layout_type == 'head') {
            $corePath = MOLAJO_LAYOUTS_EXTENSIONS;    
        } else if ($layout_type == 'wrap') {
            $corePath = MOLAJO_LAYOUTS_WRAPS;
        } else {
            return false;
        }

        /** template extension override **/
        if (is_dir($templateExtensionPath.'/'.$layout)) {
            $this->layout_path = $templateExtensionPath.'/'.$layout;
            return;

        /** template layout override **/
        } else if (is_dir($templateLayoutPath.'/'.$layout)) {
            $this->layout_path = $templateLayoutPath.'/'.$layout;
            return;

        /** extension layout **/
        } else if (is_dir($extensionPath.'/'.$layout)) {
            $this->layout_path = $extensionPath.'/'.$layout;
            return;

        /** molajao library **/
        } else if (is_dir($corePath.'/'.$layout)) {
            $this->layout_path = $corePath.'/'.$layout;
            return;
        }

        $this->layout_path = false;
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
    * @return string
    *
    */
    protected function renderLayout ($layout)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->loadMedia ();

        /** Language */
        $this->loadLanguage ();

        /** start collecting output */
        ob_start();

        /**
        *  I. Rowset processed by Layout
        *
        *  If the custom.php file exists in layoutFolder, layout handles $this->rowset processing
        *
        */
        if (file_exists($this->layout_path.'/layouts/custom.php')) {
            include $this->layout_path.'/layouts/custom.php';

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

                    if (file_exists($this->layout_path.'/layouts/top.php')) {
                        include $this->layout_path.'/layouts/top.php';
                    }
                }

                /** item: header */
                if (file_exists($this->layout_path.'/layouts/header.php')) {
                    include $this->layout_path.'/layouts/header.php';

                    /** event: After Display of Title */
                    if (isset($this->row->event->afterDisplayTitle)) {
                        echo $this->row->event->afterDisplayTitle;
                    }
                }

                /** item: body */
                if (file_exists($this->layout_path.'/layouts/body.php')) {
                    include $this->layout_path.'/layouts/body.php';
                }

                /** item: footer */
                if (file_exists($this->layout_path.'/layouts/footer.php')) {
                    include $this->layout_path.'/layouts/footer.php';
                }

                $rowCount++;
            }

            /** layout: bottom */
            if (file_exists($this->layout_path.'/layouts/bottom.php')) {
                include $this->layout_path.'/layouts/bottom.php';

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
     * @param $this->layout_path
     * @return void
     */
    protected function loadLanguage ()
    {
        $language = MolajoFactory::getLanguage();
        $language->load('layouts', MOLAJO_LAYOUTS, $language->getDefault(), false, false);
        $language->load('layouts_'.$this->request['layout_type'].'s'.$this->request['layout'], $this->layout_path, $language->getDefault(), false, false);
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
     * @param $this->layout_path
     *
     * @return void
     */
    protected function loadMedia ()
    {
        if (MOLAJO_APPLICATION_PATH == '') {
            $applicationName = 'frontend';
        } else {
            $applicationName = MOLAJO_APPLICATION_PATH;
        }

        /** Application-specific CSS and JS in => media/site/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_PATH_ROOT.'/media/site/'.$applicationName;
        $urlPath = JURI::root().'media/site/'.$applicationName;
        if (isset($this->params->load_application_css)
            && $this->params->get('load_application_css', true) === true) {
            $this->loadMediaCSS ($filePath, $urlPath);
        }
        if (isset($this->params->load_application_css)
            && $this->params->get('load_application_css', true) === true) {
            $this->loadMediaJS ($filePath, $urlPath);
        }

        /** Component specific CSS and JS in => media/site/[application]/[com_component]/css[js]/XYZ.css[js] */
        if (isset($this->params->load_component_css)
            && $this->params->get('load_component_css', true) === true) {
            $this->loadMediaCSS ($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }
        if (isset($this->params->load_component_js)
            && $this->params->get('load_component_js', true) === true) {
            $this->loadMediaJS ($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }

        /** Asset ID specific CSS and JS in => media/site/[application]/[asset_id]/css[js]/XYZ.css[js] */
        if (isset($this->params->load_application_css)
            && $this->params->get('load_application_css', true) === true) {
//            $this->loadMediaCSS ($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        }
        if (isset($this->params->load_application_css)
            && $this->params->get('load_application_css', true) === true) {
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