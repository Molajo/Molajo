<?php
/**
 * Fields Base
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fields;

use Molajo\Plugins\DisplayEventPlugin;

/**
 * Fields Plugin Base
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Base extends DisplayEventPlugin
{
    /**
     * Text Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $extended_field_array = array();

    /**
     * Text Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $field_array = array();

    /**
     * Text Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $all_fields_array = array();
}
