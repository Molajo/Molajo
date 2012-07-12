<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Pagination;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Pagination
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class PaginationTrigger extends ContentTrigger
{

    /**
     * Post-read processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        return true;
    }

    /**
     * getPagination
     *
     * Method to get a JPagination object for the data set.
     *
     * @return object A JPagination object for the data set.
     * @since    1.0
     */
    public function getPagination()
    {
        /** get pagination id **/
        $store = $this->getStoreId('getPagination');

        /** if available, load from cache **/
        if (empty($this->cache[$store])) {
        } else {
            return $this->cache[$store];
        }

        /** pagination object **/
        $limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
//        $page = new JPagination($this->getTotal(), $this->getStart(), $limit);
        $page = '';
        /** load cache **/
        $this->cache[$store] = $page;

        /** return from cache **/

        return $this->cache[$store];
    }

    /**
     * getTotal
     *
     * Method to get the total number of items for the data set.
     *
     * @return integer
     * @since    1.0
     */
    public function getTotal()
    {
        /** cache **/
        $store = $this->getStoreId('getTotal');
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        /** get total of items returned from the last query **/
        $this->db->setQuery($this->queryStatement);
        $this->db->query();

        $total = (int) $this->db->getNumRows();

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        /** load cache **/
        $this->cache[$store] = $total;

        /** return from cache **/

        return $this->cache[$store];
    }

    /**
     * getStart
     *
     * Method to get the starting number of items for the data set.
     *
     * @return integer
     * @since    1.0
     */
    public function getStart()
    {
        /** cache **/
        $store = $this->getStoreId('getStart');
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        /** get list object **/
        $start = $this->getState('list.start');
        $limit = $this->getState('list.limit');
        $total = $this->getTotal();
        if ($start > $total - $limit) {
            $start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
        }

        /** load cache **/
        $this->cache[$store] = $start;

        /** return from cache **/

        return $this->cache[$store];
    }
}
