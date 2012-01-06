<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ($this->parameters->get('html5', true) === true
    && isset($headerType)
        && ($headerType == 'article'
            || $headerType == 'aside'
            || $headerType == 'footer'
            || $headerType == 'header'
            || $headerType == 'hgroup'
            || $headerType == 'nav'
            || $headerType == 'section') ):
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