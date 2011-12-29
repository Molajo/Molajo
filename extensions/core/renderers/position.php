<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoPosition
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoPositionRenderer
{
    /**
     *  Position
     *
     * @var array
     * @since 1.0
     */
    protected $position = null;

    /**
     *  Parameters
     *
     * @var array
     * @since 1.0
     */
    protected $parameters = null;

    /**
     *  Config
     *
     * @var array
     * @since 1.0
     */
    protected $config = null;

    public function __construct($position, $parameters = array(), $config = null)
    {
        $this->position = $position;
        $this->parameters = $parameters;
        $this->config = $config;
    }

    /**
     * Renders multiple modules script and returns the results as a string
     *
     * @param   string  $name    The position of the modules to render
     * @param   array   $parameters  Associative array of values
     *
     * @return  string  The output of the script
     *
     * @since   1.0
     */
    public function render()
    {
        $class = 'MolajoModuleRenderer';
        $renderer = new $class ($this->parameters, $this->config);
        $buffer = '';

        foreach (MolajoModuleRenderer::getModules($this->position) as $module_object) {
            $buffer .= $renderer->render($module_object);
        }
        return $buffer;
    }
}
