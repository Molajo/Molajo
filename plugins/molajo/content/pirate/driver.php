<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoContentPirate {

    /**
     * Driver
     *
     * Method called by MolajoContentPirate::MolajoOnAfterRender to load pirate talk JS
     *
     * @param	none
     * @return	boolean
     * @since	1.6
     */
    function driver ()
    {
        /** system plugin **/
        $molajoSystemPlugin =& JPluginHelper::getPlugin('system', 'molajo');
        $systemParams = new JParameter($molajoSystemPlugin->params);

        /** talk like a pirate day **/
        if (($systemParams->def('enable_pirate_day', 0) == 1) && (date("m.d") == '09/19')) {
        } else {
           return false;
        }

        /** load js **/
$js = '
<script src="http://l.yimg.com/d/lib/ydn/js/pirate1252961643.js"></script>
</body>
</html>';

        $buffer = JResponse::getBody();
        $buffer = substr($buffer, 0, strripos ($buffer, '</body>')).$js;
        JResponse::setBody($buffer);

        return true;
    }
}