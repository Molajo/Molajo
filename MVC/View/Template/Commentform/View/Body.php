<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;
if ($this->row->closed == 1) {
    return;
} ?>
<form>
    <fieldset>
        <legend>Have something to add to the discussion?</legend>
        <div>
                <label class="right inline" for="id1">Name:</label>
                <input id="id1" type="text" placeholder="First and Last Name"/>
        </div>
        <div>
                <label class="right inline" for="id2">Email:</label>
                <input id="id2" type="text" placeholder="Your email address will not be published."/>
        </div>
        <div>
                <label class="right inline" for="id3">Website:</label>
                <input id="id3" type="text" placeholder="Website will be shared with site visitors." />
        </div>
        <div>
                <label class="right inline" for="id4">Comment:</label>
                <textarea id="id4" placeholder="Your response..."></textarea>
        </div>
    </fieldset>
</form>
