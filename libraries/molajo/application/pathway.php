<?php
/**
 * @package     Molajo
 * @subpackage  Pathway
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Class to maintain a pathway.
 *
 * The user's navigated path within the application.
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoPathway extends JObject
{
	/**
	 * @var    array  Array to hold the pathway item objects
	 * @since  1.0
	 */
	protected $_pathway = null;

	/**
	 * @var    integer  Integer number of items in the pathway
	 * @since  1.0
	 */
	protected $_count = 0;

	/**
	 * Class constructor
	 * @since  1.0
	 */
	function __construct($options = array())
	{
		$this->_pathway = array();
	}

	/**
	 * Returns a MolajoPathway object
	 *
	 * @param   string  $application  The name of the client
	 * @param   array   $options An associative array of options
	 *
	 * @return  MolajoPathway  A MolajoPathway object.
	 * @since   1.0
	 */
	public static function getInstance($application, $options = array())
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances[$application]))
		{
			//Load the router object
			$info = MolajoApplicationHelper::getApplicationInfo($application, true);

			$path = $info->path.'/includes/pathway.php';
            
			if (file_exists($path))
			{
				require_once $path;

				$classname = 'MolajoPathway'.ucfirst($application);
				$instance = new $classname($options);
			}
			else
			{
				$error = JError::raiseError(500, JText::sprintf('MOLAJO_APPLICATION_ERROR_PATHWAY_LOAD', $application));
				return $error;
			}

			$instances[$application] = & $instance;
		}

		return $instances[$application];
	}

	/**
	 * Return the JPathWay items array
	 *
	 * @return  array  Array of pathway items
	 * @since   1.0
	 */
	public function getPathway()
	{
		$pw = $this->_pathway;

		// Use array_values to reset the array keys numerically
		return array_values($pw);
	}

	/**
	 * Set the MolajoPathway items array.
	 *
	 * @param   array  $pathway	An array of pathway objects.
	 *
	 * @return  array  The previous pathway data.
	 * @since   1.0
	 */
	public function setPathway($pathway)
	{
		$oldPathway	= $this->_pathway;
		$pathway	= (array) $pathway;

		// Set the new pathway.
		$this->_pathway = array_values($pathway);

		return array_values($oldPathway);
	}

	/**
	 * Create and return an array of the pathway names.
	 *
	 * @return  array  Array of names of pathway items
	 * @since   1.0
	 */
	public function getPathwayNames()
	{
		// Initialise variables.
		$names = array (null);

		// Build the names array using just the names of each pathway item
		foreach ($this->_pathway as $item) {
			$names[] = $item->name;
		}

		//Use array_values to reset the array keys numerically
		return array_values($names);
	}

	/**
	 * Create and add an item to the pathway.
	 *
	 * @param   string  $name
	 * @param   string  $link
	 *
	 * @return  boolean  True on success
	 * @since   1.0
	 */
	public function addItem($name, $link='')
	{
		// Initalize variables
		$ret = false;

		if ($this->_pathway[] = $this->_makeItem($name, $link)) {
			$ret = true;
			$this->_count++;
		}

		return $ret;
	}

	/**
	 * Set item name.
	 *
	 * @param   integer  $id
	 * @param   string   $name
	 *
	 * @return  boolean  True on success
	 * @since   1.0
	 */
	public function setItemName($id, $name)
	{
		// Initalize variables
		$ret = false;

		if (isset($this->_pathway[$id])) {
			$this->_pathway[$id]->name = $name;
			$ret = true;
		}

		return $ret;
	}

	/**
	 * Create and return a new pathway object.
	 *
	 * @param   string   $name  Name of the item
	 * @param   string   $link  Link to the item
	 *
	 * @return  MolajoPathway  Pathway item object
	 * @since   1.0
	 */
	protected function _makeItem($name, $link)
	{
		$item = new stdClass();
		$item->name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');
		$item->link = $link;

		return $item;
	}
}
