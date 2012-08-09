<?php
use Molajo\Service\Services;

/**
 * @package     Molajo
 * @copyright   2012 Babs Gösgens. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageURL = Services::Registry()->get('Plugindata', 'full_page_url');
?>
    <include:template name=Adminheader/>

	<nav role="navigation">
		<include:template name=Adminnavigationbar/>
	</nav>

	<div role="main">
	<section>
		<include:message/>
		<include:request/>
	</section>

	</div>
