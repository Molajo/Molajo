<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * Actions
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class Table extends DisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($table = null, $id = null)
    {
        $this->name = get_class($this);
        $this->table_name = '#__actions';
        $this->primary_key = 'id';

        return parent::__construct($id);
    }
}
