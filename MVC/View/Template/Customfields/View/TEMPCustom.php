<?php
/**
 * Customfields Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;

if ($this->row->new_fieldset == '1') {
    if ($this->row->page_first_row == 'first') {
    } elseif ((int)$this->row->first_following === 1) {
        ?>
    </ol>
    </fieldset>
    <?php } else { ?>
    </tbody>
    </table>
    </fieldset>
    <?php } ?>
<?php if ($this->row->fieldset_title == 'Create') { ?>
<fieldset class="two-up">
<legend><?php echo $this->row->fieldset_title; ?></legend>
        <?php if (Services::Registry()->get('parameters', 'application_help') == 1) { ?>
            <p><?php echo $this->row->fieldset_description; ?></p>
            <?php } ?>
    <ol>
    <?php } else { ?>
<fieldset>
<legend><?php echo $this->row->fieldset_title; ?></legend>
        <?php if (Services::Registry()->get('parameters', 'application_help') == 1) { ?>
            <p><?php echo $this->row->fieldset_description; ?></p>
            <?php } ?>
<table class="responsive">
    <thead>
    <tr>
        <th class="name"><?php echo Services::Language()->translate('Name'); ?></th>
        <th class="type"><?php echo Services::Language()->translate('Type'); ?></th>
        <th class="required"><?php echo Services::Language()->translate('Required'); ?></th>
        <th class="datalist"><?php echo Services::Language()->translate('List'); ?></th>
        <th class="hidden"><?php echo Services::Language()->translate('Hidden'); ?></th>
        <th width="1%">
            <?php echo Services::Language()->translate('Delete'); ?>
        </th>
    </tr>
    </thead>
    <tbody>
<?php
    }
} ?>

<?php if ($this->row->tab_fieldset_title == 'Create') { ?>
    <li>
        <?php echo $this->row->view;
        die;
        ?>
        <include:template name=<?php echo $this->row->view; ?> parameter_np=<?php echo $this->row->name; ?>/>
    </li>

    <?php } else { ?>
    <tr>
        <td class="name"><?php echo Services::Language()->translate($this->row->label); ?></td>
        <td class="type"><?php echo $this->row->type; ?></td>
        <td class="required"><?php $this->row->required; ?></td>
        <td class="datalist"><?php echo $this->row->datalist; ?></td>
        <td class="hidden"><?php echo $this->row->hidden; ?></td>
        <td><a href="#" class="small button alert radius"><?php echo Services::Language()->translate(
            'Delete',
            'Delete'
        ); ?></a></td>
    </tr>
<?php }
