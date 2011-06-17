<?php
/**
 * @version     categories.php
 * @package     Molajo
 * @subpackage  Category Tree
 *
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Molajo Component Category Tree
 *
 * @static
 * @package	Molajo
 * @since 1.6
 */
class MolajoCategoriesHelper extends MolajoCategories
{
    public function __construct($options = array())
    {
        $options['table'] = JRequest::getCmd('component_table');
        $options['extension'] = JRequest::getCmd('option');
        parent::__construct($options);
    }
}