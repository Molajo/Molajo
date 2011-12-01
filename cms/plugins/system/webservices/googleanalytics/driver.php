<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Web Services Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoWebservicesGoogleAnalytics {

    /**
     * Driver
     *
     * Method called by plgMolajoWebServices::MolajoOnAfterRender to load Google Analytics Code
     *
     * @param	none
     * @return	boolean
     * @since	1.6
     */
    function driver ()
    {
        /** system plugin **/
        $molajoSystemPlugin =& MolajoApplicationPlugin::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);

        /** Google Analytics **/
        if ($systemParameters->def('enable_google_analytics', 0) == '0') {
            return false;
        }

        $google_analytics_tracking_code = $systemParameters->def('google_analytics_tracking_code', '');

$js = '
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try{
var pageTracker = _gat._getTracker("'.$google_analytics_tracking_code.'");
pageTracker._trackPageview();
} catch(err) {}
</script>
</body>
</html>';

        $buffer = JResponse::getBody();
        $buffer = substr($buffer, 0, strripos ($buffer, '</body>')).$js;
        JResponse::setBody($buffer);

        return true;
    }
}