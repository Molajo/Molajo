<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Media Oembed
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoMediaOembed
{

    /**
     * Driver
     *
     * Method called by plgMolajoWebServices::MolajoOnAfterRender to load Google Analytics Code
     *
     * @param    none
     * @return    boolean
     * @since    1.6
     */
    function driver($context, &$content, &$parameters, $page, $location)
    {
        $molajoSystemPlugin =& MolajoPlugin::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);
        $temp = $content->$location;

        $regex = '#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is';
        preg_match_all($regex, $content->$location, $matches);
        var_dump($matches);
        die();

        for ($i = 0; $i < count($matches[0]); $i++) {

            $replacethis = $matches[0][$i];
            $replacewith = $replacethis;

            $link = $replacethis;

            $external_link = MolajoApplicationHelperURLs::checkURLExternal($link);

            $valid_link = MolajoApplicationHelperURLs::checkURLValidity($link);
            if ($valid_link) {
                $output_Host = MolajoApplicationHelperURLs::getHost($link);
            }

            $mediaEmbedHelper = new TamkaMediaEmbedHelper ();
            $provider = $mediaEmbedHelper->getProvider($link);
            if (trim($provider) !== '') {
                $replacewith = $provider;

            } elseif (($external_link) && (TAMKA_CONFIGURATION_LINKS_AUTOLINK_URLS)) {
                $replacewith = '<a ' . $externalClass . 'href="' . $link . '" rel="' . $followNoFollow . '">' . $link . ' ' . $external_link_message . '</a>';
            }

            if ($replacethis == $replacewith) {
            } else {
                $content = str_replace($replacethis, $replacewith, $content);
            }
        }

        /**
         *     Process regular links (i.e., <a href="and so on">
         */
        $regex = '/<a href="[^<]*<\/a>/i';
        $matches = array();
        preg_match_all($regex, $content, $matches);

        for ($i = 0; $i < count($matches[0]); $i++) {

            $replacethis = $matches[0][$i];
            $replacewith = $replacethis;

            $link = strtolower(substr($replacethis, (stripos($replacethis, 'href="') + 6), 9999));
            $link = substr($link, 0, stripos($link, '"'));

            $text = $link;
            $text = substr($text, (stripos($text, '>') + 1), 9999);
            $text = substr($text, 0, stripos($text, '<'));

            $external_link = MolajoApplicationHelperURLs::checkURLExternal($link);

            $valid_link = MolajoApplicationHelperURLs::checkURLValidity($link);
            if ($valid_link) {
                $output_Host = MolajoApplicationHelperURLs::getHost($link);
            }

            $mediaEmbedHelper = new TamkaMediaEmbedHelper ();
            $provider = $mediaEmbedHelper->getProvider($link);
            if ($provider) {
                $replacewith = $provider;

                //			} elseif ( ($external_link) && (TAMKA_CONFIGURATION_LINKS_AUTOLINK_URLS) ) {
                //	    	$replacewith = '<a '.$externalClass.'href="'.$link.'" rel="'.$followNoFollow.'" title="'.$text.'">'.$text.$external_link_message.'</a>';
            }

            if ($replacethis == $replacewith) {
            } else {
                $content = str_replace($replacethis, $replacewith, $content);
            }
        }
    }


}
}