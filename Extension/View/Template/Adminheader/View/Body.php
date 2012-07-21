<?php
use Molajo\Service\Services;
/**
 * @package       Molajo
 * @copyright     2012 Amy Stephen. All rights reserved.
 * @license       GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$title = Services::Registry()->get('Triggerdata', 'AdminTitle');
if ($title == '') {
	$title = $this->row->criteria_title;
} else {
	$title .= '-' . $this->row->criteria_title;
}
$homeURL = Services::Registry()->get('Configuration', 'application_base_url');
?>
<div class="row header">
	<div class="twelve columns">
		<div class="row topmenu topmenu-text">
			<div class="three columns">
				<span>Molajo</span>
			</div>
			<div class="nine columns">
				<include:template name=Adminnavigationbar/>
			</div>
		</div>
		<div class="row heading heading-text">
			<div class="twelve columns">
				<h1><?php echo $title; ?></h1>
			</div>
		</div>
	</div>
</div>
