<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Babs Gösgens. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$_baseUri = Services::Registry()->get('Triggerdata', 'full_page_url');
?>
    <include:template name=Adminheader/>

	<nav role="navigation">
		<include:template name=Adminnavigationbar/>
	</nav>

	<section role="main">
		<include:message/>

		<include:request/>
	</section>

