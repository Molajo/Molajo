<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Module;

use Molajo\MVC\Model\DisplayModel;
use Molajo\Services;

defined('MOLAJO') or die;

/**
 * ModuleAdminSubmenuModel
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class ModuleAdminSubmenuModel extends DisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct($table = null, $id = null, $path = null)
    {
        $this->name = get_class($this);

        return parent::__construct($table, $id, $path);
    }

    /**
     * getData
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getData()
    {
        $links =
            Service::Parameter()
                ->get('submenu_items', 'Request');

        $linksArray = explode(',', $links);

        $this->data = array();
        foreach ($linksArray as $link) {
            $this->data[] = $link;
        }

        return $this->data;
    }
}
