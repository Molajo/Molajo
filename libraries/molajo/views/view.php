<?php
/**
 * @version     $id: view.html.php
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo View 
 *
 * @package	Molajo
 * @subpackage	View
 * @since	1.0
 */
class MolajoView extends JView
{
    /**
     * @var $app object
     */
        protected $app;
    
    /**
     * @var $system object
     */
        protected $system;

    /**
     * @var $document object
     */
        protected $document;

    /**
     * @var $user object
     */
        protected $user;

    /**
     * @var $state object
     */
        protected $state;

    /**
     * @var $params object
     */
        protected $params;

    /**
     * @var $rowset object
     */
        protected $rowset;

    /**
     * @var $row array
     */
        protected $row;

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
        /** @var $this->app */
        $this->app = MolajoFactory::getApplication();
        
        /** @var $this->system */
        $this->system = MolajoFactory::getConfig();

        /** @var $this->document */
        $this->document = MolajoFactory::getDocument();

        /** @var $this->user */
        $this->user = MolajoFactory::getUser();

        /** Set Page Meta */
//		$pageModel = JModel::getInstance('Page', 'MolajoModel', array('ignore_request' => true));
//		$pageModel->setState('params', $this->app->getParams());
    }

    /**
     * findPath
     * 
     * Looks for path of Request Layout as a layout folder, in this order:
     *
     *  1. CurrentTemplate/html/$layout-folder/
     *  2. components/com_component/views/$view/tmpl/$layout-folder/
     *  3. MOLAJO_LAYOUTS/$layout-folder/
     *
     * @param  $tpl
     * @return bool|string
     */
    protected function findPath ($layout)
    {
        /** path: template **/
        $template = MolajoFactory::getApplication()->getTemplate();
        $templatePath = MOLAJO_PATH_THEMES.'/'.$template.'/html/';

        /** path: component **/
        if (MOLAJO_APPLICATION == 'site') {
            $componentPath = MOLAJO_PATH_ROOT.'/components/'.$this->state->get('request.option').'/views/'.$this->state->get('request.view').'/tmpl/';
        } else {
            $componentPath = MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION.'/components/'.$this->state->get('request.option').'/views/'.$this->state->get('request.view').'/tmpl/';
        }

        /** path: core **/
        $corePath = MOLAJO_LAYOUTS.'/';

        /** template **/
        if (is_dir($templatePath.$layout)) {
            return $templatePath.$layout;

        /** component **/
        } else if (is_dir($componentPath.$layout)) {
            return $componentPath.$layout;

        /** molajao library **/
        } else if (is_dir($corePath.$layout)) {
            return $corePath.$layout;
        }

        return false;
    }

    /**
    * renderMolajoLayout
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
    protected function renderMolajoLayout ($layoutFolder, $layout)
    {
        /** @var $rowCount */
        $rowCount = 1;

        /** Media */
        $this->renderMolajoLayoutHeadMedia ($layoutFolder);

        /** Language */
        $this->renderMolajoLayoutLanguage ($layoutFolder);

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
     * renderMolajoLayoutHead
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
    protected function renderMolajoLayoutLanguage ($layoutFolder)
    {
        $language = MolajoFactory::getLanguage();
        $language->load('layouts', MOLAJO_LAYOUTS, $language->getDefault(), true, true);
        $language->load('layouts_'.$this->state->get('request.layout'), $layoutFolder, $language->getDefault(), true, true);
    }

    /**
     * renderMolajoLayoutHeadMedia
     *
     * Automatically includes the following files (if existing)
     *
     * 1. Standard site-wide CSS and JS in => media/site/css[js]/site.css[js]
     * 2. Component specific CSS and JS in => media/site/css[js]/component_option.css[js]
     * 3. Any CSS file in the CSS sub-folder => css/filenames.css
     * 4. Any JS file in the JS sub-folder => js/filenames.js
     *
     * Note: Right-to-left css files should begin with rtl_
     *
     * @param $layoutFolder
     *
     * @return void
     */
    protected function renderMolajoLayoutHeadMedia ($layoutFolder)
    {
        if ($this->state->get('layout.loadSiteCSS', true) === true) {
            /** standard site-wide css and js - media/site/css[js]/viewname.css[js] **/
            if (JFile::exists(MOLAJO_PATH_BASE.'/media/site/css/site.css')) {
                $this->document->addStyleSheet(JURI::base().'/site/css/site.css');
            }
            if ($this->document->direction == 'rtl') {
                if (JFile::exists(MOLAJO_PATH_BASE.'/media/site/css/site_rtl.css')) {
                    $this->document->addStyleSheet(JURI::base().'/media/site/css/site_rtl.css');
                }
            }
        }

        if ($this->state->get('layout.loadSiteJS', true) === true) {
            if (JFile::exists(MOLAJO_PATH_BASE.'/media/site/js/site.js')) {
                $this->document->addScript(JURI::base().'/media/site/js/site.js');
            }
        }

        /** component specific css and js - media/site/css[js]/component_option.css[js] **/
        if ($this->state->get('layout.loadComponentCSS', true) === true) {
            if (JFile::exists(MOLAJO_PATH_BASE.'/media/site/css/'.$this->state->get('request.option').'.css')) {
                $this->document->addStyleSheet(JURI::base().'/media/site/css/'.$this->state->get('request.option').'.css');
            }
        }

        if ($this->state->get('layout.loadComponentJS', true) === true) {
            if (JFile::exists(MOLAJO_PATH_BASE.'/media/site/js/'.$this->state->get('request.option').'.js')) {
                $this->document->addScript(JURI::base().'media/site/js/'.$this->state->get('request.option').'.js');
            }
        }

        /** Load Layout CSS (if exists in layout CSS folder) */
        if ($this->state->get('layout.loadLayoutCSS', true) === true) {
            $files = JFolder::files($layoutFolder.'/css', '\.css', false, false);
            foreach ($files as $file) {
                if (substr(strtolower($file), 0, 4) == 'rtl_' && $this->document->direction == 'rtl') {
                    $this->document->addStyleSheet($layoutFolder.'/css/'.$file);
                } else {
                    $this->document->addStyleSheet($layoutFolder.'/css/'.$file);
                }
            }
        }

        /** Load Layout JS (if exists in layout JS folder) */
        if ($this->state->get('layout.loadLayoutJS', true) === true) {
            $files = JFolder::files($layoutFolder.'/js', '\.js', false, false);
            foreach ($files as $file) {
                if (substr(strtolower($file), 0, 4) == 'rtl_' && $this->document->direction == 'rtl') {
                    $this->document->addStyleSheet($layoutFolder.'/js/'.$file);
                } else {
                    $this->document->addStyleSheet($layoutFolder.'/js/'.$file);
                }
            }
        }
    }

    /**
     * getColumns
     *
     * Displays system variable names and values
     *
     * $this->params
     *
     * $this->getColumns ('system');
     *
     * @param  $type
     * @return void
     */
    protected function getColumns ($type, $layout='system')
    {
        /** @var $this->rowset */
        $this->rowset = array();

        /** @var $registry */
        $registry = new JRegistry();

        /** @var $tempIndex */
        $columnIndex = 0;

        if ($type == 'user') {
            foreach ($this->$type as $column=>$value) {
                if ($column == 'params') {
                    $registry->loadJSON($value);
                    $options = $registry->toArray();
                    $this->getColumnsJSONArray ($type, $options);
                } else {
                    $this->getColumnsFormatting ($type, $column, $value);
                }
            }

        } else if ($type == 'system') {
                $registry->loadJSON($this->$type);
                $options = $registry->toArray();
                $this->getColumnsJSONArray ($type, $options);

        } else {
            return false;
        }

        /**
         *  Display Results
         */
        $layoutFolder = $this->findPath($layout);
        echo $this->renderMolajoLayout ($layoutFolder, 'system');

        return;

    }

    /**
     * getColumnsJSONArray
     *
     * Process Array from converted JSON Object
     *
     * @param  $type
     * @param  $options
     * @return void
     */
    private function getColumnsJSONArray ($type, $options)
    {
        foreach ($options as $column=>$value) {
            $this->getColumnsFormatting ($type, $column, $value);
        }
    }

    /**
     * getColumnsFormatting
     *
     * Process Columns from Object
     *
     * @param  $type
     * @param  $column
     * @param  $value
     * @return void
     */
    private function getColumnsFormatting ($type, $column, $value, $columnIndex)
    {
        $this->rowset[$columnIndex]['column'] = $column;

        if (is_array($value)) {
            $this->rowset[$columnIndex]['syntax'] = '$list = $this->'.$type."->get('".$column."');<br />";
            $this->rowset[$columnIndex]['syntax'] .= 'foreach ($list as $item=>$itemValue) { <br />';
            $this->rowset[$columnIndex]['syntax'] .= '&nbsp;&nbsp;&nbsp;&nbsp;echo $item.'."': '".'.$itemValue;';
            $this->rowset[$columnIndex]['syntax'] .= '<br />'.'}';
            $temp = '';
            $list = $this->$type->get($column);
            foreach ($list as $item=>$itemValue) {
                $temp .= $item.': '.$itemValue.'<br />';
            }
            $this->rowset[$columnIndex]['value'] = $temp;
        } else {
            $this->rowset[$columnIndex]['syntax'] = 'echo $this->'.$type."->get('".$column."');  ";
            $this->rowset[$columnIndex]['value'] = $value;
        }

        $columnIndex++;
    }
}