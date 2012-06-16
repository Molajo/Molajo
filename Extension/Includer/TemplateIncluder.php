<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Extension\Helpers;
use Molajo\Service\Services;
use Molajo\Extension\Includer;

defined('MOLAJO') or die;

/**
 * Template
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class TemplateIncluder extends Includer
{
	/**
	 * @param string $name
	 * @param string $type
	 *
	 * @return null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null)
	{
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', 0);
		parent::__construct($name, $type);
		Services::Registry()->set('Parameters', 'criteria_html_display_filter', false);

		return $this;
	}

	/**
	 * setRenderCriteria - Retrieve default values, if not provided by extension
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{
		/**  Extension name set to the name of the template in the getAttributes method */
		$template_title = Services::Registry()->get('Parameters', 'extension_title');

		$template_id = Helpers::Extension()
			->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, $template_title);

		if ((int)$template_id == 0) {
		} else {
			Services::Registry()->set('Parameters', 'template_view_id', $template_id);
		}

		/** Standard parameters (overwrite extension title with Template */
		Services::Registry()->set('Parameters', 'extension_title', 'Template');

		/** Template  */
		$results = Helpers::View()->get(Services::Registry()->get('Parameters', 'template_view_id'), 'Template');
		if ($results == false) {
			echo $template_title . 'Template was not found. Will be ignored. <br />';
			return false;
		}

		/** Wrap  */
		$wrap_id = (int)Services::Registry()->get('Parameters', 'wrap_view_id');
		if ((int)$wrap_id == 0) {
			$wrap_title = Services::Registry()->get('Parameters', 'wrap_view_path_node', '');
			if ($wrap_title == '') {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					Helpers::Extension()
						->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, 'None'));
			}
		}
		Helpers::View()->get(Services::Registry()->get('Parameters', 'wrap_view_id'), 'Wrap');

		/** Merge Configuration in */
		Services::Registry()->merge('Configuration', 'Parameters', true);

		/* Set other model parameters: model_parameter is set in Attributes */
		$model_name = Services::Registry()->get('Parameters', 'model_name', '');
		$model_parameter = Services::Registry()->get('Parameters', 'model_parameter', '');

		if (strtolower($this->type) == 'asset') {
			Services::Registry()->set('Parameters', 'model_name', 'Assets');
			Services::Registry()->set('Parameters', 'model_type', 'dbo');
			Services::Registry()->set('Parameters', 'model_query_object', 'getAssets');

		} elseif (strtolower($this->type) == 'metadata') {
			Services::Registry()->set('Parameters', 'model_name', 'Metadata');
			Services::Registry()->set('Parameters', 'model_type', 'dbo');
			Services::Registry()->set('Parameters', 'model_query_object', 'getMetadata');

		} elseif (($model_name == '' || strtolower($model_name) == 'dummy')
					&& ($model_parameter == '')) {
			Services::Registry()->set('Parameters', 'model_name', 'Dummy');
			Services::Registry()->set('Parameters', 'model_type', 'dbo');
			Services::Registry()->set('Parameters', 'model_query_object', 'dummy');

		} elseif (strtolower($model_name) == 'parameters') {
			Services::Registry()->set('Parameters', 'model_name', 'Parameters');
			Services::Registry()->set('Parameters', 'model_type', 'dbo');
			Services::Registry()->set('Parameters', 'model_query_object', 'getParameters');
			Services::Registry()->set('Parameters', 'model_parameter', '');

		} else {
			Services::Registry()->set('Parameters', 'model_name', 'Triggerdata');
			Services::Registry()->set('Parameters', 'model_type', 'dbo');
			Services::Registry()->set('Parameters', 'model_query_object', 'getTriggerdata');
		}

		/** Cleanup */
		Services::Registry()->delete('Parameters', 'item*');
		Services::Registry()->delete('Parameters', 'list*');
		Services::Registry()->delete('Parameters', 'form*');

		/** Sort */
		Services::Registry()->sort('Parameters');

/**
echo 'Final test view: '. Services::Registry()->get('Parameters', 'template_view_title').'<br />';
echo 'View ' . Services::Registry()->get('Parameters', 'template_view_title') . '<br />';
echo 'Model '.$model_name .'<br />';
Services::Registry()->get('Parameters', 'model*');
 */

		/** Was a View selected? */
		if (Services::Registry()->get('Parameters', 'template_view_title', '') == '') {
			return false;
		}
		return true;
	}

	/**
	 * Loads Language Files for extension
	 *
	 * @return null
	 * @since   1.0
	 */
	protected function loadLanguage()
	{
		return $this;
	}

	/**
	 * Loads Media CSS and JS files for Template and Template Views
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function loadViewMedia()
	{
		if ($this->type == 'asset' || $this->type == 'metadata') {
			return $this;
		}

		$priority = Services::Registry()->get('Parameters', 'criteria_media_priority_other_extension', 400);

		$file_path = Services::Registry()->get('Parameters', 'template_view_path');
		$url_path = Services::Registry()->get('Parameters', 'template_view_path_url');

		Services::Asset()->addCssFolder($file_path, $url_path, $priority);
		Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
		Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

		return $this;
	}
}
