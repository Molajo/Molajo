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
        //$document->setTitle($this->getCfg('sitename'). ' - ' .JText::_('JADMINISTRATION'));
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
        $filePath = MOLAJO_EXTENSIONS_TEMPLATES . '/' . $this->requestArray['template_name'];
        $urlPath = MOLAJO_EXTENSIONS_TEMPLATES_URL . '/' . $this->requestArray['template_name'];
        MolajoController::getApplication()->loadMediaCSS($filePath, $urlPath);
        MolajoController::getApplication()->loadMediaJS($filePath, $urlPath);

        $tempObject = new JObject();
        $tempObject->set('type', 'base');
        $tempObject->set('title', $this->requestArray['metadata_title']);
        $tempObject->set('base', $this->requestArray['base']);
        $tempObject->set('last_modified', $this->requestArray['source_last_modified']);
        $tempObject->set('description', $this->requestArray['metadata_description']);
        $tempObject->set('generator', $this->requestArray['generator']);
        $tempObject->set('favicon', $this->requestArray['template_favicon']);
        $tempObject->set('keywords', $this->requestArray['metadata_keywords']);
        $tempObject->set('author', $this->requestArray['metadata_author']);
        $tempObject->set('content_rights', $this->requestArray['metadata_rights']);
        $tempObject->set('robots', $this->requestArray['metadata_robots']);

        $this->items[] = $tempObject;

        $tempObject = new JObject();
        $tempObject->set('type', 'metadata');
        $this->items[] = $tempObject;

        $tempObject = new JObject();
        $tempObject->set('type', 'stylesheets');
        $this->items[] = $tempObject;

        $tempObject = new JObject();
        $tempObject->set('type', 'styles');
        $this->items[] = $tempObject;

        $tempObject = new JObject();
        $tempObject->set('type', 'scripts');
        $this->items[] = $tempObject;

        $tempObject = new JObject();
        $tempObject->set('type', 'script');
        $this->items[] = $tempObject;

        $tempObject = new JObject();
        $tempObject->set('type', 'links');
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

    /**
     * Adds <link> tags to the head of the document
     *
     * $relType defaults to 'rel' as it is the most common relation type used.
     * ('rev' refers to reverse relation, 'rel' indicates normal, forward relation.)
     * Typical tag: <link href="index.php" rel="Start">
     *
     * @param   string  $href        The link that is being related.
     * @param   string  $relation    Relation of link.
     * @param   string  $relType    Relation type attribute.  Either rel or rev (default: 'rel').
     * @param   array   $attributes Associative array of remaining attributes.
     *
     * @return  void
     */
    public function addHeadLink($href, $relation, $relType = 'rel', $attribs = array())
    {
        $attribs = JArrayHelper::toString($attribs);
        $generatedTag = '<link href="' . $href . '" ' . $relType . '="' . $relation . '" ' . $attribs;
        $this->_links[] = $generatedTag;
    }

    /**
     * Adds a shortcut icon (favicon)
     *
     * This adds a link to the icon shown in the favorites list or on
     * the left of the url in the address bar. Some browsers display
     * it on the tab, as well.
     *
     * @param   string  $href        The link that is being related.
     * @param   string  $type        File type
     * @param   string  $relation    Relation of link
     */
    public function addFavicon($href, $type = 'image/vnd.microsoft.icon', $relation = 'shortcut icon')
    {
        $href = str_replace('\\', '/', $href);
        $this->_links[] = '<link href="' . $href . '" rel="' . $relation . '" type="' . $type . '"';
    }

    /**
     * Adds a custom HTML string to the head block
     *
     * @param   string  $html  The HTML to add to the head
     * @return  void
     */

    public function addCustomTag($html)
    {
        $this->_custom[] = trim($html);
    }
}