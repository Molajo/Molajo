<?php
/**
 *
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
?>
<div class="row">
    <div class="twelve columns">
        <?php echo $this->row->content_text; ?>
    </div>
</div>

<div class="row">
    <div class="twelve columns">
        <include:template name=Author/>
    </div>
</div>
