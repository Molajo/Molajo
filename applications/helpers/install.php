<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoInstallHelper
 *
 * @package     Molajo
 * @subpackage  Install Helper
 * @since       1.0
 */
abstract class MolajoInstallHelper
{

    /**
     * parseManifestXML
     *
     * Parses install manifest XML
     *
     * @param string $xml
     *
     * @return array|bool XML metadata.
     *
     * @since   1.0
     */
    static public function parseManifestXML($path)
    {
        if ($xml = MolajoController::getXML($path)) {
        } else {
            return false;
        }

        /** XML Root: install - all extensions except languages which use manifest */
        if ($xml->getName() == 'install'
            || $xml->getName() == 'manifest'
        ) {
        } else {
            return false;
        }

        $data = array();

        $data['name'] = (string)$xml->name;
        $data['description'] = (string)$xml->description;

        if ($xml->getName() == 'manifest') {
            $data['type'] = 'language';

        } else if ($xml->getName() == 'install') {
            $data['type'] = (string)$xml->attributes()->type;

        } else {
            return false;
        }
        $data['group'] = (string)$xml->group;
        $data['method'] = (string)$xml->method;

        if ((string)$xml->create_date()) {
            $data['create_date'] = (string)$xml->create_date();
        } else {
            $data['create_date'] = MolajoTextHelper::_('Unknown');
        }
        $data['version'] = (string)$xml->version;
        $data['copyright'] = (string)$xml->copyright;
        $data['license'] = (string)$xml->license;

        if ((string)$xml->author()) {
            $data['author'] = (string)$xml->author();
        } else {
            $data['author'] = MolajoTextHelper::_('Unknown');
        }
        $data['author_email'] = (string)$xml->author_email;
        $data['author_url'] = (string)$xml->author_url;

        return $data;
    }
}