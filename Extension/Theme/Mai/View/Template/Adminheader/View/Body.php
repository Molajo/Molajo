<?php
use Molajo\Service\Services;
/**
 * @package       Molajo
 * @copyright     2012 Babs Gösgens. All rights reserved.
 * @license       GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageURL = Services::Registry()->get('Triggerdata', 'full_page_url');

$title = Services::Registry()->get('Triggerdata', 'PageTitle');
if ($title == '') {
    $title = $this->row->criteria_title;
} else {
    $title .= '-' . $this->row->criteria_title;
}
$homeURL = Services::Registry()->get('Configuration', 'application_base_url');
$pageURL = Services::Registry()->get('Triggerdata', 'full_page_url');
?>
    <header role="banner">
		<div>
			<h1><a href="<?php echo $homeURL ?>"><i>"</i><span><strong>Molajo</strong> Admin Interface</span></a></h1>
			<nav>

				<ul class="suckerfish settings">
					<li class="search">
						<a href="<?php echo $pageURL ?>#search"><i>=</i><span>Search</span></a>
						<form role="search">
							<input type="search" placeholder="Search Resources">
						</form>
					</li
					><li class="user align-right">
						<a href="<?php echo $pageURL ?>#user">
							<img src="/source/Molajo/Extension/Theme/Mai/Images/smile.png" alt="" width="40" height="40" />
							<span>Babs G&ouml;sgens</span>
						</a>
						<div>
							<h2>User Settings</h2>
							<ul id="user">
								<li><a href="<?php echo $pageURL ?>#"><?php echo Services::Language()->translate('User Settings'); ?></a></li>
								<li><a href="<?php echo $pageURL ?>#">Mail <small>no&nbsp;new&nbsp;messages</small></a></li>
								<li class="divider"></li>
								<li><a href="<?php echo $pageURL ?>#">Last Item</a></li>
							</ul>
						</div>
					</li
					><li class="config last align-right">
						<a href="<?php echo $pageURL ?>#aplication-config"><i>a</i><span>Settings</span></a>
						<div>
							<h2><?php echo Services::Language()->translate('Site Settings'); ?></h2>
							<ul>
								<li><a href="<?php echo $pageURL ?>" data-reveal-id="application-config"><?php echo Services::Language()->translate('Application&nbsp;Configuration'); ?></a></li>
								<li><a href="<?php echo $pageURL ?>" data-reveal-id="application-options"><?php echo Services::Language()->translate('Applications&hellip;'); ?></a></li>
								<li class="divider"></li>
								<li class="switch">

									<?php echo Services::Language()->translate('This&nbsp;application&nbsp;is'); ?>&nbsp;&nbsp;<a href="<?php echo $pageURL ?>" class="tiny success radius button" data-reveal-id="offline-switch"><?php echo Services::Language()->translate('Online'); ?></a>
									<div id="offline-switch" class="reveal-modal">
										Click 'Continue' to take this application offline. <label for="offline_message">Edit the site's offline message:</label>

										<form action="<?php echo $pageURL ?>" class="custom">
											<input type="hidden" name="offline" value="1" />

											<textarea name="offline_message" id="offline_message" id="offline_message">This site is not available</textarea>
											
											<ul class="button-group radius">
												<li><button class="button alert radius">Cancel</button></li>
												<li><button class="button secondary radius">Continue</button></li>
											</ul>
										</form>
									</div>

								</li>
							</ul>
						</div>
					</li>
				</ul>

			</nav>
		</div>
    </header>

	<div id="application-config" class="reveal-modal">

		<ul class="button-group radius">
			<li><button class="button secondary radius">Simple</button></li>
			<li><button class="button secondary radius active">Advanced</button></li>
		</ul>

		<form class="custom">

			<dl class="tabs">
				<dd class="active"><a href="<?php echo $pageURL ?>#application"><?php echo Services::Language()->translate('Application'); ?></a></dd>
				<dd><a href="<?php echo $pageURL ?>#development"><?php echo Services::Language()->translate('Development'); ?></a></dd>
				<dd><a href="<?php echo $pageURL ?>#design"><?php echo Services::Language()->translate('Design'); ?></a></dd>
			</dl>

			<ul class="tabs-content">
				<li class="active" id="applicationTab">
					
					<ul class="accordion">
						<li class="active">
							<h5 class="title"><?php echo Services::Language()->translate('Application'); ?></h5>
							<div class="content">

								<div class="row">
									<div class="five columns">
										<label class="inline" for="application_name">
											<?php echo Services::Language()->translate('application_name'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate('A name for your website'); ?>" 
											name="application_name" 
											id="application_name" 
											class="eighteen" 
											value="Site 2" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="This is the name of your website. It will be put in the browser title of your site and Google will use it in its search results."><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="application_home_catalog_id"><?php echo Services::Language()->translate('application_home_catalog_id'); ?></label>
									</div>
									<div class="twelve columns">
										<select name="application_home_catalog_id" id="application_home_catalog_id">
											<option value="423">Label</option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="???"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="application_logon_requirement"><?php echo Services::Language()->translate('application_logon_requirement'); ?></label>
									</div>
									<div class="twelve columns">
										<select name="application_logon_requirement" id="application_logon_requirement">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="When set to Yes, users will be required to log on to get access."><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

							</div>

						</li>
						<li>
							<h5 class="title"><?php echo Services::Language()->translate('Url'); ?></h5>
							<div class="content">

								<div class="row">
									<div class="five columns">
										<label class="inline" for="url_sef">
											<?php echo Services::Language()->translate('url_sef'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="url_sef" id="url_sef">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="url_unicode_slugs">
											<?php echo Services::Language()->translate('url_unicode_slugs'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="url_unicode_slugs" id="url_unicode_slugs">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="url_force_ssl">
											<?php echo Services::Language()->translate('url_force_ssl'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="url_force_ssl" id="url_force_ssl">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

							</div>

						</li>
						<li>
							<h5 class="title"><?php echo Services::Language()->translate('Language'); ?></h5>
							<div class="content">

								<div class="row">
									<div class="five columns">
										<label class="inline" for="language">
											<?php echo Services::Language()->translate('language'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="language" id="language">
											<option value="en-GB" selected="selected"><?php echo Services::Language()->translate('English (GB)'); ?></option>
											<option value="nl-NL"><?php echo Services::Language()->translate('Dutch (NL)'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="language_direction">
											<?php echo Services::Language()->translate('language_direction'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="language_direction" id="language_direction">
											<option value="rtl" selected="selected"><?php echo Services::Language()->translate('Left to Right'); ?></option>
											<option value="ltr"><?php echo Services::Language()->translate('Right to Left'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="language_multilingual">
											<?php echo Services::Language()->translate('language_multilingual'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="language_multilingual" id="language_multilingual">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="language_utc_offset">
											<?php echo Services::Language()->translate('language_utc_offset'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="language_utc_offset" id="language_utc_offset">
											<option value="UTC" selected="selected"><?php echo Services::Language()->translate('UTC'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

							</div>
						</li>
					</ul>

				</li>
				<li id="developmentTab">

					<ul class="accordion">
						<li class="active">
							<h5 class="title"><?php echo Services::Language()->translate('Profiler'); ?></h5>
							<div class="content">

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler">
											<?php echo Services::Language()->translate('profiler'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler" id="profiler">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler">
											<?php echo Services::Language()->translate('profiler_verbose'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler_verbose" id="profiler_verbose">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler">
											<?php echo Services::Language()->translate('profiler_start_with'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler_start_with" id="profiler_start_with">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_start_with">
											<?php echo Services::Language()->translate('profiler_start_with'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate('Initialise'); ?>" 
											name="profiler_start_with" 
											id="profiler_start_with" 
											class="eighteen" 
											value="Initialise" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_end_with">
											<?php echo Services::Language()->translate('profiler_end_with'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate('Response'); ?>" 
											name="profiler_end_with" 
											id="profiler_end_with" 
											class="eighteen" 
											value="Response" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<!-- <div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_output">
											<?php echo Services::Language()->translate('profiler_output'); ?>
										</label>
									</div>
									<div class="twelve columns"><?php
											$outputTypes = explode(",",'Actions,Application,Authorisation,Queries,Rendering,Routing,Services,Triggers');
											foreach ($outputTypes as $type):
										?>
										<label>
											<input 
												type="checkbox" 
												placeholder="<?php echo Services::Language()->translate('Response'); ?>" 
												name="profiler_output[]" 
												id="profiler_output[]" 
												value="<?php echo $type ?>"
												checked="checked"/>&nbsp;<?php echo Services::Language()->translate($type); ?>
										</label><?php
											endforeach; ?>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div> -->

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_output">
											<?php echo Services::Language()->translate('profiler_output'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<div class="row">
											<?php
											$outputTypes = explode(",",'Actions,Application,Authorisation,Queries,Rendering,Routing,Services,Triggers');
											foreach ($outputTypes as $type):
										?>
										<div class="five columns">
											<label class="inline"><?php echo Services::Language()->translate($type); ?></label>
										</div>
										<div class="thirteen columns">
											<select name="profiler_output_queries_table_registry" id="profiler_output_queries_table_registry">
												<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
												<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
											</select>
										</div>
										<?php
											endforeach; ?>
										</div>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_output_queries_table_registry">
											<?php echo Services::Language()->translate('profiler_output_queries_table_registry'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler_output_queries_table_registry" id="profiler_output_queries_table_registry">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_output_queries_sql">
											<?php echo Services::Language()->translate('profiler_output_queries_sql'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler_output_queries_sql" id="profiler_output_queries_sql">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_output_queries_query_results">
											<?php echo Services::Language()->translate('profiler_output_queries_query_results'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler_output_queries_query_results" id="profiler_output_queries_query_results">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_console_template_view_id">
											<?php echo Services::Language()->translate('profiler_console_template_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler_console_template_view_id" id="profiler_console_template_view_id">
											<option value="1385" selected="selected"><?php echo Services::Language()->translate('Profiler'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="profiler_console_wrap_view_id">
											<?php echo Services::Language()->translate('profiler_console_wrap_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="profiler_console_template_view_id" id="profiler_console_wrap_view_id">
											<option value="2090" selected="selected"><?php echo Services::Language()->translate('None'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

							</div>
						</li>
					</ul>

				</li>
				<li id="designTab">

					<ul class="accordion">
						<li class="active">
							<h5 class="title"><?php echo Services::Language()->translate('Views'); ?></h5>
							<div class="content">

								<h6><?php echo Services::Language()->translate('Head'); ?></h6>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="head_template_view_id">
											<?php echo Services::Language()->translate('head_template_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="head_template_view_id" id="head_template_view_id">
											<option value="1340" selected="selected"><?php echo Services::Language()->translate('Head'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="head_wrap_view_id">
											<?php echo Services::Language()->translate('head_wrap_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="head_wrap_view_id" id="head_wrap_view_id">
											<option value="2090" selected="selected"><?php echo Services::Language()->translate('None'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<h6><?php echo Services::Language()->translate('Defer'); ?></h6>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="defer_template_view_id">
											<?php echo Services::Language()->translate('defer_template_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="defer_template_view_id" id="defer_template_view_id">
											<option value="1240" selected="selected"><?php echo Services::Language()->translate('Defer'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="defer_wrap_view_id">
											<?php echo Services::Language()->translate('defer_wrap_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="defer_wrap_view_id" id="defer_wrap_view_id">
											<option value="2090" selected="selected"><?php echo Services::Language()->translate('None'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<h6><?php echo Services::Language()->translate('Message'); ?></h6>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="message_template_view_id">
											<?php echo Services::Language()->translate('message_template_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="message_template_view_id" id="message_template_view_id">
											<option value="1350" selected="selected"><?php echo Services::Language()->translate('Message'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="message_wrap_view_id">
											<?php echo Services::Language()->translate('message_wrap_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="message_wrap_view_id" id="message_wrap_view_id">
											<option value="2030" selected="selected"><?php echo Services::Language()->translate('Div'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<h6><?php echo Services::Language()->translate('Offline'); ?></h6>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="offline_theme_id">
											<?php echo Services::Language()->translate('offline_theme_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="offline_theme_id" id="offline_theme_id">
											<option value="9000" selected="selected"><?php echo Services::Language()->translate('System'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="offline_page_view_id">
											<?php echo Services::Language()->translate('offline_page_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="offline_page_view_id" id="offline_page_view_id">
											<option value="260" selected="selected"><?php echo Services::Language()->translate('???'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="offline_message">
											<?php echo Services::Language()->translate('offline_message'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate('This site is not available'); ?>" 
											name="offline_message" 
											id="offline_message" 
											class="eighteen" 
											value="This site is not available" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="The message to display when your site is offline."><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<h6><?php echo Services::Language()->translate('Error'); ?></h6>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="error_page_view_id">
											<?php echo Services::Language()->translate('error_page_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="error_page_view_id" id="error_page_view_id">
											<option value="9000" selected="selected"><?php echo Services::Language()->translate('System'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="head_wrap_view_id">
											<?php echo Services::Language()->translate('head_wrap_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="head_wrap_view_id" id="head_wrap_view_id">
											<option value="250" selected="selected"><?php echo Services::Language()->translate('???'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="error_404_message">
											<?php echo Services::Language()->translate('error_404_message'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate('Page not found'); ?>" 
											name="error_404_message" 
											id="error_404_message" 
											class="eighteen" 
											value="Page not found" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="The message to display when your site is offline."><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="error_403_message">
											<?php echo Services::Language()->translate('error_403_message'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate('Not authorised'); ?>" 
											name="error_403_message" 
											id="error_403_message" 
											class="eighteen" 
											value="Not authorised" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="The message to display when your site is offline."><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

							</div>
						</li>
						<li>

							<h5 class="title"><?php echo Services::Language()->translate('Theme'); ?></h5>
							<div class="content">

								<h6><?php echo Services::Language()->translate('Theme'); ?></h6>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_theme_id">
											<?php echo Services::Language()->translate('menuitem_theme_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="menuitem_theme_id" id="menuitem_theme_id">
											<option value="9050" selected="selected"><?php echo Services::Language()->translate('MAI'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<h6><?php echo Services::Language()->translate('Menu Item'); ?></h6>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_page_view_id">
											<?php echo Services::Language()->translate('menuitem_page_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="menuitem_page_view_id" id="menuitem_page_view_id">
											<option value="200" selected="selected"><?php echo Services::Language()->translate('Admin'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_page_view_css_id">
											<?php echo Services::Language()->translate('menuitem_page_view_css_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate(''); ?>" 
											name="menuitem_page_view_css_id" 
											id="menuitem_page_view_css_id" 
											class="eighteen" 
											value="" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_page_view_css_class">
											<?php echo Services::Language()->translate('menuitem_page_view_css_class'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate(''); ?>" 
											name="menuitem_page_view_css_class" 
											id="menuitem_page_view_css_class" 
											class="eighteen" 
											value="" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_template_view_id">
											<?php echo Services::Language()->translate('menuitem_template_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="menuitem_template_view_id" id="menuitem_template_view_id">
											<option value="1030" selected="selected"><?php echo Services::Language()->translate('Admindashboard'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_template_view_css_id">
											<?php echo Services::Language()->translate('menuitem_template_view_css_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate(''); ?>" 
											name="menuitem_template_view_css_id" 
											id="menuitem_template_view_css_id" 
											class="eighteen" 
											value="" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_template_view_css_class">
											<?php echo Services::Language()->translate('menuitem_template_view_css_class'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate(''); ?>" 
											name="menuitem_template_view_css_class" 
											id="menuitem_template_view_css_class" 
											class="eighteen" 
											value="" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_wrap_view_id">
											<?php echo Services::Language()->translate('menuitem_wrap_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="menuitem_wrap_view_id" id="menuitem_wrap_view_id">
											<option value="2030" selected="selected"><?php echo Services::Language()->translate('Div'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_wrap_view_id">
											<?php echo Services::Language()->translate('menuitem_wrap_view_id'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate(''); ?>" 
											name="menuitem_wrap_view_id" 
											id="menuitem_wrap_view_id" 
											class="eighteen" 
											value="" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_wrap_view_css_class">
											<?php echo Services::Language()->translate('menuitem_wrap_view_css_class'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<input 
											type="text" 
											placeholder="<?php echo Services::Language()->translate(''); ?>" 
											name="menuitem_wrap_view_css_class" 
											id="menuitem_wrap_view_css_class" 
											class="eighteen" 
											value="" />
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_model_name">
											<?php echo Services::Language()->translate('menuitem_model_name'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="menuitem_model_name" id="menuitem_model_name">
											<option value="Content" selected="selected"><?php echo Services::Language()->translate('Content'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_model_type">
											<?php echo Services::Language()->translate('menuitem_model_type'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="menuitem_model_type" id="menuitem_model_type">
											<option value="Menuitem" selected="selected"><?php echo Services::Language()->translate('Menuitem'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

								<div class="row">
									<div class="five columns">
										<label class="inline" for="menuitem_model_query_object">
											<?php echo Services::Language()->translate('menuitem_model_query_object'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="menuitem_model_query_object" id="menuitem_model_query_object">
											<option value="List" selected="selected"><?php echo Services::Language()->translate('List'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>


									<!-- <ul class="block-grid two-up">
									<li>

										<h6><?php echo Services::Language()->translate('List View'); ?></h6>
										<dl>
											<dd>
											    "list_theme_id":"9050",
											    "list_page_view_id":"220",
											    "list_page_view_css_id":"",
											    "list_page_view_css_class":"",
											    "list_template_view_id":"1050",
											    "list_template_view_css_id":"",
											    "list_template_view_css_class":"",
											    "list_wrap_view_id":"2030",
											    "list_wrap_view_css_id":"",
											    "list_wrap_view_css_class":"",
											    "list_model_name":"Content",
											    "list_model_type":"Table",
											    "list_model_query_object":"List",
											    "list_model_ordering":"start_publishing_datetime",
											    "list_model_direction":"DESC",
											    "list_model_offset":"0",
											    "list_model_count":"5",

											    "list_select_archived_content":"1",
											    "list_select_featured_content":"1",
											    "list_select_stickied_content":"1",
											    "list_select_published_date_begin":"",
											    "list_select_published_date_end":"",
											    "list_select_category_list":"0",
											    "list_select_tag_list":"0",
											    "list_select_author_list":"0",
						    				</dd>
						    			</dl>

									</li>
									<li>

										<h6><?php echo Services::Language()->translate('Item View'); ?></h6>
										<dl class="content">
											<dd>
											    "item_theme_id":"9050",
											    "item_page_view_id":"200",
											    "item_page_view_css_id":"",
											    "item_page_view_css_class":"",
											    "item_template_view_id":"1130",
											    "item_template_view_css_id":"",
											    "item_template_view_css_class":"",
											    "item_wrap_view_id":"2030",
											    "item_wrap_view_css_id":"",
											    "item_wrap_view_css_class":"",
											    "item_model_name":"Content",
											    "item_model_type":"Table",
											    "item_model_query_object":"Item",
											</dd>
										</dl>

									</li>
									<li>

										<h6><?php echo Services::Language()->translate('Edit Views'); ?></h6>
										<dl class="content">
											<dd>
											    "form_theme_id":"9050",
											    "form_page_view_id":"210",
											    "form_page_view_css_id":"",
											    "form_page_view_css_class":"",
											    "form_template_view_id":"1260",
											    "form_template_view_css_id":"",
											    "form_template_view_css_class":"",
											    "form_wrap_view_id":"2030",
											    "form_wrap_view_css_id":"",
											    "form_wrap_view_css_class":"",
											    "form_model_name":"Content",
											    "form_model_type":"Table",
											    "form_model_query_object":"Item",
											</dd>
										</dl>
										
									</li>
									<li>

										<h6><?php echo Services::Language()->translate('Menu Item'); ?></h6>
										<dl class="content">
											<dd></dd>
										</dl>
										
									</li>
									<li>

										<h6><?php echo Services::Language()->translate('Menu Item'); ?></h6>
										<dl class="content">
											<dd></dd>
										</dl>
										
									</li>
									<li>

										<h6><?php echo Services::Language()->translate('Menu Item'); ?></h6>
										<dl class="content">
											<dd></dd>
										</dl>
										
									</li>
								</ul> -->


							</div>
							
						</li>
						<li>
							<h5 class="title"><?php echo Services::Language()->translate('Mustache'); ?></h5>
							<div class="content">

								<div class="row">
									<div class="five columns">
										<label class="inline" for="mustache">
											<?php echo Services::Language()->translate('mustache'); ?>
										</label>
									</div>
									<div class="twelve columns">
										<select name="mustache" id="mustache">
											<option value="1" selected="selected"><?php echo Services::Language()->translate('yes'); ?></option>
											<option value="0"><?php echo Services::Language()->translate('no'); ?></option>
										</select>
									</div>
									<div class="one column">
										<span class="has-tip tip-left form-helper" data-width="250" title="Tooltip info"><i>`</i><small><?php echo Services::Language()->translate('More info&hellip;'); ?></small></span>
									</div>
								</div>

							</div>
						</li>
					</ul>

				</li>
			</ul>

		</form>
		<a class="close-reveal-modal">&#215;</a>
	</div>

	<div id="application-options" class="reveal-modal">
		hellp
		<a class="close-reveal-modal">&#215;</a>
	</div>



