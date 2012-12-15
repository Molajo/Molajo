<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
