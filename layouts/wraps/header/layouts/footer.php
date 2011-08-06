<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
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