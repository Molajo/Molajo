<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Categories Helper
 *
 * @package     Molajo
 * @subpackage  Categories Helper
 * @since       1.0
 */
class MolajoCategoriesHelper extends MolajoCategories
{
    public function __construct($options = array())
    {
        $options['table'] = JRequest::getCmd('ComponentTable');
        $options['extension'] = JRequest::getCmd('option');
        parent::__construct($options);
    }
}