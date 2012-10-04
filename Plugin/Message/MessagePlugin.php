<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Message;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Application;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MessagePlugin extends Plugin
{
	/**
	 * Prepares data for System Message
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterParsebody()
	{

		$message = Services::Message()->get();
		if (count($message) == 0 || $message === false) {
			return true;
		}

		$query_results = array();
		foreach ($message as $message) {

			$row = new \stdClass();

			$row->message = $message['message'];
			$row->type = $message['type'];
			$row->code = $message['code'];
			$row->action = Application::Request()->get('base_url_path_for_application') .
				Application::Request()->get('requested_resource_for_route');

			$row->class = 'alert-box';
			if ($message['type'] == MESSAGE_TYPE_SUCCESS) {
				$row->heading = Services::Language()->translate('Success');
				$row->class .= ' success';

			} elseif ($message['type'] == MESSAGE_TYPE_WARNING) {
				$row->heading = Services::Language()->translate('Warning');
				$row->class .= ' warning';

			} elseif ($message['type'] == MESSAGE_TYPE_ERROR) {
				$row->heading = Services::Language()->translate('Error');
				$row->class .= ' alert';

			} else {
				$row->heading = Services::Language()->translate('Information');
				$row->class .= ' secondary';
			}
			$query_results[] = $row;
		}

		Services::Registry()->set('Plugindata', 'alertmessage', $query_results);

		return true;
	}
}
