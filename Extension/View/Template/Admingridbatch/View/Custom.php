<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$pageUrl = Services::Registry()->get('Trigger', 'PageURL');
echo 'PAGEURL '. $pageUrl;
?>
	<dl class="nice vertical tabs">
		<dd><a href="<?php echo $pageUrl; ?>#batch1" class="active">Status</a></dd>
		<dd><a href="<?php echo $pageUrl; ?>#batch2">Categories</a></dd>
		<dd><a href="<?php echo $pageUrl; ?>#batch3">Tags</a></dd>
		<dd><a href="<?php echo $pageUrl; ?>#batch4">Access</a></dd>
	</dl>

	<ul class="nice tabs-content vertical">
		<li class="active" id="batch1Tab">
			<fieldset class="batch">
				<legend><?php echo Services::Language()->translate('GRID_BATCH_OPTIONS');?></legend>
					<p><?php echo Services::Language()->translate('Change the status of selected items to this value.');?></p>

				<button type="submit">
					<?php echo Services::Language()->translate('GRID_SUBMIT'); ?>
				</button>

				<button type="button">
					<?php echo Services::Language()->translate('GRID_CLEAR'); ?>
				</button>

			</fieldset>
		</li>
		<li id="batch2Tab">This is nice tab 2's content. Now you see it!</li>
		<li id="batch3Tab">This is nice tab 3's content. It's, you know...okay.</li>
	</ul>

</form>
