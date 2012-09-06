<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo;

use Molajo\Service\Services;
use Molajo\MVC\Controller\DisplayController;

defined('MOLAJO') or die;

/**
 * Includer
 *
 * @package     Molajo
 * @subpackage  Extension
 * @since       1.0
 */
class Includer
{
	/**
	 * $name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name = null;

	/**
	 * $type
	 * Examples: head, message, tag, request, resource, defer
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type = null;

	/**
	 * $tag
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $tag = null;

	/**
	 * $attributes
	 *
	 * Extracted in Parser Class from Theme/Rendered output
	 *
	 * <include:extension statement attr1=x attr2=y attrN="and-so-on" />
	 * template, template_view_css_id, template_view_css_class
	 * wrap, wrap_view_css_id, wrap_view_css_class
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $attributes = array();

	/**
	 * @param string $name
	 * @param string $type
	 *
	 * @return null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null)
	{
		$this->name = $name;
		$this->type = $type;

		Services::Registry()->createRegistry('Include');

		Services::Registry()->set('Parameters', 'includer_name', $this->name);
		Services::Registry()->set('Parameters', 'includer_type', $this->type);
		Services::Registry()->copy('RouteParameters', 'Parameters', 'Request*');
		Services::Registry()->copy('RouteParameters', 'Parameters', 'Theme*');
		Services::Registry()->copy('RouteParameters', 'Parameters', 'Page*');
		Services::Registry()->copy('RouteParameters', 'Parameters', 'Ui*');

		return;
	}

	/**
	 * process
	 *
	 * - Loads Metadata (only Theme Includer)
	 * - Loads Language files for Extension
	 * - Loads Assets for Extension
	 * - Activates Controller for Task
	 * - Returns Rendered Output to Parse for <include:type /> replacement
	 *
	 * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function process($attributes = array())
	{
		/** attributes from <include:type */
		$this->attributes = $attributes;

		$this->getAttributes();

		/** retrieve the extension that will be used to generate the MVC request */
		$this->getExtension();

		/** initialises and populates the MVC request */
		$results = $this->setRenderCriteria();
		if ($results == false) {
			return false;
		}

		/** language must be there before the extension runs */
		$this->loadLanguage();

		/** instantiate MVC and render output */
		$rendered_output = $this->invokeMVC();

		/** only load media if there was rendered output */
		if ($rendered_output == ''
			&& Services::Registry()->get('Parameters', 'criteria_display_view_on_no_results') == 0
		) {
		} else {
			$this->loadMedia();
			$this->loadViewMedia();
			$this->loadPlugins();
		}

