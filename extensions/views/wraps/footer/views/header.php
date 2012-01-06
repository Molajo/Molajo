<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C)  2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (MolajoController::getApplication()->get('html5', true) === true) :
    $headertype = 'footer';
else :
    $headertype = 'div';
endif;
include MOLAJO_EXTENSIONS_VIEWS . '/common/headertype.php';
include MOLAJO_EXTENSIONS_VIEWS . '/common/headings.php';