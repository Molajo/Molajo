<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ($this->parameters->def('html5', true) === true
    && ($this->parameters->def('wrap', '') == 'article'
        || $this->parameters->def('wrap', '') == 'aside'
        || $this->parameters->def('wrap', '') == 'footer'
        || $this->parameters->def('wrap', '') == 'header'
        || $this->parameters->def('wrap', '') == 'hgroup'
        || $this->parameters->def('wrap', '') == 'nav'
        || $this->parameters->def('wrap', '') == 'section') ):
    $headerType = $this->parameters->get('wrap');
else :
    $headerType = 'div';
endif;

$headerId = trim($this->parameters->get('wrap_id', ''));
if ($headerId == '') :
else :
    $headerId = ' id="' . $headerId . '"';
endif;

$headerClass = trim($this->parameters->get('wrap_class', ''));
if ($headerClass == '') :
else :
    $headerClass = ' class="' . $headerClass . '"';
endif;
?>
<<?php echo trim($headerType.$headerId.$headerClass);?>>
<?php
$headingLevel = $this->parameters->get('wrap_header_level', 3);

if ($this->parameters->get('html5', true) === true) :
    if ($this->parameters->get('wrap_show_title', false) === true
        && $this->parameters->get('wrap_show_subtitle', false) === true
    ) : ?>
	<hgroup>
<?php endif;
endif;

if ($this->parameters->get('wrap_show_title', false) === true) :  ?>
    <h<?php echo $headingLevel; ?>>
        <?php echo $this->row->title; ?>
    </h<?php echo $headingLevel++; ?>>
    <?php
    endif;

if ($this->parameters->get('wrap_show_subtitle', false) === true) :  ?>
    <h<?php echo $headingLevel; ?>>
        <?php echo $this->row->subtitle; ?>
    </h<?php echo $headingLevel++; ?>>
    <?php
    endif;

if ($this->parameters->get('html5', true) === true) :
    if ($this->parameters->get('wrap_show_title', false) === true
        && $this->parameters->get('wrap_show_subtitle', false) === true
    ) : ?>
	</hgroup>
<?php endif;
endif;