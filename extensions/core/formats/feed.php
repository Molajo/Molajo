<?php
/**
 * @package     Molajo
 * @subpackage  Feed
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Render Feed Format
 *
 * @package     Molajo
 * @subpackage  Feed
 * @since       1.0
 */
class MolajoFeedFormat
{
    /**
     *  Config
     *
     * @var array
     * @since 1.0
     */
    protected $config = null;

    /**
     *  Message
     *
     * @var string
     * @since 1.0
     */
    protected $message = null;

    /**
     *  Template folder name
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Page include file
     *
     * @var string
     * @since 1.0
     */
    protected $page = null;

    /**
     *  View include file
     *
     * @var string
     * @since 1.0
     */
    protected $view = null;

    /**
     *  Wrap for View
     *
     * @var string
     * @since 1.0
     */
    protected $wrap = null;

    /**
     *  Template Parameters
     *
     * @var string
     * @since 1.0
     */
    protected $parameters = null;

    /**
     * Array of buffered output
     *
     * @var    mixed (depends on the renderer)
     */
    protected $_buffer = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $request
     * @param   null    $page
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($config = array())
    {
        //        echo '<pre>';
        //        var_dump($config);
        //        '</pre>';

        /** set class properties */
        $this->config = $config;
        $this->message = $config->message;
        $this->template = $config->template;
        $this->page = $config->page;
        $this->view = $config->view;
        $this->wrap = $config->wrap;

        /** Request */
        $this->_renderTemplate();
    }

    /**
     * Render the Template
     *
     * @return  object
     * @since  1.0
     */
    protected function _renderTemplate()
    {
    }
}