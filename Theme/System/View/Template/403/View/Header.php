<?php
/**
 * 403 Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die; ?>
<div class="row">
    <div class="one columns show-for-small">
        <form action="<?php echo $this->row->catalog_id_url ?>/edit">
            <input type="submit" class="submit button medium" value="Edit">
        </form>
    </div>
    <div class="ten columns">
        <h2><?php echo $this->row->title; ?></h2>
    </div>
    <div class="one columns hide-for-small">
        <form action="<?php echo $this->row->catalog_id_url ?>/edit">
            <input type="submit" class="submit button medium" value="Edit">
        </form>
    </div>
</div>
