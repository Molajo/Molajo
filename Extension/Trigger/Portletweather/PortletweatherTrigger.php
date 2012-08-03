<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Portletweather;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Portletweather
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class PortletweatherTrigger extends ContentTrigger
{

	/**
	 * After-read processing
	 *
	 * Retrieves Portletweather via Google API
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (strtolower($this->get('template_view_path_node')) == 'portletweather') {
		} else {
			return true;
		}

		$city = $this->parameters['city'];
		$zip = $this->parameters['zip'];
		$country = $this->parameters['country'];
		$fahrenheit_or_celsius = $this->parameters['fahrenheit_or_celsius'];
		$language = $this->parameters['language'];

		$icons_google = '/ig/images/weather/';
		$icons_local = __DIR__ . '/Images/';

		if ($city == '') {
			$station = $zip . '-' . $country;
		} else {
			$station = $city;
		}

		$api = simplexml_load_string(
			utf8_encode(
				file_get_contents('http://www.google.com/ig/api?weather=' . $station . '&hl=' . $language)
			)
		);

		$query_results = array();

		$i = 1;
		foreach ($api->weather->forecast_conditions as $weather) {

			$row = new \stdClass();

			$row->city = $api->weather->forecast_information->city->attributes()->data;
			$row->postal_code = $api->weather->forecast_information->postal_code->attributes()->data;
			$row->forecast_date = $api->weather->forecast_information->forecast_date->attributes()->data;
			$row->current_date_time = $api->weather->forecast_information->current_date_time->attributes()->data;

			$row->now_condition = $api->weather->current_conditions->condition->attributes()->data;

			if ($fahrenheit_or_celsius == 'c') {
				$row->now_temp = $api->weather->current_conditions->temp_c->attributes()->data;
			} else {
				$row->now_temp = $api->weather->current_conditions->temp_f->attributes()->data;
			}

			$row->humidity = $api->weather->current_conditions->humidity->attributes()->data;
			$row->wind_condition = $api->weather->current_conditions->wind_condition->attributes()->data;
			$row->icon = str_replace($icons_google, $icons_local,
				$api->weather->current_conditions->icon->attributes()->data);

			$row->title = Services::Language()->translate('Portletweather for ') . $row->city;

			$row->forecast_day_of_week = $weather->day_of_week->attributes()->data;
			$row->forecast_low_temperature = $weather->low->attributes()->data;
			$row->forecast_high_temperature = $weather->high->attributes()->data;
			$row->forecast_icon = str_replace($icons_google, $icons_local, $weather->icon->attributes()->data);
			$row->forecast_condition = $weather->condition->attributes()->data;

			$query_results[] = $row;
			$i++;
		}

		$this->data = $query_results;

		return true;
	}
}
