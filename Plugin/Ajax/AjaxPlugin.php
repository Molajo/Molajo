<?php
/**
 * Ajax Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugin\Ajax;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Determines if an Ajax request was made, If so, rendering sequence and options are modified
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
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
        echo 'onBeforeParse';
        die;
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $view = $this->viewHelper->get(0, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        if ((int)Services::Client()->get('ajax') == 0) {
            return true;
        }

        $this->set('template_view_id', 1342);
        $this->set('wrap_view_id', 2090);

        $this->viewHelper->get(2090, CATALOG_TYPE_WRAP_VIEW_LITERAL);

        Services::Registry()->set(OVERRIDE_LITERAL, 'parse_sequence', 'Ajax_sequence');
        Services::Registry()->set(OVERRIDE_LITERAL, 'parse_final', 'Ajax_final');

        return true;
    }
}
