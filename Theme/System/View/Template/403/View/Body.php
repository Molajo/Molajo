<?php
/**
 *
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;
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
