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

<dl>
    <dt><?php echo JText::_('Number of Voters'); ?></dt>
    <dd><?php echo $this->votes[0]->voters; ?></dd>
    <dt><?php echo JText::_('First Vote'); ?></dt>
    <dd><?php echo $this->first_vote; ?></dd>
    <dt><?php echo JText::_('Last Vote'); ?></dt>
    <dd><?php echo $this->last_vote; ?></dd>
</dl>

<h3>
    <?php echo $this->escape($this->poll->title); ?>
</h3>

<table>
    <thead>
    <tr>
        <th id="ordering" class="td_1"><?php echo JText::_('Hits'); ?></th>
        <th id="ordering2" class="td_2"><?php echo JText::_('Percent'); ?></th>
        <th id="ordering3" class="td_3"><?php echo JText::_('Graph'); ?></th>
    </tr>
    </thead>
    <?php for ($row = 0; $row < count($this->votes); $row++) : ?>
    <?php $vote = $this->votes[$row]; ?>
    <tr>
        <td colspan="3" id="question<?php echo $row; ?>" class="question">
            <?php echo $vote->text; ?>
        </td>
    </tr>
    <tr class="answer<?php echo $vote->odd; ?>">
        <td mastheads="ordering question<?php echo $row; ?>" class="hits">
            <?php echo $vote->hits; ?>
        </td>
        <td mastheads="ordering2 question<?php echo $row; ?>" class="percent">
            <?php echo $vote->percent . '%' ?>
        </td>
        <td mastheads="ordering3 question<?php echo $row; ?>" class="graph">
            <div class="<?php echo $vote->class; ?>"
                 style="height:<?php echo $vote->barheight; ?>px;width:<?php echo $vote->percent; ?>% !important"></div>
        </td>
    </tr>
    <?php endfor; ?>
</table>
