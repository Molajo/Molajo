<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * DocumentFeed class, provides an easy interface to parse and display any feed document
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */

class MolajoDocumentFeed extends MolajoDocument
{
	/**
	 * Syndication URL feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $syndicationURL = "";

	/**
	 * Image feed element
	 *
	 * optional
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $image = null;

	/**
	 * Copyright feed elememnt
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $copyright = "";

	/**
	 * Published date feed element
	 *
	 *  optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $pubDate = "";

	/**
	 * Lastbuild date feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $lastBuildDate = "";

	/**
	 * Editor feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $editor = "";

	/**
	 * Docs feed element
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $docs = "";

	/**
	 * Editor email feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $editorEmail = "";

	/**
	 * Webmaster email feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $webmaster = "";

	/**
	 * Category feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $category = "";

	/**
	 * TTL feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $ttl = "";

	/**
	 * Rating feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $rating = "";

	/**
	 * Skiphours feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $skipHours = "";

	/**
	 * Skipdays feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $skipDays = "";

	/**
	 * The feed items collection
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $items = array();

	/**
	 * Class constructor
	 *
	 * @param   array  $options Associative array of options
	 *
	 * @since  1.0
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		//set document type
		$this->_type = 'feed';
	}

	/**
	 * Render the document
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  The rendered data
	 *
	 * @since  1.0
	 */
	public function render($cache = false, $params = array())
	{
		global $option;

		// Get the feed type
		$type = JRequest::getCmd('type', 'rss');

		/*
		 * Cache TODO In later release
		 */
		$cache		= 0;
		$cache_time = 3600;
		$cache_path = JPATH_CACHE;

		// set filename for rss feeds
		$file = strtolower(str_replace('.', '', $type));
		$file = $cache_path.'/'.$file.'_'.$option.'.xml';


		// Instantiate feed renderer and set the mime encoding
		$renderer = $this->loadRenderer(($type) ? $type : 'rss');
		if (!is_a($renderer, 'MolajoDocumentRenderer')) {
			JError::raiseError(404, MolajoText::_('JGLOBAL_RESOURCE_NOT_FOUND'));
		}
		$this->setMimeEncoding($renderer->getContentType());

		// Output
		// Generate prolog
		$data	= "<?xml version=\"1.0\" encoding=\"".$this->_charset."\"?>\n";
		$data	.= "<!-- generator=\"".$this->getGenerator()."\" -->\n";

		 // Generate stylesheet links
		foreach ($this->_styleSheets as $src => $attr) {
			$data .= "<?xml-stylesheet href=\"$src\" type=\"".$attr['mime']."\"?>\n";
		}

		// Render the feed
		$data .= $renderer->render();

		parent::render();
		return $data;
	}

	/**
	 * Adds an MolajoFeedItem to the feed.
	 *
	 * @param   object MolajoFeedItem $item The feeditem to add to the feed.
	 *
	 * @since  1.0
	 */
	public function addItem(&$item)
	{
		$item->source = $this->link;
		$this->items[] = $item;
	}
}

/**
 * MolajoFeedItem is an internal class that stores feed item information
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoFeedItem extends JObject
{
	/**
	 * Title item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $title;

	/**
	 * Link item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $link;

	/**
	 * Description item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $description;

	/**
	 * Author item element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $author;

	 /**
	 * Author email element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $authorEmail;

	/**
	 * Category element
	 *
	 * optional
	 *
	 * @var    array or string
	 * @since  1.0
	 */
	 public $category;

	 /**
	 * Comments element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $comments;

	 /**
	 * Enclosure element
	 *
	 * @var    object
	 * @since  1.0
	 */
	 public $enclosure =  null;

	 /**
	 * Guid element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 var $guid;

	/**
	 * Published date
	 *
	 * optional
	 *
	 *  May be in one of the following formats:
	 *
	 *	RFC 822:
	 *	"Mon, 20 Jan 03 18:05:41 +0400"
	 *	"20 Jan 03 18:05:41 +0000"
	 *
	 *	ISO 8601:
	 *	"2003-01-20T18:05:41+04:00"
	 *
	 *	Unix:
	 *	1043082341
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $date;

	 /**
	 * Source element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $source;


	 /**
	 * Set the MolajoFeedEnclosure for this item
	 *
	 * @param   object  $enclosure  The MolajoFeedItem to add to the feed.
	 *
	 * @since  1.0
	 */
	 public function setEnclosure($enclosure)	{
		 $this->enclosure = $enclosure;
	 }
}

/**
 * MolajoFeedEnclosure is an internal class that stores feed enclosure information
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoFeedEnclosure extends JObject
{
	/**
	 * URL enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $url = "";

	/**
	 * Length enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $length = "";

	 /**
	 * Type enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $type = "";
}

/**
 * MolajoFeedImage is an internal class that stores feed image information
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoFeedImage extends JObject
{
	/**
	 * Title image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $title = "";

	 /**
	 * URL image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $url = "";

	/**
	 * Link image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $link = "";

	 /**
	 * Width image attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $width;

	 /**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $height;

	 /**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  1.0
	 */
	 public $description;
}
