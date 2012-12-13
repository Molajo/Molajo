<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Ajax;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AjaxPlugin extends Plugin
{
    /**
     * Identify Ajax Request (run last in onBeforeParse):
     *    Adapt the Parse Include File Parameters to only generate the Request
     *     Adapt the Template and Wrap Parameters to generate consumable output
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        if ((int) Services::Registry()->get(CLIENT_LITERAL, 'Ajax') == 0) {
            return true;
        }
/**

        Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id', 1342);
        $this->viewHelper->get(1342, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_id', 2090);
        $this->viewHelper->get(2090, CATALOG_TYPE_WRAP_VIEW_LITERAL);

        Services::Registry()->set(OVERRIDE_LITERAL, 'parse_sequence', 'Ajax_sequence');
        Services::Registry()->set(OVERRIDE_LITERAL, 'parse_final', 'Ajax_final');
*/
        return true;
    }
}
