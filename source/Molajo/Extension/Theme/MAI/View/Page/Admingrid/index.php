<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>

	<div class="row">
		<nav role="navigation">
			<include:template name=Adminsectionmenu/>
			<include:template name=Adminresourcemenu/>
<?php //include('_nav-dl.php') ?>
		</nav>
		<section role="main">
				<include:message/>
				<a href="#expand" id="expander"></a>
				<include:request/>
		</section>
	</div>


<!-- <div class="row">
    <div class="two columns">
        <include:template name=Adminsectionmenu/>
    </div>
    <div class="ten columns">
    	IS this used?
    	<h2>Yes, it is.</h2>
		<div class="row">
			<div class="twelve columns">
				<div id="container-filters">
					<div class="row">
						<div class="eight columns">
							<include:template name=Adminresourcemenu/>
						</div>
						<div class="one columns">
							<div id="t-filters"><h5><a href="#"><?php echo Services::Language()->translate('Filters'); ?></a></h5></div>
						</div>
						<div class="one columns">
							<div id="t-batch"><h5><a href="#"><?php echo Services::Language()->translate('Batch'); ?></a></h5></div>
						</div>
						<div class="one columns">
							<div id="t-view"><h5><a href="#"><?php echo Services::Language()->translate('View'); ?></a></h5></div>
						</div>
						<div class="one columns">
							<div id="t-options"><h5><a href="#"><?php echo Services::Language()->translate('Options'); ?></a></h5></div>
						</div>
					</div>
				</div>
			<include:template name=Admingridfilters/>
			<include:template name=Admingridbatch/>
			<include:template name=Admingridview/>
			<include:template name=Admingridoptions/>
			</div>
		</div>
		<include:request/>
    </div>
</div> -->
