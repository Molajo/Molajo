<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Babs Gösgens. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * <include:profiler/>
 */
defined('MOLAJO') or die; ?>
<include:head/>

    <header role="banner" class="row">
    <include:template name=Adminheader/>
    </header>

	<div class="row">
		<nav role="navigation">
			<include:template name=Adminnavigationbar/>
		</nav>
		<section role="main">
				<a href="#expand" id="expander">&nbsp;</a>
				<include:message/>
				<include:template name=Adminresourcemenu/>
				<?php if (file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))) {
					include Services::Registry()->get('Parameters', 'page_view_path_include');
				} ?>
				<?php var_dump(Services::Registry()->get('Parameters', 'page_view_path_include')) ?>
				<?php var_dump(Services::Registry()->get('Parameters', 'template_view_path_include')) ?>
		</section>
	</div>

	<footer>
		<include:template name=Adminfooter wrap=none/>
	</footer>

	<include:template name=Dummyview/>

	<!-- loading jquery manually cuz it's not yetin my theme's head -->
	<!--script src="/source/Molajo/Extension/Theme/Mai/js/fallback/jquery.min.js"></script-->
	<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>
	<include:defer/>
	<?php //include('_scripts.php') ?>
