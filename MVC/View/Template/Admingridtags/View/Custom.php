<?php

/**
 *
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="grid-filters">
    <ol class="batch">
        <li><include:template name=formselectlist datalist=tags/></li>
        <li><input type="submit" class="submit button small" name="submit" id="AssignTags" value="Assign"></li>
        <li><input type="submit" class="submit button small" name="submit" id="RemoveTags" value="Remove"></li>
    </ol>
</div>
