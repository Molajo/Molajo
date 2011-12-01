<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoContentPirate
{

    /**
     * Driver
     *
     * Method called by MolajoContentPirate::MolajoOnAfterRender to load pirate talk JS
     *
     * @param    none
     * @return    boolean
     * @since    1.6
     */
    function driver()
    {
        /** system plugin **/
        $molajoSystemPlugin =& MolajoPlugin::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);

        /** talk like a pirate day **/
        if (($systemParameters->def('enable_pirate_day', 0) == 1) && (date("m.d") == '09/19')) {
        } else {
            return false;
        }

        /** load js **/
        $js = '
<script src="http://l.yimg.com/d/lib/ydn/js/pirate1252961643.js"></script>
</body>
</html>';

        $buffer = JResponse::getBody();
        $buffer = substr($buffer, 0, strripos($buffer, '</body>')) . $js;
        JResponse::setBody($buffer);

        return true;
    }
}