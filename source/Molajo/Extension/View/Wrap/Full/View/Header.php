<?php
/**
 * @package   Molajo
 * @subpackage  Wrap
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ((int)Services::Registry()->get('Configuration', 'html5', 1) == 1
	&& ($this->task->get('wrap_view_name') == 'article'
		|| $this->task->get('wrap_view_name') == 'aside'
		|| $this->task->get('wrap_view_name') == 'footer'
		|| $this->task->get('wrap_view_name') == 'header'
		|| $this->task->get('wrap_view_name') == 'hgroup'
		|| $this->task->get('wrap_view_name') == 'nav'
		|| $this->task->get('wrap_view_name') == 'section')
):
	$headerType = $this->task->get('wrap_view_name');
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
?>
<<?php echo trim($headerType . $headerId . $headerClass); ?>>
<?php
$headingLevel = Services::Registry()->get('Parameters', 'wrap_view_header_level', 3);
if ((int)Services::Registry()->get('Configuration', 'html5', 1) == 1):
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

if ((int)Services::Registry()->get('Configuration', 'html5', 1) == 1) :
	if (Services::Registry()->get('Parameters', 'wrap_view_show_title', false) === true
		&& Services::Registry()->get('Parameters', 'wrap_view_show_subtitle', false) === true
	) : ?>
	</hgroup>
<?php endif;
endif;
