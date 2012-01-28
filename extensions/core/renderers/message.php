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
     * _getAttributes
     *
     *  From <include:message attr=1 attr=2 etc />
     */
    protected function _getAttributes()
    {
        parent::_getAttributes();

        /** Model */
        $this->mvc->set('mvc_model', 'MolajoModelMessages');
    }
}
