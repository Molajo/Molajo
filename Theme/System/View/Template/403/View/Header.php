<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */

defined('NIAMBIE') or die;
?>
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
