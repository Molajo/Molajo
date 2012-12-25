<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Weather;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Weather
 *
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class WeatherPlugin extends Plugin
{
    /**
     * After-read processing
     *
     * Retrieves Weather via Google API
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (defined('ROUTE')) {
        } else {
            return true;
        }

        if (strtolower($this->get('template_view_path_node', '', 'parameters')) == 'weather') {
        } else {
            return true;
        }

        $city = $this->parameters['city'];
        $zip = $this->parameters['zip'];
        $country = $this->parameters['country'];
        $fahrenheit_or_celsius = $this->parameters['fahrenheit_or_celsius'];
        $language = $this->parameters['language'];

        if ($city == '') {
            $station = $zip . '-' . $country;
        } else {
            $station = $city;
        }

        $url = 'http://www.google.com/ig/api?weather='
            . utf8_encode(urlencode($station))
            . '&hl=' . $language;

        try {
            $results = file_get_contents($url);

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        if ($results === false) {
            $row = new \stdClass();
            $row->results = 0;
            $this->row = $row;

            return true;
        }

        $api = simplexml_load_string(utf8_encode($results));

        $row = new \stdClass();

        $row->results = 1;
        $row->city = $api->weather->forecast_information->city->attributes()->data;
        $row->postal_code = $api->weather->forecast_information->postal_code->attributes()->data;
        $row->forecast_date = $api->weather->forecast_information->forecast_date->attributes()->data;
        $row->current_date_time = $api->weather->forecast_information->current_date_time->attributes()->data;
        $row->now_condition = $api->weather->current_conditions->condition->attributes()->data;

        if ($fahrenheit_or_celsius == 'c') {
            $append = ' °C';
        } else {
            $append = ' °F';
        }
        $row->now_temp = $api->weather->current_conditions->temp_c->attributes()->data . $append;
        $row->now_humidity = $api->weather->current_conditions->humidity->attributes()->data;
        $row->now_wind_condition = $api->weather->current_conditions->wind_condition->attributes()->data;
        $row->now_icon = 'http://www.google.com' . $api->weather->current_conditions->icon->attributes()->data;
        $row->title = Services::Language()->translate('Weather for ') . $city;

        $i = 0;
        foreach ($api->weather->forecast_conditions as $weather) {

            if ($i == 0) {
                $row->day1_forecast_day_of_week = $weather->day_of_week->attributes()->data;
                $row->day1_forecast_low_temperature = $weather->low->attributes()->data . $append;
                $row->day1_forecast_high_temperature = $weather->high->attributes()->data . $append;
                $row->day1_forecast_icon = 'http://www.google.com' . $weather->icon->attributes()->data;
                $row->day1_forecast_condition = $weather->condition->attributes()->data;

            } elseif ($i == 1) {
                $row->day2_forecast_day_of_week = $weather->day_of_week->attributes()->data;
                $row->day2_forecast_low_temperature = $weather->low->attributes()->data . $append;
                $row->day2_forecast_high_temperature = $weather->high->attributes()->data . $append;
                $row->day2_forecast_icon = 'http://www.google.com' . $weather->icon->attributes()->data;
                $row->day2_forecast_condition = $weather->condition->attributes()->data;

            } elseif ($i == 2) {
                $row->day3_forecast_day_of_week = $weather->day_of_week->attributes()->data;
                $row->day3_forecast_low_temperature = $weather->low->attributes()->data . $append;
                $row->day3_forecast_high_temperature = $weather->high->attributes()->data . $append;
                $row->day3_forecast_icon = 'http://www.google.com' . $weather->icon->attributes()->data;
                $row->day3_forecast_condition = $weather->condition->attributes()->data;

            } elseif ($i == 3) {
                $row->day4_forecast_day_of_week = $weather->day_of_week->attributes()->data;
                $row->day4_forecast_low_temperature = $weather->low->attributes()->data;
                $row->day4_forecast_high_temperature = $weather->high->attributes()->data;
                $row->day4_forecast_icon = 'http://www.google.com' . $weather->icon->attributes()->data;
                $row->day4_forecast_condition = $weather->condition->attributes()->data;
            }

            $i++;
        }

        $this->row = $row;

        return true;
    }
}
