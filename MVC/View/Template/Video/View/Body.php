<?php
/**
 * Video Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die; ?>
<div class="video-wrapper">
    <div class="video-container">
        <iframe src="<?php echo $this->row->criteria_video; ?>"
                width="<?php echo $this->row->criteria_width; ?>"
                height="<?php echo $this->row->criteria_height; ?>"
                frameborder="0">
        </iframe>
    </div>
</div>
