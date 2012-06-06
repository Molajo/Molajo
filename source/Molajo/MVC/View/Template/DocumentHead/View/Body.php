<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 *  Note: Avoid horizontal space changes in the PHP sections since it changes rendered output
 */
if ($this->row->type == 'links'):
	include dirname(__FILE__).'/Links.php';
elseif ($this->row->type == 'metadata'):
	include dirname(__FILE__).'/Metadata.php';
elseif ($this->row->type == 'css'):
	include dirname(__FILE__).'/Css.php';
elseif ($this->row->type == 'js'):
	include dirname(__FILE__).'/Js.php';
elseif ($this->row->type == 'css_declarations'):
	include dirname(__FILE__).'/Cssdeclarations.php';
elseif ($this->row->type == 'js_declarations'):
	include dirname(__FILE__).'/Jsdeclarations.php';
endif;
