<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die; ?>

<div class="configuration-header">
    <header>
        <h3><?php echo $this->row->page_title_extended; ?></h3>
        <?php if ($this->parameters['application_help'] == 1) { ?>
        <h6><?php echo $this->row->page_description; ?></h6>
        <?php } ?>
    </header>
    <section>
        <ul class="inline-list right">
            <li><a href="#"><strong><?php echo Services::Language()->translate('Reset', 'Reset'); ?></strong></a></li>
            <li><a href="#" class="button success radius"><?php echo Services::Language()->translate(
                'Save',
                'Save'
            ); ?></a></li>
        </ul>
    </section>
</div>

<include:template name=<?php echo $this->row->view; ?> parameter_np=<?php echo $this->row->name; ?>/>

    <div class="configuration-body">

        <table class="responsive">
            <thead>
            <tr>
                <th class="create-type"><?php echo Services::Language()->translate('Criteria'); ?></th>
                <th class="create-value"><?php echo Services::Language()->translate('Value'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="create-type"><?php echo Services::Language()->translate('Name'); ?></td>
                <td class="create-value">
                    <input id="custom_field_name" type="text" placeholder="Customfield Name"/>
                    <include:template name=Forminput id="custom_field_name" type="text"/>
                </td>
            </tr>
            <tr>
                <td class="create-type">
                    <include:template name=formselect id="custom_field_type" datalist="datatype"/>
                </td>
            </tr>
            <tr>
                <td class="create-type"><?php echo Services::Language()->translate('Required'); ?></td>
                <td class="create-value"></td>
            </tr>
            <tr>
                <td class="create-type"><?php echo Services::Language()->translate('List'); ?></td>
                <td class="create-value"></td>
            </tr>
            <tr>
                <td class="create-type"><?php echo Services::Language()->translate('Hidden'); ?></td>
                <td class="create-value"></td>
            </tr>
            </tbody>
        </table>

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
