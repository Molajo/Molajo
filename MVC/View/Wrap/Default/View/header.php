<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;

if ((int)Services::Registry()->get('Parameters', 'criteria_html5', 1) == 1
	&& (Services::Registry()->get('WrapView', 'title') == 'article'
		|| Services::Registry()->get('WrapView', 'title') == 'aside'
		|| Services::Registry()->get('WrapView', 'title') == 'footer'
		|| Services::Registry()->get('WrapView', 'title') == 'header'
		|| Services::Registry()->get('WrapView', 'title') == 'hgroup'
		|| Services::Registry()->get('WrapView', 'title') == 'nav'
		|| Services::Registry()->get('WrapView', 'title') == 'section')
):
	$headerType = Services::Registry()->get('WrapView', 'title');
else :
	$headerType = 'div';
endif;

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
?>
<<?php echo trim($headerType . $headerId . $headerClass . $headerRole); ?>>
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

if ((int)Services::Registry()->get('Parameters', 'Configuration', 'html5', 1) == 1) :
	if (Services::Registry()->get('Parameters', 'wrap_view_show_title', false) === true
		&& Services::Registry()->get('Parameters', 'wrap_view_show_subtitle', false) === true
	) : ?>
	</hgroup>
<?php endif;
endif;
