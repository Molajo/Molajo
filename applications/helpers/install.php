<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Install
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoInstallHelper
{
    /**
     * parseManifestXML
     *
     * @param string $xml path to the XML file
     *
     * @return array|bool XML metadata.
     * @since   1.0
     */
    public static function parseManifestXML($path)
    {
        if (JFolder::exists($path)) {
        } else {
            return false;
        }

        $xml = Molajo::XML($path.'/manifest.xml');
        if ($xml === false) {
            return false;
        }

        $data = array();

        $data['type']           = (string)$xml->type;
        $data['name']           = (string)$xml->name;
        $data['author']         = (string)$xml->author();
        $data['create_date']    = (string)$xml->create_date();
        $data['copyright']      = (string)$xml->copyright;
        $data['license']        = (string)$xml->license;
        $data['author_email']   = (string)$xml->author_email;
        $data['author_url']     = (string)$xml->author_url;
        $data['version']        = (string)$xml->version;
        $data['description']    = (string)$xml->description;

        return $data;
    }
}
