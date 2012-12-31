<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Pagetypes;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class PagetypesPlugin extends Plugin
{
    /**
     * Generates list of Pagetypes
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        $folders = Services::Filesystem()->folderFolders(EXTENSIONS . '/Menuitem');
        if (count($folders) === 0 || $folders === false) {
            $page_type_list = array();
        } else {
            $page_type_list = $folders;
        }

        $folders = Services::Filesystem()->folderFolders(PLATFORM_FOLDER . '/Menuitem');
        if (count($folders) === 0 || $folders === false) {
        } else {
            $new            = array_merge($page_type_list, $folders);
            $page_type_list = $new;
        }

        $newer = array_unique($page_type_list);
        sort($newer);

        $page_types = array();
        foreach ($newer as $item) {
            $temp_row        = new \stdClass();
            $temp_row->value = $item;
            $temp_row->id    = $item;
            $page_types[]    = $temp_row;
        }

        Services::Registry()->set('Datalist', 'Pagetypes', $page_types);

        return true;
    }
}