		return $rendered_output;
	}

	/**
	 * getAttributes
	 *
	 * Use the view and/or wrap criteria ife specified on the <include statement
	 *
	 * @return null
	 * @since   1.0
	 */
	protected function getAttributes()
	{
		if (count($this->attributes) > 0) {
		} else {
			return;
		}

		//todo filter input appropriately
		//todo case statements
		foreach ($this->attributes as $name => $value) {

			/** Name used by includer for extension */
			if ($name == 'name' || $name == 'title') {

				if ($this->name == 'template') {

					if ((int)$value > 0) {
						$template_id = (int)$value;
						$template_title = Helpers::Extension()->getExtensionNode($template_id);
					} else {
						$template_title = ucfirst(strtolower(trim($value)));
						$template_id = Helpers::Extension()
							->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, $template_title);
					}

					Services::Registry()->set('Parameters', 'template_view_id', $template_id);
					Services::Registry()->set('Parameters', 'template_view_path_node', $template_title);
					Services::Registry()->set('Parameters', 'extension_title', $template_title);

				} else {
					$value = ucfirst(strtolower(trim($value)));
					Services::Registry()->set('Parameters', 'extension_title', $value);
				}
				/** Used to extract a list of extensions for inclusion */
			} elseif ($name == 'tag') {
				$this->tag = $value;

				/** Template */
			} elseif ($name == 'template' || $name == 'template_view_title'
				|| $name == 'template_view' || $name == 'template_view'
			) {
				$value = ucfirst(strtolower(trim($value)));

				$template_id = Helpers::Extension()
					->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, $value);

				if ((int)$template_id == 0) {
				} else {
					Services::Registry()->set('Parameters', 'template_view_id', $template_id);
					Services::Registry()->get('Parameters', 'template_view_path_node', $value);
				}

			} elseif ($name == 'template_view_css_id' || $name == 'template_css_id' || $name == 'template_id') {
				Services::Registry()->set('Parameters', 'template_view_css_id', $value);

			} elseif ($name == 'template_view_css_class' || $name == 'template_css_class' || $name == 'template_class') {
				Services::Registry()->set('Parameters', 'template_view_css_class', str_replace(',', ' ', $value));

				/** Wrap */
			} elseif ($name == 'wrap' || $name == 'wrap_view_title' || $name == 'wrap_view' || $name == 'wrap_title') {

				$value = ucfirst(strtolower(trim($value)));

				$wrap_id = Helpers::Extension()
					->getInstanceID(CATALOG_TYPE_EXTENSION_WRAP_VIEW, $value);

				if ((int)$wrap_id == 0) {
				} else {
					Services::Registry()->set('Parameters', 'wrap_view_path_node', $value);
					Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_id);
				}

			} elseif ($name == 'wrap_view_css_id' || $name == 'wrap_css_id' || $name == 'wrap_id') {
				Services::Registry()->set('Parameters', 'wrap_view_css_id', $value);

			} elseif ($name == 'wrap_view_css_class' || $name == 'wrap_css_class' || $name == 'wrap_class') {
				Services::Registry()->set('Parameters', 'wrap_view_css_class', str_replace(',', ' ', $value));

			} elseif ($name == 'wrap_view_role' || $name == 'wrap_role' || $name == 'role') {
				Services::Registry()->set('Parameters', 'wrap_view_role', str_replace(',', ' ', $value));

			} elseif ($name == 'wrap_view_property' || $name == 'wrap_property' || $name == 'property') {
				Services::Registry()->set('Parameters', 'wrap_view_property', str_replace(',', ' ', $value));

				/** Model */
			} elseif ($name == 'datalist') {
				Services::Registry()->set('Parameters', 'datalist', $value);
				Services::Registry()->set('Parameters', 'model_type', 'dbo');
				Services::Registry()->set('Parameters', 'model_name', 'Parameters');
				Services::Registry()->set('Parameters', 'model_query_object', 'getParameters');

			} elseif ($name == 'model_name') {
				Services::Registry()->set('Parameters', 'model_name', $value);

			} elseif ($name == 'model_type') {
				Services::Registry()->set('Parameters', 'model_type', $value);

			} elseif ($name == 'model_parameter' || $name == 'parameter' || $name == 'value') {
				Services::Registry()->set('Parameters', 'model_parameter', $value);
				if ($name == 'value') {
					Services::Registry()->get('Parameters', 'model_parameter');
				}

			} elseif ($name == 'model_query_object' || $name == 'query_object') {
				Services::Registry()->set('Parameters', 'model_query_object', $value);

			} else {
				/** Todo: For security reasons, other parameters must override and match defined parameter values */
				Services::Registry()->set('Parameters', $name, $value);
			}
		}
	}

	/**
	 * getExtension
	 *
	 * Retrieve extension information after looking up the ID in the extension-specific includer
	 *
	 * @return bool
	 * @since 1.0
	 */
	protected function getExtension()
	{
		return;
	}

	/**
	 * setRenderCriteria
	 *
	 * Use the view and/or wrap criteria ife specified on the <include statement
	 * Retrieve View and wrap criteria and path information
	 *
	 * @return bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{

		/**  Template */
		$template_id = 0;
		$template_title = Services::Registry()->get('Parameters', 'template_view_path_node');

		if (trim($template_title) == '') {
		} else {
			$template_id = Helpers::Extension()
				->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, $template_title);
		}

		if ((int)$template_id == 0) {
			$template_id = Services::Registry()->get('Parameters', 'template_view_id');
		}

		if (trim($template_title) == '' || (int)$template_id > 0) {
		} else {
			Services::Registry()->set('Parameters', 'template_view_path_node', $template_title);
			$template_id = Helpers::Extension()
				->getInstanceID(CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW, $template_title);
		}

		if ((int)$template_id == 0) {
			$template_id = Helpers::View()->getDefault('Template');
		}

		Services::Registry()->set('Parameters', 'template_view_id', $template_id);

		Services::Registry()->merge('Configuration', 'Parameters', true);

		/** Save existing parameters */
		$savedParameters = array();
		$temp = Services::Registry()->getArray('Parameters');
		if (is_array($temp) && count($temp) > 0) {
			foreach ($temp as $key => $value) {
				if ($value === 0 || trim($value) == '' || $value === null) {
				} else {
					$savedParameters[$key] = $value;
				}
			}
		}

		/** Template  */
		Helpers::View()->get(Services::Registry()->get('Parameters', 'template_view_id'), 'Template');

		/** Merge Parameters in (Pre-wrap) */
		if (is_array($savedParameters) && count($savedParameters) > 0) {
			foreach ($savedParameters as $key => $value) {
				Services::Registry()->set('Parameters', $key, $value);
			}
		}

		/** Default Wrap if needed */
		$wrap_view_id = 0;
		$wrap_view_title = Services::Registry()->get('Parameters', 'wrap_view_path_node');

		if ($wrap_view_title === null) {
			$wrap_view_id = Services::Registry()->get('Parameters', 'wrap_view_id');
			if ((int)$wrap_view_id === 0) {
			} else {
				Services::Registry()->set('Parameters', 'wrap_view_path_node',
					Helpers::Extension()->getExtensionNode((int)$wrap_view_id));
				$wrap_view_title = Services::Registry()->get('Parameters', 'wrap_view_path_node');
			}
		}

		if ($wrap_view_title === null) {
			$wrap_view_title = 'None';
		}

		Services::Registry()->set('Parameters', 'wrap_view_path_node', $wrap_view_title);
		Services::Registry()->set('Parameters', 'wrap_view_title', $wrap_view_title);
		Services::Registry()->set('Parameters', 'wrap_view_path',
			Helpers::View()->getPath($wrap_view_title, 'Wrap'));
		Services::Registry()->set('Parameters', 'wrap_view_path_url',
			Helpers::View()->getPathURL($wrap_view_title, 'Wrap'));
		Services::Registry()->set('Parameters', 'wrap_view_namespace',
			Helpers::View()->getNamespace($wrap_view_title, 'Wrap'));

		Services::Registry()->delete('Parameters', 'item*');
		Services::Registry()->delete('Parameters', 'list*');
		Services::Registry()->delete('Parameters', 'form*');

		Services::Registry()->sort('Parameters');


		/** Merge Parameters in (Post-wrap) */
		if (is_array($savedParameters) && count($savedParameters) > 0) {
			foreach ($savedParameters as $key => $value) {
				Services::Registry()->set('Parameters', $key, $value);
			}
		}

		if (Services::Registry()->get('Parameters', 'template_view_id') == 0) {
			return false;
		}

		return true;
	}

	/**
	 * loadLanguage
	 *
	 * Loads Language Files for extension
	 *
	 * @return null
	 * @since   1.0
	 */
	protected function loadLanguage()
	{
		Helpers::Extension()->loadLanguage(
			Services::Registry()->get('Parameters', 'extension_path')
		);
		Helpers::Extension()->loadLanguage(
			Services::Registry()->get('Parameters', 'template_view_path')
		);
		Helpers::Extension()->loadLanguage(
			Services::Registry()->get('Parameters', 'wrap_view_path')
		);

		return;
	}

	/**
	 * 	loadPlugins overrides (or initially loads) Plugins from the Template and/or Wrap View folders
	 *
	 *  @return  void
	 *  @since   1.0
	 */
	protected function loadPlugins()
	{
		$templatePlugins = Services::Filesystem()->folderFolders(
			Services::Registry()->get('Parameters', 'template_view_path') . '/' . 'Plugin'
		);

		if (count($templatePlugins) == 0 || $templatePlugins === false) {
		} else {
			$this->processPlugins(
				$templatePlugins,
				Services::Registry()->get('Parameters', 'template_view_namespace')
			);
		}

		$wrapPlugins = Services::Filesystem()->folderFolders(
			Services::Registry()->get('Parameters', 'wrap_view_path') . '/' . 'Plugin'
		);

		if (count($wrapPlugins) == 0 || $wrapPlugins === false) {
		} else {
			$this->processPlugins(
				$wrapPlugins,
				Services::Registry()->get('Parameters', 'wrap_view_namespace')
			);
		}

		return;
	}

	/**
	 * processPlugins for Theme, Page, and Request Extension (overrides Core and Plugin folder)
	 *
	 * @param  $plugins array of folder names
	 * @param  $path
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function processPlugins($plugins, $path)
	{
		foreach ($plugins as $folder) {
			Services::Event()->process_events(
				$folder . 'Plugin',
				$path . '\\Plugin\\' . $folder . '\\' . $folder . 'Plugin'
			);
		}
	}

	/**
	 * loadMedia
	 *
	 * Loads Media CSS and JS files for extension and related content
	 *
	 * @return null
	 * @since   1.0
	 */
	protected function loadMedia()
	{
		return $this;
	}

	/**
	 * loadViewMedia
	 *
	 * Loads Media CSS and JS files for Template and Wrap Views
	 *
	 * @return null
	 * @since   1.0
	 */
	protected function loadViewMedia()
	{
		$priority = Services::Registry()->get('Parameters', 'criteria_media_priority_other_extension', 400);

		$file_path = Services::Registry()->get('Parameters', 'template_view_path');
		$url_path = Services::Registry()->get('Parameters', 'template_view_path_url');

		$css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
		$js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
		$defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

		$file_path = Services::Registry()->get('Parameters', 'wrap_view_path');
		$url_path = Services::Registry()->get('Parameters', 'wrap_view_path_url');

		$css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
		$js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
		$defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

		return $this;
	}

	/**
	 * invokeMVC
	 *
	 * Instantiate the Controller and fire off the action, returns rendered output
	 *
	 * @return mixed
	 */
	protected function invokeMVC()
	{
		Services::Registry()->sort('Parameters');

		$message = 'Includer->invokeMVC ' . 'Name ' . $this->name . ' Type: ' . $this->type . ' Template: ' . Services::Registry()->get('Parameters', 'template_view_title');
		$message .= ' Parameters:<br />';

		ob_start();
		$message .= Services::Registry()->get('Parameters', '*');
		$message .= ob_get_contents();
		ob_end_clean();

		Services::Profiler()->set($message, LOG_OUTPUT_RENDERING, VERBOSE);

		$controller = new DisplayController();
		$controller->set('id', (int)Services::Registry()->get('Parameters', 'source_id'));

		/** Set Parameters */
		$parms = Services::Registry()->getArray('Parameters');

		if (count($parms) > 0) {
			foreach ($parms as $key => $value) {
				$controller->set($key, $value);
			}
		}
//echo '<br />INCLUDER:: ' .  Services::Registry()->get('Parameters', 'template_view_path');

		$results = $controller->execute();

		return $results;
	}
}

