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
 * Sessions
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class SessionsModel extends LoadModel
{
    /**
     * __construct
     *
     * @param  $id
     *
     * $return object
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table_name = '#__sessions';
        $this->primary_key = 'session_id';

        parent::__construct($id);
    }
}
