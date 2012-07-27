<?php
use Molajo\Service\Services;
/**
 * @package       Molajo
 * @copyright     2012 Amy Stephen. All rights reserved.
 * @license       GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$title = Services::Registry()->get('Triggerdata', 'PageTitle');
if ($title == '') {
	$title = $this->row->criteria_title;
} else {
	$title .= '-' . $this->row->criteria_title;
}
$homeURL = Services::Registry()->get('Configuration', 'application_base_url');
$resourceURL = Services::Registry()->get('Triggerdata', 'full_page_url');
var_dump($resourceURL);
?>
		<div>
			<h1><a href="index.php"><span><strong>Molajo</strong> Admin Interface</span></a></h1>
			<nav>
				<dl class="settings">
					<dt><a href="#search"><i>=</i><span>Search</span></a></dt
					><dd id="search">
						<a href="#" class="dismiss"><i>g</i><span>Close</span></a>
						<form role="search">
							<fieldset>
								<input type="search" placeholder="Search Resources">
							</fieldset>
						</form>
					</dd
					><dt class="user"><a href="#user"><img src="media/smile.png" alt="" width="40" height="40" /><span>Babs G&ouml;sgens</span></a></dt
					><dd id="user">
						<a href="#" class="dismiss"><i>g</i><span>Close</span></a>
						<ul>
							<li><a href="#">Dropdown Item</a></li>
							<li><a href="#">Another Dropdown Item</a></li>
							<li class="divider"></li>
							<li><a href="#">Last Item</a></li>
						</ul>
					</dd
					><dt class="last"><a href="#settings"><i>a</i><span>Settings</span></a></dt
					><dd id="settings">
						<a href="#" class="dismiss"><i>g</i><span>Close</span></a>
					Settings</dd>
				</dl>
			</nav>
		</div>

<!-- <div class="row header">
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
</div> -->
