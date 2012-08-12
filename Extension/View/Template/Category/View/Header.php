<?php

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
?>
<div class="row">
    <div class="eleven columns">
        <h2><?php echo $this->row->title; ?></h2>
    </div>
    <div class="one columns">
        <form action="<?php echo $this->row->catalog_id_url ?>/edit">
            <input type="submit" class="submit button small" value="Edit">
        </form>
    </div>
</div>
