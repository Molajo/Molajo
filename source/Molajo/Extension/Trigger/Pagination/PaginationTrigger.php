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
     * After reading, calculate pagination data
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
		if ($start > $total - $limit) {
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}
		 list_model_use_pagination
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
}
