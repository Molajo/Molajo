<?php
/**
 * Read Controller Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

/**
 * Read Controller Interface
 *
 * @package    Controller
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface ReadControllerInterface
{
    /**
     * Method to get data from model
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function getData();
}
