<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo View
 *
 * @package        Molajo
 * @subpackage    View
 * @since        1.0
 */
class MolajoView extends JView
{
    /**
     * @var object $app
     * @since 1.0
     */
    public $app;

    /**
     * @var object $document
     * @since 1.0
     */
    public $document;

    /**
     * @var object $user
     * @since 1.0
     */
    public $user;

    /**
     * @var object $request
     * @since 1.0
     */
    public $request;

    /**
     * @var object $state
     * @since 1.0
     */
    public $state;

    /**
     * @var object $parameters
     * @since 1.0
     */
    public $parameters;

    /**
     * @var object $rowset
     * @since 1.0
     */
    public $rowset;

    /**
     * @var object $row
     * @since 1.0
     */
    public $row;

    /**
     * @var object $pagination
     * @since 1.0
     */
    public $pagination;

    /**
     * @var object $layout_path
     * @since 1.0
     */
    public $layout_path;

    /**
     * @var object $layout_type
     * @since 1.0
     */
    public $layout_type;

    /**
     * @var object $layout
     * @since 1.0
     */
    public $layout;
    /**
     * @var object $wrap
     * @since 1.0
     */
    public $wrap;

    /**
     * renderModulePosition
     *
     * usage in layout:
     *
     * $this->renderModulePosition ('position-name', array('wrap' => 'none');
     *
     * @param $position
     * @param array $options
     * @return void
     */
    public function renderModulePosition($position, $options = array('wrap' => 'none'))
    {
        $renderer = $this->document->loadRenderer('modules');
        echo $renderer->render($position, $options, null);
    }

    /**
     * display
     *
     * View for Display View that uses no forms
     *
     * @param null $tpl
     *
     * @return bool
     */
    public function display($tpl = null)
    {
        /** no results */
        if (count($this->parameters) > 0
            && $this->parameters->def('suppress_no_results', false) === true
            && count($this->rowset == 0)
        ) {
            return;
        }

        /** Render Layout */
        $this->findPath($this->layout, $this->layout_type);
        if ($this->layout_path === false) {
            // load an error layout
            return;
        }

        $renderedOutput = $this->renderLayout($this->layout, $this->layout_type);

        /** Wrap Rendered Output */
        if ($this->wrap == 'horz') {
            $this->wrap = 'horizontal';
        }
        if ($this->wrap == 'xhtml') {
            $this->wrap = 'div';
        }
        if ($this->wrap == 'rounded') {
            $this->wrap = 'div';
        }
        if ($this->wrap == 'raw') {
            $this->wrap = 'none';
        }
        if ($this->wrap == '') {
            $this->wrap = 'none';
        }
        if ($this->wrap == null) {
            $this->wrap = 'none';
        }

        $this->findPath($this->wrap, 'wraps');
        if ($this->layout_path === false) {
            echo $renderedOutput;
            return;
        }

        $this->rowset = array();

        $tmpobj = new JObject();
        $tmpobj->set('title', $this->request['wrap_title']);
        $tmpobj->set('subtitle', $this->request['wrap_subtitle']);
        $tmpobj->set('wrap_id', $this->request['wrap_id']);
        $tmpobj->set('wrap_class', $this->request['wrap_class']);
        $tmpobj->set('published_date', $this->request['wrap_date']);
        $tmpobj->set('author', $this->request['wrap_author']);
        $tmpobj->set('position', $this->request['position']);
        $tmpobj->set('content', $renderedOutput);

        $this->rowset[] = $tmpobj;

        $wrappedOutput = $this->renderLayout($this->wrap, 'wraps');

        echo $wrappedOutput;
        return;
    }

