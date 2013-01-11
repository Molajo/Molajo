<?php
/**
 * 404 Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die; ?>
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
