<?php
/**
 * @package     Molajo
 * @subpackage  Headings
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$headinglevel = $this->parameters->get('header_level', 1);

if ($this->parameters->get('html5', true) === true) :
    if ($this->parameters->get('showtitle', false) === true
        && $this->parameters->get('showsubtitle', false) === true
    ) : ?>
	<hgroup>
<?php endif;
endif;

if ($this->parameters->get('showtitle', false) === true) :  ?>
    <h<?php echo $headinglevel; ?>>
        <?php echo $this->escape($this->row->title); ?>
    </h<?php echo $headinglevel++; ?>>
    <?php
    endif;

if ($this->parameters->get('showsubtitle', false) === true) :  ?>
    <h<?php echo $headinglevel; ?>>
        <?php echo $this->escape($this->row->subtitle); ?>
    </h<?php echo $headinglevel++; ?>>
    <?php
    endif;

if ($this->parameters->get('html5', true) === true) :
    if ($this->parameters->get('showtitle', false) === true
        && $this->parameters->get('showsubtitle', false) === true
    ) : ?>
	</hgroup>
<?php endif;
endif;