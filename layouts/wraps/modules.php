<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_rounded($module, &$params, &$attribs)
{
	if ($module->content)
	{
		?>
		<div id="<?php echo $attribs['id'] ?>">
			<div class="t">
				<div class="t">
					<div class="t"></div>
				</div>
			</div>
			<div class="m">
				<?php echo $module->content; ?>
				<div class="clr"></div>
			</div>
			<div class="b">
				<div class="b">
					<div class="b"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
/*
 * none (output raw module content)
 */
function modChrome_none($module, &$params, &$attribs)
{
	echo $module->content;
}

/*
 * xhtml (divs and font header tags)
 */
function modChrome_xhtml($module, &$params, &$attribs)
{
	$content = trim($module->content);
	if (!empty ($content)) : ?>
		<div class="module<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
		<?php if ($module->showtitle != 0) : ?>
			<h3><?php echo $module->title; ?></h3>
		<?php endif; ?>
			<?php echo $content; ?>
		</div>
	<?php endif;
}

/*
 * allows sliders
 */
function modChrome_sliders($module, &$params, &$attribs)
{
	$content = trim($module->content);
	if (!empty($content))
	{
		if ($params->get('automatic_title','0')=='0') {
			echo JHtml::_('sliders.panel', $module->title, 'module'.$module->id);
		}
		elseif (method_exists('mod'.$module->name.'Helper','getTitle')) {
			echo JHtml::_('sliders.panel', call_user_func_array(array('mod'.$module->name.'Helper','getTitle'), array($params)), 'module'.$module->id);
		}
		else {
			echo JHtml::_('sliders.panel', JText::_('MOD_'.$module->name.'_TITLE'), 'module'.$module->id);
		}
		echo $content;
	}
}

/*
 * allows tabs
 */
function modChrome_tabs($module, &$params, &$attribs)
{
	$content = trim($module->content);
	if (!empty($content))
	{
		if ($params->get('automatic_title','0')=='0') {
			echo JHtml::_('tabs.panel', $module->title, 'module'.$module->id);
		}
		elseif (method_exists('mod'.$module->name.'Helper','getTitle')) {
			echo JHtml::_('tabs.panel', call_user_func_array(array('mod'.$module->name.'Helper','getTitle'), array($params)), 'module'.$module->id);
		}
		else {
			echo JHtml::_('tabs.panel', JText::_('MOD_'.$module->name.'_TITLE'), 'module'.$module->id);
		}
		echo $content;
	}
}