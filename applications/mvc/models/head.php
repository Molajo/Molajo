<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Head
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoModelHead extends MolajoModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct($config = array())
    {
        $this->_name = get_class($this);
        parent::__construct($config = array());
    }

    /**
     * getItems
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $this->items = array();
        //$document->setTitle($this->getCfg('site_name'). ' - ' .JText::_('JADMINISTRATION'));
/**
        $mdata = $this->item->metadata->toArray();
        foreach ($mdata as $k => $v)
        {
            if ($v)
            {
                $this->document->setMetadata($k, $v);
            }
        }
if ($this->print)
{
	$this->document->setMetaData('robots', 'noindex, nofollow');
}
*/
//        $metadata = MolajoController::getApplication()->getMetaData();
        /** Template-specific CSS and JS in => template/[template-name]/css[js]/XYZ.css[js] */
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->request->get('template_name');
        $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->request->get('template_name');
        MolajoController::getApplication()->addStylesheetLinksFolder($filePath, $urlPath);
        MolajoController::getApplication()->addJavascriptLinksFolder($filePath, $urlPath);

        $tempObject = new JObject();
        $tempObject->set('type', 'base');
        $tempObject->set('title', $this->request->get('metadata_title'));
        $tempObject->set('base', $this->request->get('base'));
        $tempObject->set('last_modified', $this->request->get('source_last_modified'));
        $tempObject->set('description', $this->request->get('metadata_description'));
        $tempObject->set('generator', $this->request->get('generator'));
        $tempObject->set('favicon', $this->request->get('template_favicon'));
        $tempObject->set('keywords', $this->request->get('metadata_keywords'));
        $tempObject->set('author', $this->request->get('metadata_author'));
        $tempObject->set('content_rights', $this->request->get('metadata_content_rights'));
        $tempObject->set('robots', $this->request->get('metadata_robots'));
        $this->items[] = $tempObject;

        /** links */
        $links = MolajoController::getApplication()->getHeadLinks();
        if (count($links) > 0) {
            foreach ($links as $link) {
                $tempObject = new JObject();
                $tempObject->set('type', 'links');
                $tempObject->set('url', $link['url']);
                $tempObject->set('relation', $link['relation']);
                $tempObject->set('relation_type', $link['relation_type']);
                $tempObject->set('attributes', $link['attributes']);
                $this->items[] = $tempObject;
            }
        }

        /** stylesheet_links */
        $stylesheetLinks = MolajoController::getApplication()->getStylesheetLinks();
        if (count($stylesheetLinks) > 0) {
            foreach ($stylesheetLinks as $link) {
                $tempObject = new JObject();
                $tempObject->set('type', 'stylesheet_links');
                $tempObject->set('url', $link['url']);
                $tempObject->set('mimetype', $link['mimetype']);
                $tempObject->set('media', $link['media']);
                $tempObject->set('attributes', $link['attributes']);
                $tempObject->set('priority', $link['priority']);
                $this->items[] = $tempObject;
            }
        }

        $tempObject = new JObject();
        $tempObject->set('type', 'stylesheet_declarations');
        $this->items[] = $tempObject;

        /** javascript_links */
        $javascriptLinks = MolajoController::getApplication()->getJavascriptLinks();
        foreach ($javascriptLinks as $link) {
            $tempObject = new JObject();
            $tempObject->set('type', 'javascript_links');
            $tempObject->set('url', $link['url']);
            $tempObject->set('mimetype', $link['mimetype']);
            $tempObject->set('defer', $link['defer']);
            $tempObject->set('async', $link['async']);
            $tempObject->set('priority', $link['priority']);
            $this->items[] = $tempObject;
        }

        $tempObject = new JObject();
        $tempObject->set('type', 'script');
        $this->items[] = $tempObject;

        return $this->items;
/** custom */
    }

    /**
     * compressCSS
     *
     * @return    array
     *
     * @since    1.0
     */
    public function compressCSS()
    {
    }
}