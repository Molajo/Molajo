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
 * MolajoDocument Modules renderer
 *
 * @package    Molajo
 * @subpackage  Document
 * @since       1.0
 */
class MolajoDocumentRendererModules extends MolajoDocumentRenderer
{
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
    public function render($position, $parameters = array(), $content = null)
    {
        $renderer = $this->_doc->loadRenderer('module');
        $buffer = '';

        foreach (MolajoModule::getModules($position) as $mod) {
            $buffer .= $renderer->render($mod, $parameters, $content);
        }
        return $buffer;
    }
}