    /**
     * findPath
     *
     * Looks for path of Request Layout as a layout folder, in this order:
     *
     *  1. [template]/html/[extension-name]/[viewname(if component)]/[layout-folder]
     *  2. [template]/[layout-type]/[layout-folder]
     *  3. [extension_type]/[extension-name]/[views/viewname(if component)]/tmpl/[layout-folder]
     *  4. layouts/[layout_type]/[layout-folder]
     *
     * @return bool|string
     */
    protected function findPath($layout, $layout_type)
    {
        /** initialize layout */
        $this->layout_path = false;

        /** @var $template */
        $template = MOLAJO_EXTENSION_TEMPLATES.'/'.MolajoFactory::getApplication(MOLAJO_APPLICATION)->getTemplate() . 'html';
        $template = MOLAJO_EXTENSION_TEMPLATES.'/molajito/html';

        /** 1. @var $templateExtensionPath [template]/html/[extension-name]/[viewname(if component)]/[layout-folder] */
        $templateExtensionPath = '';
        if ($layout_type == 'extensions') {
            if ($this->request['extension_type'] == 'plugin') {
                $templateExtensionPath = $template.'/'.$this->request['plugin_folder'].'/'.$this->request['option'];

            } else if ($this->request['extension_type'] == 'module') {
                $templateExtensionPath = $template.'/'.$this->request['option'];

            } else if ($this->request['extension_type'] == 'component') {
                $templateExtensionPath = $template.'/'.$this->request['option'].'/'.$this->request['view'];
            }
        }

        /** 2. @var $templateLayoutPath [template]/[layout-folder] */
        $templateLayoutPath = $template.'/'.$layout_type;

        /** 3. @var $extensionPath [extension_type]/[extension-name]/[views-viewname(if component)]/tmpl/[layout-folder] */
        $extensionPath = '';
        if ($layout_type == 'extensions') {
            if ($this->request['extension_type'] == 'plugins') {
                $extensionPath = MOLAJO_EXTENSION_PLUGINS.'/'.$this->request['plugin_folder'].'/'.$this->request['option'].'/tmpl';

            } else if ($this->request['extension_type'] == 'modules') {
                $extensionPath = MOLAJO_EXTENSION_MODULES.'/'.$this->request['option'].'/tmpl';

            } else {
                $extensionPath = MOLAJO_EXTENSION_COMPONENTS.'/'.$this->request['option'].'/views/' . $this->request['view'].'/tmpl';
            }
        } else {
            $extensionPath = $templateLayoutPath;
        }

        /** 4. $corePath layouts/[layout_type]/[layout-folder] */
        if ($layout_type == 'extensions') {
            $corePath = MOLAJO_EXTENSION_LAYOUTS.'/extensions/';
        } else if ($layout_type == 'formfields') {
            $corePath = MOLAJO_EXTENSION_LAYOUTS.'/formfields/';
        } else if ($layout_type == 'document') {
            $corePath = MOLAJO_EXTENSION_LAYOUTS.'/document/';
        } else if ($layout_type == 'wraps') {
            $corePath = MOLAJO_EXTENSION_LAYOUTS.'/wraps/';
        }

        /**
         * Determine path in order of priority
         */
        /** 1. template extension override **/
        if (is_dir($templateExtensionPath.'/'.$layout)) {
            $this->layout_path = $templateExtensionPath.'/'.$layout;
            return;

            /** 2. template layout override **/
        } else if (is_dir($templateLayoutPath.'/'.$layout)) {
            $this->layout_path = $templateLayoutPath.'/'.$layout;
            return;

            /** 3. extension layout **/
        } else if (is_dir($extensionPath.'/'.$layout)) {
            $this->layout_path = $extensionPath.'/'.$layout;
            return;

            /** 4. molajo library **/
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
    protected function renderLayout($layout, $layout_type)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->loadMedia($layout, $layout_type);

        /** Language */
        $this->loadLanguage($layout, $layout_type);

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
                if ($rowCount == 1) {

                    /** event: Before Content Display */
                    if (isset($this->row->event->beforeDisplayContent)) {
                        echo $this->row->event->beforeDisplayContent;
                    }

                    if (file_exists($this->layout_path.'/layouts/top.php')) {
                        include $this->layout_path.'/layouts/top.php';
                    }
                }

                if ($this->row == null) {
                } else {

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
     * 2. Current Layout folder Language Files found in => layout/[layout-type]/[layout-name]/[current-language]/
     *
     * @param $this->layout_path
     * @return void
     */
    protected function loadLanguage($layout, $layout_type)
    {
        $defaultLanguage = MolajoFactory::getLanguage()->getDefault();
        MolajoFactory::getLanguage()->load('layout', MOLAJO_EXTENSION_LAYOUTS, $defaultLanguage, false, false);
        /** not plural */
        MolajoFactory::getLanguage()->load('layout_' . substr($layout_type, 0, strlen($layout_type) - 1) . '_' . $layout, $this->layout_path, $defaultLanguage, false, false);
        /** head does not have an s at the end */
        MolajoFactory::getLanguage()->load('layout_' . $layout_type . '_' . $layout, $this->layout_path, $defaultLanguage, false, false);
    }

    /**
     * loadMedia
     *
     * Automatically includes the following files (if existing)
     *
     * 1. Application-specific CSS and JS in => media/system/[application]/css[js]/XYZ.css[js]
     * 2. Component specific CSS and JS in => media/system/[application]/[com_component]/css[js]/XYZ.css[js]
     * 3. Asset ID specific CSS and JS in => media/system/[application]/[asset_id]/css[js]/XYZ.css[js]
     * 4. Layout specific CSS and JS in => layouts/[layout-type]/[layout-name]/css[js]/XYZ.css[js]
     *
     * Note: Right-to-left css files should begin with rtl_
     *
     * @param $this->layout_path
     *
     * @return void
     */
    protected function loadMedia($layout, $layout_type)
    {
        if (MOLAJO_APPLICATION_PATH == '') {
            $applicationName = 'frontend';
        } else {
            $applicationName = MOLAJO_APPLICATION_PATH;
        }

        /** Application-specific CSS and JS in => media/system/[application]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_SITE_PATH_MEDIA.'/system/' . $applicationName;
        $urlPath = JURI::root() . 'media/system/' . $applicationName;

        if (isset($this->parameters->load_application_css)
            && $this->parameters->get('load_application_css', true) === true
        ) {
            $this->loadMediaCSS($filePath, $urlPath);
        }
        if (isset($this->parameters->load_application_css)
            && $this->parameters->get('load_application_css', true) === true
        ) {
            $this->loadMediaJS($filePath, $urlPath);
        }

        /** Component specific CSS and JS in => media/system/[application]/[com_component]/css[js]/XYZ.css[js] */
        if (isset($this->parameters->load_component_css)
            && $this->parameters->get('load_component_css', true) === true
        ) {
            $this->loadMediaCSS($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }
        if (isset($this->parameters->load_component_js)
            && $this->parameters->get('load_component_js', true) === true
        ) {
            $this->loadMediaJS($filePath.'/'.$this->request['option'], $urlPath.'/'.$this->request['option']);
        }

        /** Asset ID specific CSS and JS in => media/system/[application]/[asset_id]/css[js]/XYZ.css[js] */
        if (isset($this->parameters->load_asset_css)
            && $this->parameters->get('load_asset_css', true) === true
        ) {
            $this->loadMediaCSS($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        }
        if (isset($this->parameters->load_asset_js)
            && $this->parameters->get('load_asset_js', true) === true
        ) {
            $this->loadMediaJS($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        }

        /** Layout specific CSS and JS in => layouts/[layout_type]/[asset_id]/css[js]/XYZ.css[js] */

        $filePath = $this->layout_path;

        $urlPath = JURI::root() . 'extensions/layouts/' . $layout_type.'/'.$layout;

        //        if (isset($this->parameters->load_layout_css)
        //            && $this->parameters->get('load_layout_css', true) === true) {
        $this->loadMediaCSS($filePath, $urlPath);
        //        }
        //        if (isset($this->parameters->load_layout_js)
        //            && $this->parameters->get('load_layout_js', true) === true) {
        //            $this->loadMediaJS ($filePath.'/'.$this->request['asset_id'], $urlPath.'/'.$this->request['asset_id']);
        $this->loadMediaJS($filePath, $urlPath);
        //        }
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
    protected function loadMediaCSS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath.'/css')) {
        } else {
            return;
        }

        $files = JFolder::files($filePath.'/css', '\.css$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (substr($file, 0, 4) == 'rtl_') {
                    if ($this->document->direction == 'rtl') {
                        $this->document->addStyleSheet($urlPath.'/css/' . $file);
                    }
                } else {
                    $this->document->addStyleSheet($urlPath.'/css/' . $file);
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
    protected function loadMediaJS($filePath, $urlPath)
    {
        if (JFolder::exists($filePath.'/js')) {
        } else {
            return;
        }
        //todo: differentiate between script and scripts
        $files = JFolder::files($filePath.'/js', '\.js$', false, false);

        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->document->addScript($urlPath.'/js/' . $file);
            }
        }
    }
}

/** 7. Optional data (put this into a model parent?) */
//		$this->category	            = $this->get('Category');
//		$this->categoryAncestors    = $this->get('Ancestors');
//		$this->categoryParent       = $this->get('Parent');
//		$this->categoryPeers	    = $this->get('Peers');
//		$this->categoryChildren	    = $this->get('Children');

/** used in manager */

/**
 * @var $render object
 */
//protected $render;

/**
 * @var $saveOrder string
 */
// protected $saveOrder;
//      $this->authorProfile        = $this->get('Author');

//      $this->tags (tag cloud)
//      $this->tagCategories (menu)
//      $this->calendar

/** blog variables
move variables into $options
retrieve variables here in view - and split int rowset if needed

protected $category;
protected $children;
protected $lead_items = array();
protected $intro_items = array();
protected $link_items = array();
protected $columns = 1;
 */
//Navigation
//$this->navigation->get('form_return_to_link')
//$this->navigation->get('previous')
//$this->navigation->get('next')
//
// Pagination
//$this->navigation->get('pagination_start')
//$this->navigation->get('pagination_limit')
//$this->navigation->get('pagination_links')
//$this->navigation->get('pagination_ordering')
//$this->navigation->get('pagination_direction')
//$this->breadcrumbs
//$total = $this->getTotal();

//$this->configuration
//Parameters (Includes Global Options, Menu Item, Item)
//$this->parameters->get('layout_show_page_heading', 1)
//$this->parameters->get('layout_page_class_suffix', '')