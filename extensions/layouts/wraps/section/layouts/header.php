<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C)  2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ($this->params->get('html5', true) === true) :
    $headertype = 'section';
else :
    $headertype = 'div';
endif;
include MOLAJO_EXTENSION_LAYOUT_COMMON.'/headertype.php';
include MOLAJO_EXTENSION_LAYOUT_COMMON.'/headings.php';