<?php
/**
 * Google Analytics Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Googleanalytics;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Google Analytics Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class GoogleanalyticsPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Insert Google Analytics
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRender()
    {
        $account = $this->runtime_data->application->parameters->application_google_analytics_code;

        return $this;

        if (trim($account) == '') {
            return $this;
        }

        $js = "var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '" . $account . "']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();";

        $this->document_js->set('inline', $js, 100, 1, 'text/javascript');

        return $this;
    }
}
