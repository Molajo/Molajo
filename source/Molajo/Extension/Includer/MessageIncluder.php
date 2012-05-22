<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Application;
use Molajo\Extension\Helpers;
use Molajo\Service\Services;
use Molajo\Extension\Includer;
use Molajo\MVC\Controller\DisplayController;

defined('MOLAJO') or die;

/**
 * Message
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class MessageIncluder extends Includer
{
	/**
	 * __construct
	 *
	 * Class constructor.
	 *
	 * @param  string $name
	 * @param  string $type
	 *
	 * @return  null
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
	 * getExtension
	 *
	 * Retrieve extension information after looking up the ID in the extension-specific includer
	 *
	 * @return bool
	 * @since 1.0
	 */
	protected function getExtension($extension_id = null)
	{
		return $this;
	}

	/**
	 *  setRenderCriteria
	 *
	 *  Retrieve default values, if not provided by extension
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function setRenderCriteria()
	{
		Services::Registry()->set('Parameters', 'display_view_on_no_results', 1);

		if ((int)Services::Registry()->get('Parameters', 'template_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'template_view_id',
				Services::Registry()->get('Configuration', 'message_template_view_id'));
		}
		if ((int)Services::Registry()->get('Parameters', 'wrap_view_id', 0) == 0) {
			Services::Registry()->set('Parameters', 'wrap_view_id',
				Services::Registry()->get('Configuration', 'message_wrap_view_id'));
		}

		Services::Registry()->set('Parameters', 'model_name', 'dboMessages');
		Services::Registry()->set('Parameters', 'model_type', 'Table');
		Services::Registry()->set('Parameters', 'model_query_object', 'getMessages');

		/** Final Template and Wrap selections */
		Services::Registry()->merge('Configuration', 'Parameters', true);

		Helpers::Extension()->finalizeParameters(
			Services::Registry()->get('Parameters', 'content_id', 0),
			Services::Registry()->get('Parameters', 'request_action', 'display')
		);

		/* Yes, this is done before, too. Get over it or fix it. */
		Services::Registry()->set('Parameters', 'model_name', 'Assets');
		Services::Registry()->set('Parameters', 'model_type', 'Table');
		Services::Registry()->set('Parameters', 'model_query_object', 'getAssets');

		/** Sort */
		Services::Registry()->sort('Include');
		Services::Registry()->sort('Parameters');

		return;
	}

	/**
	 * Loads Language Files for extension
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadLanguage()
	{
		return $this;
	}

	/**
	 * Loads Media CSS and JS files for Template and Wrap Views
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadViewMedia()
	{
		return $this;
	}
}
