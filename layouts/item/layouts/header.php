<?php
/**
 * @version     $id: driver.php
 * @package     Molajo
 * @subpackage  Item Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<div class="item<?php echo $this->params->get('page_class_suffix', ''); ?>">

<div class="item-header<?php echo $this->params->get('page_class_suffix', ''); ?>">

<h2><?php echo $this->row->title; ?></h2>

<?php if ($this->row->canEdit) : ?>
<ul class="actions">
    <li class="edit-icon">
        <?php //echo JHtml::_('icon.edit', $this->row, $this->state); ?>
    </li>
</ul>
<?php endif; ?>

<ul class="subheading">
    <dd class="published">
        <?php echo JText::_('MOLAJO_PUBLISHED_DATE').' '.$this->row->created_ccyymmdd; ?>
    </dd>
    <dd class="author">
        <?php echo JText::_('MOLAJO_WRITTEN_BY').' '.$this->row->display_author_name; ?>
    </dd>
</ul>

</div>