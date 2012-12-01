<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die; ?>
<tr>
    <td class="name"><?php echo Services::Language()->translate($this->row->label); ?></td>
    <td class="type"><?php echo $this->row->type; ?></td>
    <td class="required"><?php $this->row->required; ?></td>
    <td class="datalist"><?php echo $this->row->datalist; ?></td>
    <td class="hidden"><?php echo $this->row->hidden; ?></td>
    <td><a href="#" class="small button alert radius"><?php echo Services::Language()->translate('Delete', 'Delete'); ?></a></td>
</tr>
