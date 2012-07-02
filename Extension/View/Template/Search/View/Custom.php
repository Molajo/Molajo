<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
defined('MOLAJO') or die;
$action = Services::Registry()->get('Triggerdata', 'PageURL'); ?>
<label>Search</label>
<div class="row">
	<div class="five columns">
		<div class="row collapse">
			<div class="eight mobile-three columns">
				<input type="text" />
			</div>
			<div class="four mobile-one columns">
				<a href="<?php echo $action; ?>" class="postfix button">Search</a>
			</div>
		</div>
	</div>
</div>

