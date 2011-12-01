<?php defined('_JEXEC') or die;
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

// Joomla 1.6+ only

JHtml::_('behavior.tooltip');
?>

<section class="profile<?php echo $this->pageclass_sfx?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1>
	        <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php echo $this->loadTemplate('core'); ?>

    <?php echo $this->loadTemplate('params'); ?>

    <?php echo $this->loadTemplate('custom'); ?>

    <?php if (JFactory::getUser()->id == $this->data->id) : ?>
        <a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
	        <?php echo JText::_('USERS_Edit_Profile'); ?>
	    </a>
    <?php endif; ?>
</section>
