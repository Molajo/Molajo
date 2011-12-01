<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Protect Bad Words Plugin
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class protectBadWords
{
    function checkWords($cleanString)
    {
        /**
         *     Retrieve User Group Parameter for Auto Publish
         */
        $tamkaLibraryPlugin =& MolajoPlugin::getPlugin('system', 'tamka');
        $tamkaLibraryPluginParameters = new JParameter($tamkaLibraryPlugin->parameters);

        /**
         *     Filter content through array of Bad Words
         */
        $badWords = explode(",", $tamkaLibraryPluginParameters->def('badword', ''));
        return str_replace($badWords, '', $cleanString);

    }
}