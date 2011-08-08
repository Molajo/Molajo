<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
$hide = JRequest::getInt('hidemainmenu');
?>
<ul id="submenu">
	<?php foreach ($list as $item) : ?>
	<li>
	<?php
	if ($hide) :
		if (isset ($item[2]) && $item[2] == 1) :
			?><span class="nolink active"><?php echo $item[0]; ?></span><?php
		else :
			?><span class="nolink"><?php echo $item[0]; ?></span><?php
		endif;
	else :
		if(strlen($item[1])) :
			if (isset ($item[2]) && $item[2] == 1) :
				?><a class="active" href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
			else :
				?><a href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a><?php
			endif;
		else :
			?><?php echo $item[0]; ?><?php
		endif;
	endif;
	?>
	</li>
	<?php endforeach; ?>
</ul>