<?php
/**
 * @package   Molajo
 * @subpackage  Module
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;
namespace Molajo\Extension\Module;

defined('MOLAJO') or die;

/**
 * GridFilters
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class ModuleGridFiltersModel extends Model
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
     * @return array
     * @since    1.0
     */
    public function getData()
    {
    }
}
