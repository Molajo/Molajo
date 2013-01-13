<?php
/**
 * Formselect Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;
$tooltip_css_class = 'has-tip'; ?>
<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>">
<label for="<?php echo $this->row->field_id; ?>"><?php echo $this->row->label; ?></label></span>
	<select id="<?php echo $this->row->field_id; ?>"
            name="<?php echo $this->row->name; ?>"<?php echo $this->row->multiple; ?> <?php echo $this->row->required; ?> <?php echo $this->row->disabled; ?> <?php echo $this->row->size; ?>>
<?php if ($this->row->multiple == '') { ?>
    <option value=""><?php echo Services::Language()->translate(
        'SELECT_' . strtoupper($this->row->datalist)
    ); ?></option>
    <?php } else { ?>
    <option value=""><?php echo Services::Language()->translate('No selection'); ?></option>
    <?php if ($this->row->selected == '#') {

        $selected = ' selected';
    } else {
        $selected = '';
    }?>
    <option<?php echo $selected; ?> value="#"><?php echo Services::Language()->translate('Select all'); ?></option>
<?php }
