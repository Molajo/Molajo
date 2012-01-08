<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Head Renderer Class
 *
 * Renders the HTML Head
 *
 * @package     Molajo
 * @subpackage  Head
 * @since       11.1
 */
class MolajoHeadRenderer
{
    /**
     * Array of Header <link> tags
     *
     * @var    array
     */
    public $_links = array();

    /**
     * Array of custom tags
     *
     * @var    array
     */
    public $_custom = array();

    /**
     * @var null
     */
    public $template = null;
    public $baseurl = null;
    public $parameters = null;
    public $_file = null;

    /**
     * String holding parsed template
     */
    protected $_template = '';

    /**
     * Array of parsed template doc tags
     */
    protected $_template_tags = array();

    /**
     * Integer with caching setting
     */
    protected $_caching = null;

    /**
     * __construct
     *
     * Class constructor
     *
     * @param   array  $options Associative array of options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

    }

    /**
     * getHeadData
     *
     * Get the HTML document head data
     *
     * @return  array  The document head data in array form
     */
    public function getHeadData()
    {
        $data = array();
        $data['title'] = $this->title;
        $data['description'] = $this->description;
        $data['link'] = $this->link;
        $data['metaTags'] = $this->_metaTags;
        $data['links'] = $this->_links;
        $data['styleSheets'] = $this->_styleSheets;
        $data['style'] = $this->_style;
        $data['scripts'] = $this->_scripts;
        $data['script'] = $this->_script;
        $data['custom'] = $this->_custom;
        return $data;
    }

    /**
     * setHeadData
     *
     * Set the HTML document head data
     *
     * @param   array  $data    The document head data in array form
     */
    public function setHeadData($data)
    {
        if (empty($data) || !is_array($data)) {
            return;
        }

        $this->title = (isset($data['title']) && !empty($data['title'])) ? $data['title'] : $this->title;
        $this->description = (isset($data['description']) && !empty($data['description'])) ? $data['description']
                : $this->description;
        $this->link = (isset($data['link']) && !empty($data['link'])) ? $data['link'] : $this->link;
        $this->_metaTags = (isset($data['metaTags']) && !empty($data['metaTags'])) ? $data['metaTags']
                : $this->_metaTags;
        $this->_links = (isset($data['links']) && !empty($data['links'])) ? $data['links'] : $this->_links;
        $this->_styleSheets = (isset($data['styleSheets']) && !empty($data['styleSheets'])) ? $data['styleSheets']
                : $this->_styleSheets;
        $this->_style = (isset($data['style']) && !empty($data['style'])) ? $data['style'] : $this->_style;
        $this->_scripts = (isset($data['scripts']) && !empty($data['scripts'])) ? $data['scripts'] : $this->_scripts;
        $this->_script = (isset($data['script']) && !empty($data['script'])) ? $data['script'] : $this->_script;
        $this->_custom = (isset($data['custom']) && !empty($data['custom'])) ? $data['custom'] : $this->_custom;
    }

    /**
     * mergeHeadData
     *
     * Merge the HTML document head data
     *
     * @param   array  $data    The document head data in array form
     */
    public function mergeHeadData($data)
    {
        if (empty($data) || !is_array($data)) {
            return;
        }

        $this->title = (isset($data['title']) && !empty($data['title']) && !stristr($this->title, $data['title']))
                ? $this->title . $data['title'] : $this->title;
        $this->description = (isset($data['description']) && !empty($data['description']) && !stristr($this->description, $data['description']))
                ? $this->description . $data['description'] : $this->description;
        $this->link = (isset($data['link'])) ? $data['link'] : $this->link;

        if (isset($data['metaTags'])) {
            foreach ($data['metaTags'] AS $type1 => $data1)
            {
                $booldog = $type1 == 'http-equiv' ? true : false;
                foreach ($data1 AS $name2 => $data2)
                {
                    $this->setMetaData($name2, $data2, $booldog);
                }
            }
        }

        $this->_links = (isset($data['links']) && !empty($data['links']) && is_array($data['links']))
                ? array_unique(array_merge($this->_links, $data['links'])) : $this->_links;
        $this->_styleSheets = (isset($data['styleSheets']) && !empty($data['styleSheets']) && is_array($data['styleSheets']))
                ? array_merge($this->_styleSheets, $data['styleSheets']) : $this->_styleSheets;

        if (isset($data['style'])) {
            foreach ($data['style'] AS $type => $stdata)
            {
                if (!isset($this->_style[strtolower($type)]) || !stristr($this->_style[strtolower($type)], $stdata)) {
                    $this->addStyleDeclaration($stdata, $type);
                }
            }
        }

        $this->_scripts = (isset($data['scripts']) && !empty($data['scripts']) && is_array($data['scripts']))
                ? array_merge($this->_scripts, $data['scripts']) : $this->_scripts;


        if (isset($data['script'])) {
            foreach ($data['script'] AS $type => $sdata)
            {
                if (!isset($this->_script[strtolower($type)]) || !stristr($this->_script[strtolower($type)], $sdata)) {
                    $this->addScriptDeclaration($sdata, $type);
                }
            }
        }

        $this->_custom = (isset($data['custom']) && !empty($data['custom']) && is_array($data['custom']))
                ? array_unique(array_merge($this->_custom, $data['custom'])) : $this->_custom;
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