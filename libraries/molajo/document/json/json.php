<?php
/**
 * @package    Molajo
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * MolajoDocumentJSON class, provides an easy interface to parse and display JSON output
 *
 * @package    Molajo
 * @subpackage  Document
 * @see         http://www.json.org/
 * @since       1.0
 */

jimport('joomla.document.document');

class MolajoDocumentJSON extends MolajoDocument
{
	/**
	 * Document name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $_name = 'joomla';

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @return  MolajoDocumentJson
	 *
	 * @since  1.0
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		//set mime type
		$this->_mime = 'application/json';

		//set document type
		$this->_type = 'json';
	}

	/**
	 * Render the document.
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
		JResponse::allowCache(false);
		JResponse::setHeader('Content-disposition', 'attachment; filename="'.$this->getName().'.json"', true);

		parent::render();

		return $this->getBuffer();
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 *
	 * @since  1.0
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 *
	 * @return  void
	 *
	 * @since  1.0
	 */
	public function setName($name = 'joomla') {
		$this->_name = $name;
	}
}
