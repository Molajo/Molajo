<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Extension\Includer;

defined('MOLAJO') or die;

/**
 * Tag
 *
 * @package     Molajo
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
    public function process($attributes = array())
    {
        return '';
    }

    /**
     * Renders multiple modules script and returns the results as a string
     *
     * @param string $position The position of the modules to render
     * @param array  $params   Associative array of values
     * @param string $content  Module content
     *
     * @return string The output of the script
     *
     * @since   11.1
     */
    public function render($position, $params = array(), $content = null)
    {
        $renderer = $this->_doc->loadIncluder('module');
        $buffer = '';

        foreach (JModuleHelper::getModules($position) as $mod) {
            $buffer .= $renderer->render($mod, $params, $content);
        }

        return $buffer;
    }
}
