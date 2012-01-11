<?php
/**
 * @package     Molajo
 * @subpackage  Head
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
if ($this->row->type == 'metadata'): ?>
<?php elseif ($this->row->type == 'links'): ?>
<link <?php echo $this->row->relation; ?>="<?php echo $this->row->relation_type; ?>" href="<?php echo $this->row->url; ?>" <?php echo $this->row->attributes; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes.' ';?><?php endif; ?>/>
<?php elseif ($this->row->type == 'stylesheet_links'): ?>
    <link rel="stylesheet" href="<?php echo $this->row->url; ?>" <?php if ($this->parameters->get('html5', true) === false): ?>type="<?php echo $this->row->mimetype; ?>" <?php endif; ?><?php if ($this->row->media != null): ?>type="<?php echo $this->row->media; ?>" <?php endif; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes.' ';?><?php endif; ?>/>
<?php elseif ($this->row->type == 'styles'): ?>
<?php elseif ($this->row->type == 'javascript_links'): ?>
    <script src="<?php echo $this->row->url; ?>" <?php if ($this->parameters->get('html5', true) === false): ?> type="<?php echo $this->row->mimetype; ?>" <?php endif; ?>/></script>
<?php elseif ($this->row->type == 'script'): ?>
<?php endif; ?>