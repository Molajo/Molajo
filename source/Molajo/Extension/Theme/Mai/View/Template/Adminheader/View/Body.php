<?php
use Molajo\Service\Services;
/**
 * @package       Molajo
 * @copyright     2012 Babs Gösgens. All rights reserved.
 * @license       GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageUri = $_SERVER['REQUEST_URI'];

$title = Services::Registry()->get('Triggerdata', 'PageTitle');
if ($title == '') {
    $title = $this->row->criteria_title;
} else {
    $title .= '-' . $this->row->criteria_title;
}
$homeURL = Services::Registry()->get('Configuration', 'application_base_url');
$resourceURL = Services::Registry()->get('Triggerdata', 'full_page_url');
?>
		<div>
			<h1><a href="<?php echo $homeURL ?>"><span><strong>Molajo</strong> Admin Interface</span></a></h1>
			<nav>
				<dl class="settings">
					<dt><a href="<?php echo $pageUri ?>#search"><i>=</i><span>Search</span></a></dt
					><dd id="search">
						<a href="<?php echo $pageUri ?>#" class="dismiss"><i>g</i><span>Close</span></a>
						<form role="search">
							<fieldset>
								<input type="search" placeholder="Search Resources">
							</fieldset>
						</form>
					</dd
					><dt class="user"><a href="<?php echo $pageUri ?>#user"><img src="/source/Molajo/Extension/Theme/Mai/Images/smile.png" alt="" width="40" height="40" /><span>Babs G&ouml;sgens</span></a></dt
					><dd id="user">
						<a href="<?php echo $pageUri ?>#" class="dismiss"><i>g</i><span>Close</span></a>
						<ul>
							<li><a href="<?php echo $pageUri ?>#">Dropdown Item</a></li>
							<li><a href="<?php echo $pageUri ?>#">Another Dropdown Item</a></li>
							<li class="divider"></li>
							<li><a href="<?php echo $pageUri ?>#">Last Item</a></li>
						</ul>
					</dd
					><dt class="last"><a href="<?php echo $pageUri ?>#settings"><i>a</i><span>Settings</span></a></dt
					><dd id="settings">
						<a href="<?php echo $pageUri ?>#" class="dismiss"><i>g</i><span>Close</span></a>
						
					Settings</dd>
				</dl>
			</nav>
		</div>
