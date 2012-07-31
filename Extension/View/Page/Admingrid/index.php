<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 *
 */
defined('MOLAJO') or die;
$url = Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<div class="row">
    <div class="two columns hide-for-small">
		<include:template name=Dummy/>
        <include:template name=Adminsectionmenu/>
    </div>
    <div class="ten column">
		<div class="row">
			<div class="twelve columns">
				<div id="container-filters">
					<div class="row">
						<div class="eight columns">
							<include:template name=Adminstatusmenu/>
						</div>
						<div class="four columns">
							<ul class="link-list">
								<li id="t-filters"><strong><a href="<?php echo $url; ?>#Filters"><?php echo Services::Language()->translate('Filters'); ?></a></strong></li>
								<li id="t-batch"><strong><a href="<?php echo $url; ?>#Batch"><?php echo Services::Language()->translate('Batch'); ?></a></strong></li>
								<li id="t-view"><strong><a href="<?php echo $url; ?>#View"><?php echo Services::Language()->translate('View'); ?></a></strong></li>
								<li id="t-options"><strong><a href="<?php echo $url; ?>#Options"><?php echo Services::Language()->translate('Options'); ?></a></strong></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="twelve columns">
				<include:template name=Admingridfilters/>
				<include:template name=Admingridbatch/>
				<include:template name=Admingridview/>
				<include:template name=Admingridoptions/>
			</div>
		</div>
		<div class="row">
			<div class="twelve columns">
				<include:request/>
			</div>
		</div>
		<div class="row">
			<div class="twelve columns">
				<include:template name=Admingridpagination value=AdminGridPagination/>
			</div>
		</div>
	</div>
</div>
<div class="row show-for-small">
	<div class="twelve columns">
		<include:template name=Adminsectionmenu/>
	</div>
</div>
