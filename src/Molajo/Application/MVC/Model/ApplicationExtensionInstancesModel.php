<?php
/**
 * @package   Molajo
 * @subpackage  Model
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * ApplicationExtensionInstancesModel
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class ApplicationExtensionInstancesModel extends DisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table_name = '#__application_extension_instances';
        $this->primary_key = 'id';

        return parent::__construct($id);
    }
}

