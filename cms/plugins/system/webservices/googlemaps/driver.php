<?php
/**
 * @package     Molajo
 * @subpackage  Web Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoWebServicesGoogleMaps
{

    /**
     * MolajoWebServicesGoogleMaps::driver
     *
     * Interfaces with Google Static Maps API
     *
     * @param    string        The context for the content passed to the plugin.
     * @param    object        The content object.
     * @param    object        The content parameters
     * @param    string        The 'page' number
     * @param   string          Then name of the text field in the content object
     * @return    string
     * @since    1.6
     *
     * Static Map API
     * http://code.google.com/apis/maps/documentation/staticmaps/
     *
     */
    function driver($context, &$content, &$parameters, $page, $location)
    {
        /** extract maps **/
        preg_match_all("#{googlemaps}(.*?){/googlemaps}#s", $content->$location, $matches);

        if (count($matches) == 0) {
            return;
        }

        /** process maps **/
        for ($i = 0; $i < count($matches[0]); $i++) {

            $key = $content->id . '-' . $i;

            $options = explode(";", $matches[1][$i]);

            /** extract parameter pairs **/
            for ($p = 0; $p < count($options); $p++) {

                if ($p == 0) {
                    $title == '';
                    $latitude = 0;
                    $longitude = 0;
                    $zoom = 0;
                    $width = 0;
                    $height = 0;
                    $maptype = '';
                }

                $x = explode("=", $options[$p]);

                if ($x[0] == 'title') {
                    $title = filter_var($x[1], FILTER_SANITIZE_STRING);

                } else if ($x[0] == 'center') {
                    $y = explode(',', $x[1]);
                    if (is_numeric($y[0])) {
                        $latitude = $y[0];
                    } else {
                        $latitude = 0;
                    }
                    if (is_numeric($y[1])) {
                        $longitude = $y[1];
                    } else {
                        $longitude = 0;
                    }

                } else if ($x[0] == 'zoom') {
                    if (((int)$x[1] > 0) && ((int)$x[1] < 22)) {
                        $zoom = $x[1];
                    } else {
                        $zoom = 0;
                    }

                } else if ($x[0] == 'size') {
                    $y = explode('x', $x[1]);
                    if (is_numeric($y[0])) {
                        $width = $y[0];
                    } else {
                        $width = 0;
                    }
                    if (is_numeric($y[1])) {
                        $height = $y[1];
                    } else {
                        $height = 0;
                    }

                } else if ($x[0] == 'maptype') {
                    if (($x[1] == 'roadmap') || ($x[1] == 'satellite') || ($x[1] == 'terrain') || ($x[1] == 'hybrid ')) {
                        $maptype = $x[1];
                    } else {
                        $maptype = 'roadmap';
                    }
                }
            }

            /** generate map request **/
            $mapScript = '

            function initialize() {
                var myLatlng = new google.maps.LatLng(' . $latitude . ',' . $longitude . ');
                var myOptions = {
                  zoom: ' . $zoom . ',
                  center: myLatlng,
                  mapTypeId: google.maps.MapTypeId.' . $maptype . ',
            };
            var map = new google.maps.Map(document.getElementById("map' . $key . '"),
                myOptions);
            }

  function loadScript() {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "http://maps.google.com/maps/api/js?sensor=false&callback=initialize";
    document.body.appendChild(script);
  }

  window.onload = loadScript;


';
            /** load map request **/
            if ($i == 0) {
                //                $document =& MolajoFactory::getDocument();
                //                $document->addScript('http://maps.google.com/maps/api/js?sensor=false');

                $molajoSystemPlugin =& MolajoPlugin::getPlugin('system', 'molajo');
                $systemParameters = new JParameter($molajoSystemPlugin->parameters);
            }

            //            $document->addScriptDeclaration($mapScript);


            /** static map request **/
            //            if ($systemParameters->def('enable_static_google_map', 0) == 1) {

            if ($title == '') {
                $title = $content->$title;
            }

            $staticMap = 'http://maps.google.com/maps/api/staticmap?alt=' . $title;
            $staticMap .= '&center=' . $latitude . ',' . $longitude;
            $staticMap .= '&zoom=' . $zoom;
            $staticMap .= '&size=' . $width . 'x' . $height;
            $staticMap .= '&maptype=' . $maptype;

            //                $layoutOutput = '<div id="map'.$key.'"><noscript><img src="'.$staticMap.'&sensor=false" width="'.$width.'" height="'.$height.'" /></noscript></div>';
            $layoutOutput = '<div id="map' . $key . '"><img src="' . $staticMap . '&sensor=false" width="' . $width . '" height="' . $height . '" /></div>';

            /** no static map request **/
            //            } else {
            //                $layoutOutput = '<div id="map'.$key.'" width="'.$width.'" height="'.$height.'" title="'.$title.'" /></div>';
            //            }
            /** insert into output and replace request **/
            $content->$location = str_replace($matches[0][$i], $layoutOutput, $content->$location);
        }
    }
}
