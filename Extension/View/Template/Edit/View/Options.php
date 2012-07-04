<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="row">
	<div class="twelve columns">
		<div class="row">
			<div class="six columns">
				<fieldset>
					<legend>Publishing Options</legend>

					<label>This is a label.</label>
					<input type="text" placeholder="Standard Input"/>

					<label>Address</label>
						<input type="text"/>
						<input type="text" class="six"/>

						<label for="radio1">
							<input name="radio1" type="radio" id="radio1"> Featured
						</label>

						<label for="radio2">
							<input name="radio1" type="radio" id="radio2"> Not Featured
						</label>

				</fieldset>
			</div>
			<div class="six columns">
				<fieldset>

					<legend>Categories and Tags</legend>

					<label>This is a label.</label>
					<input type="text" placeholder="Standard Input"/>

					<label>Address</label>
					<input type="text"/>
					<input type="text" class="six"/>

				</fieldset>

			</div>
		</div>
	</div>
</div>
