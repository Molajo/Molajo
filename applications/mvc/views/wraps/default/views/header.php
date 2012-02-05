<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ((int)Molajo::App()->get('html5', 1) == 1
    && ($this->mvc->get('wrap_view_name') == 'article'
        || $this->mvc->get('wrap_view_name') == 'aside'
        || $this->mvc->get('wrap_view_name') == 'footer'
        || $this->mvc->get('wrap_view_name') == 'header'
        || $this->mvc->get('wrap_view_name') == 'hgroup'
        || $this->mvc->get('wrap_view_name') == 'nav'
        || $this->mvc->get('wrap_view_name') == 'section') ):
    $headerType = $this->mvc->get('wrap_view_name');
else :
    $headerType = 'div';
endif;

$headerId = trim($this->parameters->get('wrap_view_css_id', ''));
if ($headerId == '') :
else :
    $headerId = ' id="' . $headerId . '"';
endif;

$headerClass = trim($this->parameters->get('wrap_view_css_class', ''));
if ($headerClass == '') :
else :
    $headerClass = ' class="' . $headerClass . '"';
endif;
?>
<<?php echo trim($headerType.$headerId.$headerClass);?>>
<?php
$headingLevel = $this->parameters->get('wrap_view_header_level', 3);
if ((int)Molajo::App()->get('html5', 1) == 1):
    if ($this->parameters->get('wrap_view_show_title', false) === true
        && $this->parameters->get('wrap_view_show_subtitle', false) === true
    ) : ?>
	<hgroup>
<?php endif;
endif;

if ($this->parameters->get('wrap_view_show_title', false) === true) :  ?>
    <h<?php echo $headingLevel; ?>>
        <?php echo $this->row->title; ?>
    </h<?php echo $headingLevel++; ?>>
    <?php
    endif;

if ($this->parameters->get('wrap_view_show_subtitle', false) === true) :  ?>
    <h<?php echo $headingLevel; ?>>
        <?php echo $this->row->subtitle; ?>
    </h<?php echo $headingLevel++; ?>>
    <?php
    endif;

if ((int)Molajo::App()->get('html5', 1) == 1) :
    if ($this->parameters->get('wrap_view_show_title', false) === true
        && $this->parameters->get('wrap_view_show_subtitle', false) === true
    ) : ?>
	</hgroup>
<?php endif;
endif;
