<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Googleanalytics;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class GoogleanalyticsPlugin extends Plugin
{
    /**
     * Insert Google Analytics
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        $account = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_google_analytics_code');

        if (trim($account) == '') {
            return true;
        }

        $js = "var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '" . $account . "']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();";

        Services::Asset($this->assets)->addJSDeclarations($js, 100, 1, 'text/javascript');

        return true;
    }
}
