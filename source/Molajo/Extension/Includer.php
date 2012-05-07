<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension;

use Molajo\Application;
use Molajo\Extension\Helpers;
use Molajo\Service\Services;

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
	 * <include:extension statement attr1=x attr2=y attrN="and-so-on" />
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $attributes = array();

	/**
	 * $extension_required
	 *
	 * Some includes (ex. head, messages, defer), do not require
	 * an extension for further processing. In those cases, this
	 * indicator is set to false.
	 *
	 * @var    bool
	 * @since  1.0
	 */
	protected $extension_required = true;

	/**
	 * $rendered_output
	 *
	 * Rendered output resulting from MVC processing
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $rendered_output;

	/**
	 * $items
	 *
	 * Used only for event processing and will be passed into the
	 * MVC to serve as the Model data source
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $items;

	/**
	 * __construct
	 *
	 * Class constructor.
	 *
	 * @param  string $name
	 * @param  string $type
	 * @param  array  $items (used for event processing includes, only)
	 *
	 * @return  null
	 * @since   1.0
	 */
	public function __construct($name = null, $type = null, $items = null)
	{
		$this->name = $name;
		$this->type = $type;
		$this->items = $items;

		Services::Registry()->createRegistry('Parameters');
	}

	/**
	 * process
	 *
	 * - Loads Metadata (only Theme Includer)
	 * - Loads Language files for Extension
	 * - Loads Assets for Extension
	 * - Activates Controller for Task
	 * - Captures Rendered Output
	 * - Returns Rendered Output to Molajo::Parse to use for replacing with <include:type />
	 *
	 * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function process($attributes = array())
	{
		echo $this->name .' '.$this->type .' '.$this->items.'<br />';

		/** attributes from <include:type */
		$this->attributes = $attributes;

		/** initialises and populates the MVC request */
		$this->setRenderCriteria();

		if ($this->extension_required === true) {
			if (Services::Registry()->get('Parameters', 'extension_instance_id', 0) == 0) {
				return Services::Registry()->set('Parameters', 'status_found', false);
			}
		}

		/** theme include, only - loads metadata for the page */
		$this->loadMetadata();

		/** language must be there before the extension runs */
		$this->loadLanguage();

		/** instantiate MVC and render output */
		$this->rendered_output = $this->invokeMVC();

		/** only load media if there was rendered output */
		if ($this->rendered_output == ''
			&& Services::Registry()->get('Parameters', 'suppress_no_results') == 0
		) {
		} else {
			$this->loadMedia();
			$this->loadViewMedia();
		}

		/** used by events to update $items, if necessary */
		$this->postMVCProcessing();

		return $this->rendered_output;
	}

	/**
	 * setRenderCriteria
	 *
	 * Initialize the request object for MVC values
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{
		/** establish values needed for MVC */
		$this->getAttributes();

		/** Template  */
		Helpers::TemplateView()->get();

		/** Page  */
		Helpers::WrapView()->get();

		return Services::Registry()->set('Parameters', 'status_found', true);
	}

	/**
	 * getAttributes
	 *
	 *  Retrieve request information needed to execute extension
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function getAttributes()
	{
		foreach ($this->attributes as $name => $value) {

			if ($name == 'name' || $name == 'title') {
				Services::Registry()->set('Parameters', 'extension_instance_name', $value);

			} else if ($name == 'tag') {
				$this->tag = $value;


			} else if ($name == 'template') {
				Services::Registry()->set('Parameters', 'template_view_name', $value);

			} else if ($name == 'template_view_css_id'
				|| $name == 'template_view_id'
			) {
				Services::Registry()->set('Parameters', 'template_view_css_id', $value);

			} else if ($name == 'template_view_css_class'
				|| $name == 'view_class'
			) {
				Services::Registry()->set('Parameters', 'template_view_css_class', $value);


			} else if ($name == 'wrap') {
				Services::Registry()->set('Parameters', 'wrap_view_name', $value);

			} else if ($name == 'wrap_view_css_id'
				|| $name == 'wrap_view_id'
			) {
				Services::Registry()->set('Parameters', 'wrap_view_css_id', $value);

			} else if ($name == 'wrap_view_css_class'
				|| $name == 'wrap_view_class'
			) {
				Services::Registry()->set('Parameters', 'wrap_view_css_class', $value);
			}
		}

		/** Retrieve Template View Primary Key */
		if (Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
			if (Services::Registry()->get('Parameters', 'template_view_name', '') == '') {
			} else {
				Services::Registry()->set('Parameters', 'template_view_id',
					ExtensionHelper::getInstanceID(
						CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW,
						Services::Registry()->get('Parameters', 'template_view_name')
					)
				);
			}
		}

		/** Retrieve Wrap View Primary Key */
		if (Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
			if (Services::Registry()->get('Parameters', 'wrap_view_name', '') == '') {
			} else {
				Services::Registry()->set('Parameters', 'wrap_view_id',
					ExtensionHelper::getInstanceID(
						CATALOG_TYPE_EXTENSION_WRAP_VIEW,
						Services::Registry()->get('Parameters', 'wrap_view_name')
					)
				);
			}
		}

		return true;
	}

	/**
	 * getExtension
	 *
	 * Retrieve extension information using either the ID or the name
	 *
	 * @return bool
	 * @since 1.0
	 */
	protected function getExtension()
	{
		if (Services::Registry()->get('Parameters', 'extension_instance_id', 0) == 0) {
			$rows = ExtensionHelper::get(
				(int)Services::Registry()->get('Parameters', 'extension_catalog_type_id'),
				Services::Registry()->get('Parameters', 'extension_instance_name')
			);

		} else {
			$rows = ExtensionHelper::get(
				(int)Services::Registry()->get('Parameters', 'extension_catalog_type_id'),
				(int)Services::Registry()->get('Parameters', 'extension_instance_id')
			);
		}

		/** Extension not found */
		if ((Services::Registry()->get('Parameters', 'extension_instance_id', 0) == 0)
			&& (count($rows) == 0)
		) {
			return Services::Registry()->set('Parameters', 'status_found', false);
		}

		/** Process Results */
		$row = array();
		foreach ($rows as $row) {
		}

		Services::Registry()->set('Parameters', 'extension_instance_id', $row->extension_instance_id);
		Services::Registry()->set('Parameters', 'extension_instance_name', $row->title);
		Services::Registry()->set('Parameters', 'extension_catalog_id', $row->catalog_id);
		Services::Registry()->set('Parameters', 'extension_catalog_type_id', $row->catalog_type_id);
		Services::Registry()->set('Parameters', 'extension_view_group_id', $row->view_group_id);
		Services::Registry()->set('Parameters', 'extension_type', $row->catalog_type_title);

		$this->parameters = Services::Registry()->initialise();
		$this->parameters->loadString($row->parameters);

		Services::Registry()->set('Parameters', 'source_catalog_type_id',
			Services::Registry()->get('Parameters', 'source_catalog_type_id'));

		if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'template_view_id',
				Services::Registry()->get('Parameters', 'template_view_id')
			);
		}

		/** wrap */
		if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'wrap_view_id',
				Services::Registry()->get('Parameters', 'wrap_view_id')
			);
		}

		/** mvc */
		if (Services::Registry()->get('Parameters', 'controller', '') == '') {
			Services::Registry()->set('Parameters', 'controller',
				Services::Registry()->get('Parameters', 'controller', ''));
		}
		if (Services::Registry()->get('Parameters', 'task', '') == '') {
			Services::Registry()->set('Parameters', 'task',
				Services::Registry()->get('Parameters', 'task', 'display'));
		}
		if (Services::Registry()->get('Parameters', 'model', '') == '') {
			Services::Registry()->set('Parameters', 'model',
				Services::Registry()->get('Parameters', 'model', ''));
		}
		if ((int)Services::Registry()->get('Parameters', 'id', 0) == 0) {
			Services::Registry()->set('Parameters', 'id',
				Services::Registry()->get('Parameters', 'id', 0));
		}
		if ((int)Services::Registry()->get('Parameters', 'category_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'category_id',
				Services::Registry()->get('Parameters', 'category_id', 0));
		}
		if ((int)Services::Registry()->get('Parameters', 'suppress_no_results', 0) == 0) {
			Services::Registry()->set('Parameters', 'suppress_no_results',
				Services::Registry()->get('Parameters', 'suppress_no_results', 0));
		}

		Services::Registry()->set('Parameters', 'extension_event_type',
			Services::Registry()->get('Parameters', 'event_type', array('content'))
		);

		Services::Registry()->set('Parameters', 'extension_path',
			ExtensionHelper::getPath(
				Services::Registry()->get('Parameters', 'extension_catalog_type_id'),
				Services::Registry()->get('Parameters', 'extension_instance_name')
			)
		);

		return Services::Registry()->set('Parameters', 'status_found', true);
	}

	/**
	 * loadMetadata
	 *
	 * Theme Includer use, only, loads the page metadata
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadMetadata()
	{
	}

	/**
	 * loadLanguage
	 *
	 * Loads Language Files for extension
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadLanguage()
	{
		return Helpers::Extension()->loadLanguage(Services::Registry()->get('Extension', 'path'));
	}

	/**
	 * loadMedia
	 *
	 * Loads Media CSS and JS files for extension and related content
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadMedia()
	{
	}

	/**
	 * loadViewMedia
	 *
	 * Loads Media CSS and JS files for Template and Wrap Views
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadViewMedia()
	{
		$priority = Services::Registry()->get('Configuration', 'media_priority_other_extension', 400);

		$file_path = Services::Registry()->get('Parameters', 'template_view_path');
		$url_path = Services::Registry()->get('Parameters', 'template_view_path_url');
		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

		$file_path = Services::Registry()->get('Parameters', 'wrap_view_path');
		$url_path = Services::Registry()->get('Parameters', 'wrap_view_path_url');
		$css = Services::Document()->add_css_folder($file_path, $url_path, $priority);
		$js = Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
		$defer = Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);
	}

	/**
	 * invokeMVC
	 *
	 * Instantiate the Controller and fire off the task, returns rendered output
	 *
	 * @return mixed
	 */
	protected function invokeMVC()
	{
		$m = Application::Controller()->connect(Services::Registry()->get('Parameters', 'table'));

		$m->model->set('id', Services::Registry()->get('Parameters', 'id'));

		$m->model->set('get_special_fields', 1);
		$m->model->set('use_special_joins', false);
		$m->model->set('add_acl_check', false);

		$results = $m->display();

		if (Services::Registry()->get('Configuration', 'debug', 0) == 1) {
			Services::Debug()->set(' ');
			Services::Debug()->set('Includer::invokeMVC');
			//Services::Debug()->set('Controller: ' . $cc . ' Task: ' . $task . ' Model: ' . $model . ' ');
			Services::Debug()->set('Extension: ' . Services::Registry()->get('Parameters', 'extension_instance_name') . ' ID: ' . Services::Registry()->get('Parameters', 'id') . '');
			Services::Debug()->set('Template: ' . Services::Registry()->get('Parameters', 'template_view_path') . '');
			Services::Debug()->set('Wrap: ' . Services::Registry()->get('Parameters', 'wrap_view_path') . '');
		}

		/** html display filters
		Services::Registry()->set('Parameters', 'html_display_filter', false);
		if (Services::Registry()->get('Parameters', 'html_display_filter', true) == false) {
			return $results;
		} else {
			return Services::Filter()->filter_html($results);
		}
		 */
	}

	/**
	 * postMVCProcessing
	 * @return bool
	 */
	protected function postMVCProcessing()
	{

	}
}
