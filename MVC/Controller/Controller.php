<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Controller
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class Controller
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Stores various extension-specific key/value pairs
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $parameters = array();

	/**
	 * User object, custom fields and parameters
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $user = array();

	/**
	 * Registry containing Table Configuration from XML
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $table_registry_name;

	/**
	 * Valid DB Options
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $db_options = array(
		'JDatabase',
		'Text'
	);

	/**
	 * Default DB
	 *
	 * @var string
	 */
	protected $default_dataSource = 'JDatabase';

	/**
	 * Valid Query Object values
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_objects = array(
		'load',
		'loadResult',
		'loadResultArray',
		'loadRow',
		'loadAssoc',
		'loadObject',
		'loadRowList',
		'loadAssocList',
		'loadObjectList',
		'getAssets',
		'getMessages',
		'none'
	);

	/**
	 * $query_results
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_results;

	/**
	 * $row
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $row;

	/**
	 * $pagination
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $pagination;

	/**
	 * __construct
	 *
	 * Constructor.
	 *
	 * @param  array  $action_request
	 * @param  array  $parameters
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		$this->parameters = array();
	}

	/**
	 * display
	 *
	 * Display action is used to render view output
	 *
	 * @return  string  Rendered output
	 * @since   1.0
	 */
	public function add()
	{
		return $this->display();
	}

	public function edit()
	{
		return $this->display();
	}

	public function display()
	{
	}

	/**
	 * checkinItem
	 *
	 * Method to check in an item after processing
	 *
	 * @return bool
	 */
	public function checkinItem()
	{
		if (Services::Registry()->get('Parameters', 'id') == 0) {
			return true;
		}

		if (property_exists($this->model, 'checked_out')) {
		} else {
			return true;
		}

		$results = $this->model->checkin(Services::Registry()->get('Parameters', 'id'));

		if ($results === false) {
			// redirect
		}

		return true;
	}

	/**
	 * verifyCheckout
	 *
	 * Checks that the current user is the checked_out user for item
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function verifyCheckout()
	{
		if (Services::Registry()->get('Parameters', 'id') == 0) {
			return true;
		}

		if (property_exists($this->model, 'checked_out')) {
		} else {
			return true;
		}
// or super admin
		if ($this->model->checked_out == Services::Registry()->get('User', 'id')) {

		} else {
			// redirect error
			return false;
		}

		return true;
	}

	/**
	 * checkoutItem
	 *
	 * method to set the checkout_time and checked_out values of the item
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	public function checkoutItem()
	{
		if (Services::Registry()->get('Parameters', 'id') == 0) {
			return true;
		}

		if (property_exists($this->model, 'checked_out')) {
		} else {
			return true;
		}

		$results = $this->model->checkout(Services::Registry()->get('Parameters', 'id'));
		if ($results === false) {
			// redirect error
			return false;
		}
		return true;
	}

	/**
	 * createVersion
	 *
	 * Automatic version management save and restore processes for components
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function createVersion()
	{
		if (Services::Registry()->get('Parameters', 'version_management', 1) == 1) {
		} else {
			return true;
		}

		/** create **/
		if ((int)Services::Registry()->get('Parameters', 'id') == 0) {
			return true;
		}

		/** versions deleted with delete **/
		if (Services::Registry()->get('Parameters', 'action') == 'delete'
			&& Services::Registry()->get('Parameters', 'retain_versions_after_delete', 1) == 0
		) {
			return true;
		}

		/** create version **/
		$versionKey = $this->model->createVersion(Services::Registry()->get('Parameters', 'id'));

		/** error processing **/
		if ($versionKey === false) {
			// redirect error
			return false;
		}

		/** Trigger_Event: onContentCreateVersion
		 **/
		return true;
	}

	/**
	 * maintainVersionCount
	 *
	 * Prune version history, if necessary
	 *
	 * @return boolean
	 */
	public function maintainVersionCount()
	{
		if (Services::Registry()->get('Parameters', 'version_management', 1) == 1) {
		} else {
			return true;
		}

		/** no versions to delete for create **/
		if ((int)Services::Registry()->get('Parameters', 'id') == 0) {
			return true;
		}

		/** versions deleted with delete **/
		if (Services::Registry()->get('Parameters', 'action') == 'delete'
			&& Services::Registry()->get('Parameters', 'retain_versions_after_delete', 1) == 0
		) {
			$maintainVersions = 0;
		} else {
			/** retrieve versions desired **/
			$maintainVersions = Services::Registry()->get('Parameters', 'maintain_version_count', 5);
		}

		/** delete extra versions **/
		$results = $this->model->maintainVersionCount(Services::Registry()->get('Parameters', 'id'), $maintainVersions);

		/** version delete failed **/
		if ($results === false) {
			// redirect false
			return false;
		}

		/** Trigger_Event: onContentMaintainVersions
		 **/
		return true;
	}

	/**
	 * cleanCache
	 *
	 * @return    void
	 */
	public function cleanCache()
	{
//        $cache = Molajo::getCache(Services::Registry()->get('Include', 'extension_title'));
//        $cache->clean();
	}
}
