<?php
/**
 * Datalist Interface
 *
 * @package    CommonApi
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace CommonApi\Controller;

/**
 * Datalist Interface
 *
 * @package    CommonApi
 * @license    MIT
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface DatalistInterface
{
    /**
     * Get Datalist
     *
     * @param   string $list
     * @param   array  $options
     *
     * @return  array
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getDatalist($list, array $options = array());
}
