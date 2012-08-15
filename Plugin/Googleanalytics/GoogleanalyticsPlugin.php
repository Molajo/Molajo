<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Googleanalytics;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class GoogleanalyticsPlugin extends ContentPlugin
{
	/**
	 * Insert Google Analytics
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{

		/** Not authorised and not found */
		if ($this->get('model_type') == ''
			|| $this->get('model_name') == '') {
			return true;
		}

		$account = Services::Registry()->get('Configuration', 'application_google_analytics_code');

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

		Services::Asset()->addJSDeclarations($js, 100, 1, 'text/javascript');

		return true;
	}
}
