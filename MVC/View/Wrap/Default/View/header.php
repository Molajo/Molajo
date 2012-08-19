<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 1
	&& (Services::Registry()->get('Parameters', 'wrap_view_title') == 'Article'
		|| Services::Registry()->get('Parameters', 'wrap_view_title') == 'Aside'
		|| Services::Registry()->get('Parameters', 'wrap_view_title') == 'Footer'
		|| Services::Registry()->get('Parameters', 'wrap_view_title') == 'Header'
		|| Services::Registry()->get('Parameters', 'wrap_view_title') == 'Hgroup'
		|| Services::Registry()->get('Parameters', 'wrap_view_title') == 'Nav'
		|| Services::Registry()->get('Parameters', 'wrap_view_title') == 'Section')
):
	$headerType = Services::Registry()->get('Parameters', 'wrap_view_title'); else :
	$headerType = 'div';
endif;
$headerType = strtolower($headerType);

$headerId = trim(Services::Registry()->get('Parameters', 'wrap_view_css_id', ''));
if ($headerId == '') :
else :
	$headerId = ' id="' . $headerId . '"';
endif;

$headerClass = trim(Services::Registry()->get('Parameters', 'wrap_view_css_class', ''));
if ($headerClass == '') :
else :
	$headerClass = ' class="' . $headerClass . '"';
endif;

$headerRole = trim(Services::Registry()->get('Parameters', 'wrap_view_css_role', ''));
if ($headerRole == '') :
else :
	$headerRole = ' role="' . $headerRole . '"';
endif;

$headerProperty = trim(Services::Registry()->get('Parameters', 'wrap_view_css_property', ''));
if ($headerProperty == '') :
else :
	$headerProperty = ' property="' . $headerProperty . '"';
endif;
?>
<<?php echo trim($headerType . $headerId . $headerClass . $headerRole . $headerProperty); ?>>
<?php
$headingLevel = Services::Registry()->get('Parameters', 'wrap_view_header_level', 3);
if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 1):
	if (Services::Registry()->get('Parameters', 'wrap_view_show_title', false) === true
		&& Services::Registry()->get('Parameters', 'wrap_view_show_subtitle', false) === true
	) : ?>
    <hgroup>
<?php endif;
endif;

if (Services::Registry()->get('Parameters', 'wrap_view_show_title', false) === true) :  ?>
	<h<?php echo $headingLevel; ?>>
		<?php echo $this->row->title; ?>
	</h<?php echo $headingLevel++; ?>>
	<?php
endif;

if (Services::Registry()->get('Parameters', 'wrap_view_show_subtitle', false) === true) :  ?>
	<h<?php echo $headingLevel; ?>>
		<?php echo $this->row->subtitle; ?>
	</h<?php echo $headingLevel++; ?>>
	<?php
endif;

if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 1) :
	if (Services::Registry()->get('Parameters', 'wrap_view_show_title', false) === true
		&& Services::Registry()->get('Parameters', 'wrap_view_show_subtitle', false) === true
	) : ?>
    </hgroup>
<?php endif;
endif;
