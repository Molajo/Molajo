<?php
/**
 * @package     Molajo
 * @subpackage  Head
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<?php if ($this->row->type == 'links'): ?>
<?php elseif ($this->row->type == 'metadata'): ?>
<?php elseif ($this->row->type == 'stylesheets'): ?>
    <link rel="stylesheet" href="/templates/system/css/general.css" <?php if ($this->parameters->get('html5', true) === false): ?>type="text/css"<?php endif; ?> />
<?php elseif ($this->row->type == 'styles'): ?>
<?php elseif ($this->row->type == 'scripts'): ?>
<?php elseif ($this->row->type == 'script'): ?>
<?php endif; ?>