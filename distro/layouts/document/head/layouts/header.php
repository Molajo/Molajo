<?php
/**
 * @package     Molajo
 * @subpackage  Item
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>

<h2>
    <?php echo $this->row->title; ?>
</h2>

<?php if ($this->row->canEdit) : ?>
<ul class="actions">
    <li class="edit-icon">
        <?php echo MolajoHTML::_('icon.edit', $this->row, $this->state); ?>
    </li>
</ul>
<?php endif; ?>

<ul class="subheading">
    <dd class="published">
        <?php echo MolajoTextHelper::_('MOLAJO_PUBLISHED_DATE') . ' ' . $this->row->created_ccyymmdd; ?>
    </dd>
    <dd class="author">
        <?php echo MolajoTextHelper::_('MOLAJO_WRITTEN_BY') . ' ' . $this->row->display_author_name; ?>
    </dd>
</ul>