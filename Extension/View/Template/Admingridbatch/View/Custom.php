<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

$action = Services::Registry()->get('Trigger', 'PageURL');
?>
<form action="<?php echo $action; ?>" xmlns="http://www.w3.org/1999/html" method="post" name="Admingridfilters">
	<section class="row">

		<section class="eight columns">
			<ul class="tabs-content vertical">
				<li class="active" id="batchStatusTab">
					<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
						<div class="panel">
							<h4>Status</h4>

							<p>Set the status of items selected above with the status specified below.

							<include:template name=Formselect value=<?php echo 'gridbatch_status*'; ?>/>

							<input type="submit" class="submit button small" name="submit" id="action"
								   value="Apply">
							</p>
						</div>
					</form>
				</li>

				<li id="batchCategoriesTab">
					<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
						<div class="panel">
							<h4>Categories</h4>

							<p>Associate the items selected with the specified category(ies).

							<include:template name=Formselect value=<?php echo 'gridbatch_categories*'; ?>/>

							<input type="submit" class="submit button small" name="submit" id="batch-category-create"
								   value="Add">
							<input type="submit" class="submit button small" name="submit" id="batch-category-delete"
								   value="Remove">
							</p>
						</div>
					</form>
				</li>

				<li id="batchTagsTab">
					<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
						<div class="panel">
							<h4>Tags</h4>

							<p>Associate the items selected with the specified tags(s).

							<include:template name=Formselect wrap=div wrap-class=filter value=<?php echo 'gridbatch_tags*'; ?>/>

							<input type="text" name="tag" id="tag">

							<input type="submit" class="submit button small" name="submit" id="batch-tag-create"
								   value="Add">
							<input type="submit" class="submit button small" name="submit" id="batch-tag-delete"
								   value="Remove">
							</p>
						</div>
					</form>
				</li>

				<li id="batchPermissionsTab">
					<form action="<?php echo $action; ?>" method="post" name="Admingridfilters">
						<div class="panel">

							<h4>Permissions</h4>

							<p>Associate the items selected with the specified group(s) and permission(s).

							<include:template name=Formselect value=<?php echo 'gridbatch_groups*'; ?>/>

								<ul class="permissions">
									<li>
										<label for="permission-create">
											<input name="radio1" type="radio" id="permission-create">
											<span class="custom radio"></span> Create
										</label>
									</li>
									<li>
										<label for="permission-read">
											<input name="radio1" type="radio" selected id="permission-read">
											<span class="custom radio"></span> Read
										</label>
									</li>
									<li>
										<label for="permission-update">
											<input name="radio1" type="radio" id="permission-update">
											<span class="custom radio"></span> Update
										</label>
									</li>
									<li>
										<label for="permission-delete">
											<input name="radio1" type="radio" id="permission-delete">
											<span class="custom radio"></span> Delete
										</label>
									</li>
									<li>
										<label for="permission-all">
											<input name="radio1" type="radio" id="permission-all">
											<span class="custom radio"></span> All
										</label>
									</li>
								</ul>
							<br /><br />

							<input type="submit" class="submit button small" name="submit" id="batch-permission-create"
								   value="Add">
							<input type="submit" class="submit button small" name="submit" id="batch-permission-delete"
								   value="Remove">

							</p>
						</form>
					</div>
				</li>
			</ul>
		</section>

		<section class="three columns">
			<dl class="nice vertical tabs">
				<dd><a href="<?php echo $action; ?>#batchStatus" class="active">Status</a></dd>
				<dd><a href="<?php echo $action; ?>#batchCategories">Categories</a></dd>
				<dd><a href="<?php echo $action; ?>#batchTags">Tags</a></dd>
				<dd><a href="<?php echo $action; ?>#batchPermissions">Permissions</a></dd>
			</dl>
		</section>

		<section class="one columns"></section>

	</section>
</form>
