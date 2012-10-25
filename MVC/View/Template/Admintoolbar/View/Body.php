<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<!-- custom pull down button -->
<div class="split-dropdown">
    <button name="save" class="action"><i class="icon-save"></i>Save</button>
    <ul class="dropdown-list">
        <li>
            <span class="selector icon-caret-down"></span>
            <ul>
                <li><button name="save-as" class="secondary"><i class="icon-copy"></i>Save as&hellip;</button></li>
                <li><button name="revert-to" class="secondary"><i class="icon-time"></i>Revert to&hellip;</button></li>
                <li><button name="trash" class="secondary"><i class="icon-trash"></i>Trash</button></li>
            </ul>
        </li>
    </ul>
</div>
