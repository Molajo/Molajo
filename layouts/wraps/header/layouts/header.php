<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

defined('MOLAJO') or die;
if ($this->params->get('html5', true) === true) : ?>
<footer class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php else: ?>
<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php endif;
if ($this->params->get('showtitle', true)) : ?>
   <h<?php echo $params->get('header_level', '1'); ?>>
       <?php echo $this->escape($this->row->title);
    echo '</h'.$headerLevel; ?>>
endif; ?>


if ($this->params->get('html5', true) === true) : ?>
<header class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php else: ?>
<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
<?php endif;

if ($this->params->get('html5', true) === true) :
    if ($this->params->get('showtitle', true)
        && $this->params->get('showsubtitle', true)) : ?>
	<hgroup>
<?php endif;
endif;

if ($this->params->get('showtitle', true)) : ?>
    <h<?php echo $params->get('header_level', '1'); ?> class="<?php echo $params->get('header_class'); ?>">
        <?php echo $this->escape($this->row->title); ?>
    </h<?php echo $params->get('header_level', '1'); ?>>
<?php
endif;

if ($this->params->get('showsubtitle', true)) : ?>
    <h<?php echo $params->get('header_level', '1') + 1; ?> class="<?php echo $params->get('header_class'); ?>">
        <?php echo $this->escape($this->row->subtitle); ?>
    </h<?php echo $params->get('header_level', '1') + 1; ?>>
<?php
endif;

if ($this->params->get('html5', true) === true) :
    if ($this->params->get('showtitle', true)
        && $this->params->get('showsubtitle', true)) : ?>
	</hgroup>
<?php endif;
endif;

if ($this->params->get('html5', true) === true) : ?>
</header>
<?php else: ?>
</div>
<?php endif;