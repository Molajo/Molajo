<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; 
$defaultView = $this->state->get('request.DefaultView');
if ($this->render['column_name'] == 'title' && $this->params->def('config_manager_grid_column_display_alias', 1)) {
    $printAlias = '<br />'.$this->escape($this->row->alias);
} else {
    $printAlias = '';
}
?>

<td>
<?php if ($this->row->checked_out) : ?>
        <?php echo MolajoHTML::_('jgrid.checkedout', $this->row->rowCount, $this->row->editor, $this->row->checked_out_time, $defaultView.'.', $this->row->canCheckin); ?>
<?php endif; ?>
<?php if ($this->row->canEdit) : ?>
        <a href="<?php echo MolajoRoute::_('index.php?option='.$this->request['option'].'&task=edit&id='.$this->row->id);?>">
            <?php echo $this->row->title; ?>
        </a>
<?php else : ?>
        <?php echo $this->row->title; ?>
<?php endif; ?>

<?php if ($this->params->def('config_manager_grid_column_display_alias', 1))  : ?>
        <p class="smallsub"><?php echo MolajoText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($this->row->alias));?></p>
<?php endif; ?>
</td>