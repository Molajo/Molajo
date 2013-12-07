<?php
/**
 * Fullname Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Fullname;

use Molajo\Plugin\ReadEventPlugin;
use CommonApi\Event\ReadInterface;

/**
 * Fullname Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class FullnamePlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Adds full_name to recordset containing first_name and last_name
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->query_results->first_name)
            && isset($this->query_results->last_name)
        ) {
        } else {
            return $this;
        }

        if (isset($this->query_results->full_name)) {
            return $this;
        }

        $this->setField(null, 'full_name', $this->query_results->first_name . ' ' . $this->query_results->last_name);

        return $this;
    }
}
