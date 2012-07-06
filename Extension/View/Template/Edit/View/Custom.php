<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<form>
	<dl class="tabs pill">
		<dd class="active"><a
			href="<?php echo Services::Registry()->get('Parameters', 'full_page_url'); ?>#edit">Edit</a></dd>
		<dd><a href="<?php echo Services::Registry()->get('Parameters', 'full_page_url'); ?>#options">Options</a></dd>
		<dd><a href="<?php echo Services::Registry()->get('Parameters', 'full_page_url'); ?>#fields">Fields</a></dd>
	</dl>
	<ul class="tabs-content">
		<li class="active" id="editTab">
			<?php
			include __DIR__ . '/Editor.php';
			?>
		</li>
		<li id="optionsTab">
			<?php
			include __DIR__ . '/Options.php';
			?>
		</li>
		<li id="fieldsTab">
			<?php
			include __DIR__ . '/Fields.php';
			?>
		</li>
	</ul>
</form>
