<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die; ?>
<div class="video-wrapper">
    <div class="video-container">
        <iframe src="<?php echo $this->row->criteria_video; ?>"
                width="<?php echo $this->row->criteria_width; ?>"
                height="<?php echo $this->row->criteria_height; ?>"
                frameborder="0">
        </iframe>
    </div>
</div>
