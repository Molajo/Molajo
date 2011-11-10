<?php defined('_JEXEC') or die;
/**
* @package		Template Framework for Molajo 1.6
* @author		Joomla Engineering http://joomlaengineering.com
* @copyright	Copyright (C) 2010 Matt Thomas | Joomla Engineering. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/
?>
<?php function modChrome_jexhtml( $module, &$parameters, &$attribs ) {
	
	$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 3;
	$headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
	$moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
	if (!empty($module->content)){ ?>
		<div class="moduletable<?php echo $parameters->get('layout_class_suffix'); ?> <?php if($moduleClass) echo $moduleClass; ?>">
			<?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h'.$headerLevel; ?>>
			<?php endif; ?>
			<?php echo $module->content; ?>	
		</div>						

<?php	}
	}
?>	
	
<?php function modChrome_jerounded( $module, &$parameters, &$attribs ) {
	$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 3;
	$headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
	$moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
	if (!empty($module->content))
		{ ?>
		<div class="moduletable<?php echo $parameters->get('layout_class_suffix'); ?> <?php if($moduleClass) echo $moduleClass; ?>">
			<div>
				<div>
					<div>		 
					<?php if ($module->showtitle) : ?>
						<h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h'.$headerLevel; ?>>
					<?php endif; ?>
					<?php echo $module->content; ?>
					</div>
				</div>
			</div>
		</div>						

<?php	}
	}			
?>

<?php function modChrome_html5Section( $module, &$parameters, &$attribs ) {
	$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 3;
	$headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
	$moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
	if (!empty($module->content))
		{ ?>
		<section class="moduletable<?php echo $parameters->get('layout_class_suffix'); ?> <?php if($moduleClass) echo $moduleClass; ?>">
			<div>		 
					<?php if ($module->showtitle) : ?>
						<h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h'.$headerLevel; ?>>
					<?php endif; ?>
					<?php echo $module->content; ?>
			</div>
		</section>						

<?php	}
	}			
?>

<?php function modChrome_html5Article( $module, &$parameters, &$attribs ) {
	$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 3;
	$headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
	$moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
	if (!empty($module->content))
		{ ?>
		<article class="moduletable<?php echo $parameters->get('layout_class_suffix'); ?> <?php if($moduleClass) echo $moduleClass; ?>">
			<div>
					<?php if ($module->showtitle) : ?>
						<h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h'.$headerLevel; ?>>
					<?php endif; ?>
					<?php echo $module->content; ?>
			</div>		
		</article>						

<?php	}
	}			
?>

<?php function modChrome_html5Nav( $module, &$parameters, &$attribs ) {
	$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 3;
	$headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
	$moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
	if (!empty($module->content))
		{ ?>
		<nav class="moduletable<?php echo $parameters->get('layout_class_suffix'); ?> <?php if($moduleClass) echo $moduleClass; ?>">
			<div>		
					<?php if ($module->showtitle) : ?>
						<h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h'.$headerLevel; ?>>
					<?php endif; ?>
					<?php echo $module->content; ?>
			</div>
		</nav>						

<?php	}
	}			
?>

<?php function modChrome_html5Aside( $module, &$parameters, &$attribs ) {
	$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 3;
	$headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
	$moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
	if (!empty($module->content))
		{ ?>
		<aside class="moduletable<?php echo $parameters->get('layout_class_suffix'); ?> <?php if($moduleClass) echo $moduleClass; ?>">
			<div>	 
					<?php if ($module->showtitle) : ?>
						<h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h'.$headerLevel; ?>>
					<?php endif; ?>
					<?php echo $module->content; ?>
			</div>
		</aside>						

<?php	}
	}			
?>
<?php function modChrome_html5Footer( $module, &$parameters, &$attribs ) {
	$headerLevel = isset($attribs['level']) ? (int) $attribs['level'] : 3;
	$headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
	$moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
	if (!empty($module->content))
		{ ?>
		<footer class="moduletable<?php echo $parameters->get('layout_class_suffix'); ?> <?php if($moduleClass) echo $moduleClass; ?>">
			<div>	 
					<?php if ($module->showtitle) : ?>
						<h<?php echo $headerLevel; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h'.$headerLevel; ?>>
					<?php endif; ?>
					<?php echo $module->content; ?>
			</div>
		</footer>						

<?php	}
	}			
?>