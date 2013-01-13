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
    <div class="twelve columns">
        <?php echo $this->row->content_text; ?>
    </div>
</div>

<div class="row">
    <div class="twelve columns">
        <include:template name=Author/>
    </div>
</div>
