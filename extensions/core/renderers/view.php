<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoView
 *
 * @package     Molajo
 * @subpackage  View
 * @since       1.0
 */
class MolajoViewRenderer
{
    /**
     *  View
     *
     * @var array
     * @since 1.0
     */
    protected $view = null;

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

    public function __construct($view, $parameters = array(), $config = null)
    {
        $this->view = $view;
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
        return 'Rendered output from View';
    }
}
