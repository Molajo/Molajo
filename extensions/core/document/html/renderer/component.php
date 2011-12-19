<?php
/**
 * @package     Molajo
 * @subpackage  Document
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Component renderer
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocumentRendererComponent extends MolajoDocumentRenderer
{
    /**
     * Renders a component script and returns the results as a string
     *
     * @param   string  $component  The name of the component to render
     * @param   array   $parameters     Associative array of values
     * @param   string  $content    Content script
     *
     * @return  string  The output of the script
     *
     * @since   1.0
     */
    public function render($component = null, $parameters = array(), $content = null)
    {
        return $content;
    }
}
