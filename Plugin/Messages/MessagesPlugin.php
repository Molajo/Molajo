<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Messages;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;
use Molajo\Application;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MessagesPlugin extends ContentPlugin
{
	/**
	 * Prepares data for System Messages
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (strtolower($this->get('template_view_path_node')) == 'messages') {
		} else {
			return true;
		}

		/** Message Type */
		$type = $this->getField('type');
		if ($type == false) {
			$type_value = MESSAGE_TYPE_INFORMATION;
		} else {
			$type_value = $this->getFieldValue($type);
		}
		$this->saveField(null, 'type', $type_value);

		/** Message */
		$message = $this->getField('message');
		if ($message == false) {
			$message_value = '';
		} else {
			$message_value = $this->getFieldValue($message);
		}
		$this->saveField(null, 'message', $message_value);

		/** Action */
		$action = Application::Request()->get('base_url_path_for_application') .
			Application::Request()->get('requested_resource_for_route');

		$this->saveField(null, 'action', $action);

		$class = 'alert-box';
		if ($type_value == MESSAGE_TYPE_SUCCESS) {
			$heading = Services::Language()->translate('Success');
			$class .= ' success';

		} elseif ($type_value == MESSAGE_TYPE_WARNING) {
			$heading = Services::Language()->translate('Warning');
			$class .= ' warning';

		} elseif ($type_value == MESSAGE_TYPE_ERROR) {
			$heading = Services::Language()->translate('Error');
			$class .= ' alert';

		} else {
			$heading = Services::Language()->translate('Information');
			$class .= ' secondary';
		}

		$this->saveField(null, 'heading', $heading);
		$this->saveField(null, 'class', $class);

		return true;
	}
}
