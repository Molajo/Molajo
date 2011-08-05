<?php
/**
 * @package     Molajo
 * @subpackage  List Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<div class="moduletable<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
    <?php if ($module->showtitle != 0) : ?>
        <h3><?php echo $module->title; ?></h3>
    <?php endif; ?>