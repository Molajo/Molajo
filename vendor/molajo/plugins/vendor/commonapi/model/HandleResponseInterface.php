<?php
/**
 * Handle Response Interface
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Model;

/**
 * Handle Response Interface
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
interface HandleResponseInterface
{
    /**
     * Get Processed Value
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function getFieldValue();

    /**
     * Did the data value change as a result of processing?
     *
     * @return  boolean
     * @since   1.0.0
     */
    public function getChangeIndicator();
}
