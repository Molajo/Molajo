<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Admindashboard;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AdmindashboardTrigger extends ContentTrigger
{

    /**
     * Before-read processing
     *
     * Prepares data for the Administrator Grid  - position AdmindashboardTrigger last
     *
     * @return void
     * @since   1.0
     */
    public function onBeforeParse()
    {
        /** Is this an Administrative Grid Request?  */
        if (strtolower($this->get('template_view_path_node')) == 'admindashboard') {
        } else {
            return true;
        }

        return true;
    }
}
