<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ((int)Services::Registry()->get('Configuration', 'html5', 1) == 1
    && ($this->get('wrap_view_name') == 'article'
        || $this->get('wrap_view_name') == 'aside'
        || $this->get('wrap_view_name') == 'footer'
        || $this->get('wrap_view_name') == 'header'
        || $this->get('wrap_view_name') == 'hgroup'
        || $this->get('wrap_view_name') == 'nav'
        || $this->get('wrap_view_name') == 'section')
):
    $headerType = $this->get('wrap_view_name');
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

$headerRole = trim($this->parameters->get('wrap_view_css_role', ''));
if ($headerRole == '') :
else :
    $headerRole = ' role="' . $headerRole . '"';
endif;
?>
<<?php echo trim($headerType . $headerId . $headerClass . $headerRole); ?>>
<?php
$headingLevel = $this->parameters->get('wrap_view_header_level', 3);
if ((int)Services::Registry()->get('Configuration', 'html5', 1) == 1):
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

if ((int)Services::Registry()->get('Configuration', 'html5', 1) == 1) :
    if ($this->parameters->get('wrap_view_show_title', false) === true
        && $this->parameters->get('wrap_view_show_subtitle', false) === true
    ) : ?>
	</hgroup>
<?php endif;
endif;
