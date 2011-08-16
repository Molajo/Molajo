<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** JS **/
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js', false, true);

/** list variables **/
$this->saveOrder = $this->state->get('list.ordering');

/** generate output **/
include dirname(__FILE__) . '/form/form_begin.php';
include dirname(__FILE__) . '/driver_form_filters.php';
include dirname(__FILE__) . '/form/table_begin.php';
include dirname(__FILE__) . '/driver_table_head.php';