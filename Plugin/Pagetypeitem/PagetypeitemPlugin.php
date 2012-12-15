<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeitem;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Plugin\Pagetypeconfiguration;
use Molajo\Service\Services;


defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     GNU GPL v 2, or later and MIT
 * @since       1.0
 */
class PagetypeitemPlugin extends Plugin
{
    /**
     * Switches the model registry for an item since the Content Query already retrieved the data
     *  and saved it into the registry
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('catalog_page_type', '', 'parameters')) == strtolower(PAGE_TYPE_ITEM)) {
        } else {
            return true;
        }

        return true;
    }
}
