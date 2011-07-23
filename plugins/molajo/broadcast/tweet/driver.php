<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Broadcast Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

class MolajoBroadcastTweet {

    /**
     * Minutes
     *
     * @var	string
     * @access	public
     */
    protected $days;

    /**
     * Driver
     *
     * Method called by plgMolajoBroadcast::MolajoOnContentChangeState
     *
     * @param	none
     * @return	boolean
     * @since	1.6
     */
    function driver ()
    {
        /** com_responses parameters **/
        $responsesParams = MolajoComponentHelper::getParams('com_responses', true);


        /** email **/
        if ($responsesParams->def('xxxx', 0) == '0') {
            return false;
        }

        $this->days = $responsesParams->def('xxxx', 7);

        return true;
    }
}