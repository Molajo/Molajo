<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * echo Services::Language()->translate('No inpution')
 */
defined('MOLAJO') or die;
$tooltip_css_class = 'has-tip'; ?>
<fieldset class="radio">
    <legend class="radio">
		<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>">
			<?php echo $this->row->label; ?>
		</span>
	</legend>
	<ol>
