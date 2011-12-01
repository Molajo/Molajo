<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

// Joomla 1.5 only

?>

<script type="text/javascript">
    <!--
    function tableOrdering(order, dir, task) {
        var form = document.adminForm;

        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit(task);
    }
    // -->
</script>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">
    <?php if ($this->params->get('filter')) : ?>
    <fieldset class="filters">

        <legend class="hidelabeltxt">
            <?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?>
        </legend>

        <label class="filter-search-lbl"
               for="filter-search"><?php echo JText::_($this->escape($this->params->get('filter_type')) . ' ' . 'Filter') . '&nbsp;'; ?>
            <input type="text" name="filter-search" value="<?php echo $this->escape($this->lists['filter']); ?>"
                   class="inputbox" onchange="document.adminForm.submit();"/>
        </label>


        <?php if ($this->params->get('show_pagination_limit')) : ?>
        <div class="display-limit">
            <?php echo JText::_('Display Num'); ?>&nbsp;
            <?php echo $this->pagination->getLimitBox(); ?>
        </div>
        <?php endif; ?>
    </fieldset>
    <?php endif; ?>



    <table class="category">

        <?php if ($this->params->get('show_headings')) : ?>
        <thead>
        <tr>
            <th class="list-count" id="count">
                <?php echo JText::_('Num'); ?>
            </th>

            <?php if ($this->params->get('show_title')) : ?>
            <th class="list-title" id="tableOrdering">
                <?php echo JHTML::_('grid.sort', 'Item Title', 'a.title', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
            <?php endif; ?>

            <?php if ($this->params->get('show_date')) : ?>
            <th class="list-date" id="tableOrdering2">
                <?php echo JHTML::_('grid.sort', 'Date', 'a.created', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
            <?php endif; ?>

            <?php if ($this->params->get('show_author')) : ?>
            <th class="list-author" id="tableOrdering3">
                <?php echo JHTML::_('grid.sort', 'Author', 'author', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
            <?php endif; ?>

            <?php if ($this->params->get('show_hits')) : ?>
            <th class="list-hits" id="tableOrdering4">
                <?php echo JHTML::_('grid.sort', 'Hits', 'a.hits', $this->lists['order_Dir'], $this->lists['order']); ?>
            </th>
            <?php endif; ?>
        </tr>
        </thead>
        <?php endif; ?>

        <tbody>

        <?php foreach ($this->items as $item) : ?>
        <tr class="sectiontableentry<?php echo ($item->odd + 1); ?>">
            <td mastheads="count">
                <?php echo $this->pagination->getRowOffset((int)$item->count); ?>
            </td>

            <?php if ($this->params->get('show_title')) : ?>
            <td class="list-title" mastheads="tableOrdering">
                <?php if ($item->access <= $this->user->get('aid', 0)) : ?>
                <a href="<?php echo $item->link; ?>"><?php echo $this->escape($item->title); ?></a>
                <ul class="actions">
                    <li class="edit-icon">
                        <?php echo JHTML::_('icon.edit', $item, $this->params, $this->access); ?>
                    </li>
                </ul>
                <?php else : ?>
                <?php echo $item->title; ?> :
                <a href="<?php echo JRoute::_('index.php?option=com_user&task=register'); ?>">
                    <?php echo JText::_('Register to read more...'); ?></a>
                <?php endif; ?>
            </td>
            <?php endif; ?>

            <?php if ($this->params->get('show_date')) : ?>
            <td class="list-date" mastheads="tableOrdering2">
                <?php echo $this->escape($item->created); ?>
            </td>
            <?php endif; ?>

            <?php if ($this->params->get('show_author')) : ?>
            <td class="list-author" mastheads="tableOrdering3">
                <?php echo $item->created_by_alias ? $this->escape($item->created_by_alias)
                    : $this->escape($item->author); ?>
            </td>
            <?php endif; ?>

            <?php if ($this->params->get('show_hits')) : ?>
            <td class="list-hits" mastheads="tableOrdering4">
                <?php echo $item->hits ? (int)$item->hits : '-'; ?>
            </td>
            <?php endif; ?>

        </tr>
            <?php endforeach; ?>

        </tbody>

    </table>

    <?php if ($this->params->get('show_pagination')) : ?>
    <nav class="pagination">
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </nav>
    <?php endif; ?>
    <input type="hidden" name="id" value="<?php echo (int)$this->category->id; ?>"/>
    <input type="hidden" name="sectionid" value="<?php echo (int)$this->category->sectionid; ?>"/>
    <input type="hidden" name="task" value="<?php echo $this->lists['task']; ?>"/>
    <input type="hidden" name="filter_order" value=""/>
    <input type="hidden" name="filter_order_Dir" value=""/>
    <input type="hidden" name="limitstart" value="0"/>
</form>
