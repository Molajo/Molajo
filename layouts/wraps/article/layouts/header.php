<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
if ($this->params->get('html5', true) === true) : ?>
<article class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php else: ?>
<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php endif;
if ($this->params->get('showtitle', true)) : ?>
  <h<?php echo $params->get('header_level', '1'); ?>>
      <?php echo $this->escape($this->row->title);
  </h<?php echo $params->get('header_level', 1); ?>>
endif; ?>