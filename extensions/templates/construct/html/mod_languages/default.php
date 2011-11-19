<?php defined('_JEXEC') or die;
/**
 * @version		$Id: default.php 21184 2011-04-23 05:44:08Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_languages
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Joomla 1.6+ only

JHtml::_('stylesheet', 'mod_languages/template.css', array(), true);
?>

<div class="mod-languages<?php echo $moduleclass_sfx ?>">
    <?php if ($headerText) : ?>
        <p class="pretext">
            <?php echo $headerText; ?></p>
        </p>
    <?php endif; ?>
    
    <?php if ($params->get('dropdown',1)) : ?>
        <form name="lang" method="submit">
            <select class="inputbox" onchange="document.location.replace(this.value);" >
                <?php foreach($list as $language):?>
                    <option value="<?php echo $language->link;?>" <?php echo $language->active ? 'selected="selected"' : ''?>><?php echo $language->title_native;?></option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php else : ?>
        <ul class="<?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block';?>">
        <?php foreach($list as $language):?>
            <?php if ($params->get('show_active', 0) || !$language->active):?>
                <li class="<?php echo $language->active ? 'lang-active' : '';?>">
                <a href="<?php echo $language->link;?>">
                <?php if ($params->get('image', 1)):?>
                    <?php echo JHtml::_('image', 'mod_languages/'.$language->image.'.gif', $language->title_native, array('title'=>$language->title_native), true);?>
                <?php else : ?>
                    <?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
                <?php endif; ?>
                </a>
                </li>
            <?php endif;?>
        <?php endforeach;?>
        </ul>
    <?php endif; ?>
    
    <?php if ($footerText) : ?>
        <p class="posttext">
            <?php echo $footerText; ?>
        </p>
    <?php endif; ?>
</div>