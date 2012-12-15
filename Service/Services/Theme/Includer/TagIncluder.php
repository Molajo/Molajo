<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services\Theme\Includer;

defined('NIAMBIE') or die;

/**
 * Tag
 *
 * @package     Niambie
 * @subpackage  Includer
 * @since       1.0
 */
Class TagIncluder extends Includer
{
    /**
     * process
     *
     * @return mixed
     * @since   1.0
     */
    public function __construct($include_name = null, $include_type = null)
    {
        return 'still need to do Tag Includer';
    }

    public function render($tag)
    {

        foreach (x::y($tag) as $item) {
            $buffer .= $includer->etc($thing;
        }

        return $buffer;
    }
}
