<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

defined('MOLAJO') or die;

/**
 * Page
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class PageIncluder extends Includer
{
    /**
     * @param   string $name
     * @param   string $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', 0);
        parent::__construct($name, $type);
        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_html_display_filter', false);

        return $this;
    }

    /**
     * Loads Media CSS and JS files for Page Views
     *
     * @return  null
     * @since   1.0
     */
    protected function loadViewMedia()
    {

    }
}
