<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Message
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoRendererMessage extends MolajoRenderer
{

    /**
     * _setParameters
     *
     *  From <include:message attr=1 attr=2 etc />
     */
    protected function _setParameters()
    {
        parent::_setParameters();

        /** Model */
        $this->_request->set('mvc_model', 'MolajoModelMessages');
    }
}
