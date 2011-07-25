<?php
/** 
 * @package     Minima
 * @subpackage  mod_myshortcuts
 * @author      Marco Barbosa
 * @copyright   Copyright (C) 2011 Marco Barbosa. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$buttons = ModMyshortcutsHelper::getButtons();

// get the current logged in user
$currentUser = MolajoFactory::getUser();

$lang   = MolajoFactory::getLanguage();
//$lang->load('mod_menu', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
$lang->load('mod_menu', JPATH_ADMINISTRATOR, 'en-GB', true);
$lang->load('mod_menu', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, true);

?>

<ul>
	<li class="home">
		<a href="index.php">Dashboard</a>
	</li>
	<?php if( $currentUser->authorize( array('core.manage','com_config') ) ): ?>
	<li>
		<a href="<?php echo JRoute::_('index.php?option=com_config'); ?>">
			<?php echo JText::_('MOD_MYSHORTCUTS_CONFIGURATION');?>
		</a>		
	</li>
	<?php endif; ?>
	<?php 
		if( $currentUser->authorize( array('core.manage','com_content') )
			|| $currentUser->authorize( array('core.manage','com_categories') ) 
			|| $currentUser->authorize( array('core.manage','com_media') )
		) : 
	?>
	<li class="parent">
		<a href="#"><?php echo JText::_('MOD_MENU_COM_CONTENT');?></a>
		<nav class="sub">			
			<?php if( $currentUser->authorize( array('core.manage','com_content') ) ): ?>
			<ul>				
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_content#content-box'); ?>"><?php echo JText::_('MOD_MYSHORTCUTS_ARTICLES'); ?></a>
				</li>			
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_content&task=article.add'); ?>"><?php echo JText::_('MOD_MENU_COM_CONTENT_NEW_ARTICLE'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_content&view=featured#content-box'); ?>"><?php echo JText::_('MOD_MENU_COM_CONTENT_FEATURED'); ?></a>
				</li>
			</ul>
			<?php endif; ?>
			<?php if( $currentUser->authorize( array('core.manage','com_categories') ) ): ?>
			<ul>
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_categories&view=categories&extension=com_content#content-box'); ?>"><?php echo JText::_('MOD_MYSHORTCUTS_CATEGORIES'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_categories&view=category&layout=edit&extension=com_content'); ?>"><?php echo JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'); ?></a>
				</li>
			</ul>
			<?php endif; ?>
			<?php if( $currentUser->authorize( array('core.manage','com_media') ) ): ?>
			<ul class="row">
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_media#content-box');?>"><?php echo JText::_('MOD_MYSHORTCUTS_MEDIA'); ?></a>
				</li>				
			</ul>
			<?php endif; ?>
		</nav><!-- /.sub -->
	</li><!-- /.parent -->
	<?php endif; ?>
	<?php if( $currentUser->authorize( array('core.manage','com_menus') ) ): ?>
	<li class="parent">
		<a href="#"><?php echo JText::_('MOD_MENU_MENUS'); ?></a>
		<nav class="sub">			
			<ul>
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_menus&view=items'); ?>">Menu Items</a>
				</li>				
				<li>
					<a href="index.php?option=com_menus&view=item&layout=edit"><?php echo JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU_ITEM'); ?></a>
				</li>
			</ul>
			<ul class="row">			
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_menus&view=menus#content-box'); ?>"><?php echo JText::_('MOD_MENU_MENUS'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_menus&view=menu&layout=edit'); ?>"><?php echo JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU'); ?></a>
				</li>
			</ul>			
		</nav><!-- /.sub -->
	</li><!-- /.parent -->
	<?php endif; ?>
	<?php if( $currentUser->authorize( array('core.manage','com_users') ) ): ?>
	<li class="parent">
		<a href="#"><?php echo JText::_('MOD_MENU_COM_USERS'); ?></a>	
		<nav class="sub">			
			<ul>
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_users&view=users#content-box'); ?>"><?php echo JText::_('MOD_MENU_COM_USERS'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.add'); ?>"><?php echo JText::_('MOD_MENU_COM_USERS_ADD_USER'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=mail'); ?>"><?php echo JText::_('MOD_MENU_MASS_MAIL_USERS'); ?></a>
				</li>
				</ul>			
			<ul>
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_users&view=groups#content-box'); ?>"><?php echo JText::_('MOD_MENU_COM_USERS_GROUPS'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&task=group.add'); ?>"><?php echo JText::_('MOD_MENU_COM_USERS_ADD_GROUP'); ?></a>
				</li>
				</ul>			
			<ul class="row">
				<li>
					<a class="section" href="<?php echo JRoute::_('index.php?option=com_users&view=levels#content-box'); ?>"><?php echo JText::_('MOD_MENU_COM_USERS_LEVELS'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&task=level.add'); ?>"><?php echo JText::_('MOD_MENU_COM_USERS_ADD_LEVEL'); ?></a>
				</li>
			</ul>
		</nav><!-- /.sub -->
	</li><!-- /.parent -->
	<?php endif; ?>
	<?php 
		if( $currentUser->authorize( array('core.manage','com_languages') ) 
			|| $currentUser->authorize( array('core.manage','com_modules') )
			|| $currentUser->authorize( array('core.manage','com_plugins') )
			|| $currentUser->authorize( array('core.manage','com_templates') )
		): 
	?>
	<li class="parent">			
		<a href="#"><?php echo JText::_('MOD_MENU_EXTENSIONS_EXTENSIONS'); ?></a>
		<nav class="sub">			
			<ul>
				<?php if( $currentUser->authorize( array('core.manage','com_languages') ) ): ?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_languages#content-box'); ?>"><?php echo JText::_('MOD_MYSHORTCUTS_LANGUAGES'); ?></a></li>
				<?php endif; ?>
				<?php if( $currentUser->authorize( array('core.manage','com_modules') ) ): ?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_modules#content-box'); ?>"><?php echo JText::_('MOD_MYSHORTCUTS_MODULES'); ?></a></li>
				<?php endif; ?>
				<?php if( $currentUser->authorize( array('core.manage','com_plugins') ) ): ?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_plugins#content-box'); ?>"><?php echo JText::_('MOD_MYSHORTCUTS_PLUGINS'); ?></a></li>
				<?php endif; ?>
				<?php if( $currentUser->authorize( array('core.manage','com_templates') ) ): ?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_templates#content-box'); ?>"><?php echo JText::_('MOD_MYSHORTCUTS_TEMPLATES'); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav><!-- /.sub -->
	</li><!-- /.parent -->
	<?php endif; ?>
	<li class="last">
		<a href="<?php echo JRoute::_('index.php?option=com_admin&view=help'); ?>">
			<?php echo JText::_('MOD_MENU_HELP'); ?>
		</a>
	</li>
</ul>

