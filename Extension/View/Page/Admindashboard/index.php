<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$url = Services::Registry()->get('Plugindata', 'full_page_url'); ?>
    <include:template name=Adminsectionmenu wrap=nav wrap_view_css_class=navsection role=/>
    <div class="ten columns">
		<div class="row">
			<div class="eleven columns">&nbsp;</div>
			<div class="one columns">
				<ul class="link-list">
					<li id="t-options"><strong><a href="<?php echo $url; ?>#Options"><?php echo Services::Language()->translate('Options'); ?></a></strong></li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="twelve columns">
				<div id="m-options" style="display: none;">
					<h3><?php echo Services::Language()->translate('Add Portlet to Dashboard'); ?></h3>
					<form action="<?php echo $url; ?>" method="post" name="Admingridfilters">
						<ul class="filter">
							<li class="filter">
								<include:template name=formselectlist wrap=div wrap_class=filter value=list_portlets*/>
							</li>
							<li>
								<include:template name=formbutton size="small" name=add id=add value=Add/>
  								<input type="submit" class="submit button small" name="add" id="add" value="Add">
							</li>
						</ul>
					</form>
				</div>
				<div id="b-options"></div>
			</div>
		</div>
        <include:request/>
