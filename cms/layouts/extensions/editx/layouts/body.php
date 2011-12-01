<?php
/**
 * @version     $id: body.php
 * @package     Molajo
 * @subpackage  Edit Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; 

/** mt **/
MolajoHTML::_('behavior.framework', true);
MolajoHTML::_('behavior.tooltip');
MolajoHTML::_('behavior.formvalidation');
MolajoHTML::_('behavior.keepalive');
MolajoHTML::_('script','system/multiselect.js', false, true);

/** form begin **/
include dirname(__FILE__) . '/form/' . 'form_begin.php';

/** form validation **/
// js problems include dirname(__FILE__).'/form/'.'form_validation.php');

/**
 *  LEFT COLUMN
 */
/** begin **/
include dirname(__FILE__) . '/form/' . 'left_column_begin.php';

/** top **/
$this->tempSection = 'config_manager_editor_left_top_column';
include dirname(__FILE__) . '/driver_section.php';

/** primary **/
$this->tempSection = 'config_manager_editor_primary_column';
include dirname(__FILE__) . '/driver_section.php';

/** bottom **/
$this->tempSection = 'config_manager_editor_left_bottom_column';
include dirname(__FILE__) . '/driver_section.php';

/** end **/
include dirname(__FILE__) . '/form/' . 'left_column_end.php';

/**
 *  RIGHT COLUMN
 */

/** begin **/
include dirname(__FILE__) . '/form/' . 'right_column_begin.php';
/** publishing **/
include dirname(__FILE__) . '/form/' . 'right_column_publishing_top.php';
$this->tempSection = 'config_manager_editor_right_publishing_column';
include dirname(__FILE__) . '/driver_section.php';
include dirname(__FILE__) . '/form/' . 'right_column_publishing_bottom.php';
/** attribs **/
include dirname(__FILE__) . '/driver_fieldset.php';
/** right column end **/
include dirname(__FILE__) . '/form/' . 'right_column_end.php';

/**
 *  FULL WIDTH BOTTOM
 */

/** begin **/
include dirname(__FILE__) . '/form/' . 'bottom_begin.php';
/** acl **/
include dirname(__FILE__) . '/form/' . 'bottom_acl.php';
/** hidden **/
include dirname(__FILE__) . '/form/' . 'bottom_hidden.php';
/** form end **/
include dirname(__FILE__) . '/form/' . 'form_end.php';
/** end **/
include dirname(__FILE__) . '/form/' . 'bottom_end.php';