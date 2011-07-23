<?php
/**
 * @package     Molajo
 * @subpackage  Tags
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();


class plgMolajoTags extends JPlugin	{

    /**
     * plgMolajoTags::MolajoOnContentChangeState
     *
     * System Component Plugin activates tasks:
     *
     * 1: Email
     * 2: Ping
     * 3: Tweet
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		primary key
     * @param	object		value
     * @since	1.6
     */
    function MolajoOnContentChangeState ($context, $pks, $value)
    {
        /** broadcast published state, only **/
        if ($value == 1) {
        } else {
            return;
        }

        /** com_tags parameters **/
        $responsesParams = MolajoComponentHelper::getParams('com_tags', true);

        /** broadcasting enabled **/
        if ($responsesParams->def('enable_broadcast', 0) == '1') {
        } else {
            return;
        }

        /** meta **/
        if ($responsesParams->def('enable_subscriptions', 0) == '1') {
            require_once dirname(__FILE__).'/email/driver.php';
            MolajoTagsEmail::driver ($rows);
        }

        /** tags **/
        if ($responsesParams->def('enable_ping', 0) == '1') {
            require_once dirname(__FILE__).'/ping/driver.php';
            MolajoTagsPing::driver ($rows);
        }

        /** Processing Complete **/
        return;
    }
}