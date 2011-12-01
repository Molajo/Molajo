<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

if (substr(JVERSION, 0, 3) >= '1.6') {
	include JPATH_ROOT.'/modules/mod_search/tmpl/default.php';
}
else {
?>

<div class="search">
	<form action="index.php?option=com_search&view=search"  method="post">
		<fieldset>
			<label for="mod_search_searchword">
				<?php echo JText::_('search') ?>
			</label>
			<?php
					$output = '<input name="searchword" id="mod_search_searchword" maxlength="20" class="inputbox" type="text" size="'.$width.'" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" />';
		
					if ($button) :
						if ($imagebutton) :
							$button = '<input type="image" value="'.$button_text.'" class="button" src="'.$img.'"/>';
						else :
							$button = '<input type="submit" value="'.$button_text.'" class="button"/>';
						endif;
					endif;
		
					switch ($button_pos) :
						case 'top' :
							$button = $button.'<br />';
							$output = $button.$output;
							break;
		
						case 'bottom' :
							$button = '<br />'.$button;
							$output = $output.$button;
							break;
		
						case 'right' :
							$output = $output.$button;
							break;
		
						case 'left' :
						default :
							$output = $button.$output;
							break;
					endswitch;
		
					echo $output;
			?>
		</fieldset>
		<input type="hidden" name="option" value="com_search" />
		<input type="hidden" name="task"   value="search" />
	</form>
</div>
<?php }