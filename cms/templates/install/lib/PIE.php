<?php
/**
 * @package     Molajo
 * @subpackage  Install
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/*
This file is a wrapper, for use in PHP environments, which serves PIE.htc using the
correct content-type, so that IE will recognize it as a behavior.  Simply specify the
behavior property to fetch this .php file instead of the .htc directly:

.myElement {
    [ ...css3 properties... ]
    behavior: url(PIE.php);
}

This is only necessary when the web server is not configured to serve .htc files with
the text/x-component content-type, and cannot easily be configured to do so (as is the
case with some shared hosting providers).
*/

header( 'Content-type: text/x-component' );
include dirname(__FILE__) . 'PIE.htc';