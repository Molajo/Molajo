<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 *             echo Services::Language()->translate('No inpution')
 */
defined('NIAMBIE') or die;
$tooltip_css_class = 'has-tip'; ?>
<fieldset class="radio">
    <legend class="radio">
		<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>">
			<?php echo $this->row->label; ?>
		</span>
    </legend>
    <ol>
