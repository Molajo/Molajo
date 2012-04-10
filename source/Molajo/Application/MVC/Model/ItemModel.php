<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * Item
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
class ItemModel extends LoadModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($table = null, $id = null, $path = null)
    {
        return parent::__construct($table, $id, $path);
    }

    /**
     * store
     *
     * Method to store a row (insert: no PK; update: PK) in the database.
     *
     * @param   boolean True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function store()
    {
        /**
        echo '<pre>';
        var_dump($this->row);
        echo '</pre>';
         */
        if ((int)$this->id == 0) {
            $stored = $this->db->insertObject(
                $this->table_name, $this->row, $this->primary_key);
        } else {
            $stored = $this->db->updateObject(
                $this->table_name, $this->row, $this->primary_key);
        }

        if ($stored) {

        } else {

//			throw new \Exception(
//				MOLAJO_DB_ERROR_STORE_FAILED . ' ' . $this->name
//				. ' '. $this->db->getErrorMsg()
//			);
        }
        /**
        if ($this->_locked) {
        $this->_unlock();
        }
         */

        return true;
    }
}
