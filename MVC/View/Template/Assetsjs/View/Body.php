<?php
/**
 * Assetsjs Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */

defined('MOLAJO') or die;

$application_html5 = $this->row->application_html5;
$end               = $this->row->end;
?>
<script src="<?php echo $this->row->url; ?>"<?php if ((int)$application_html5 == 0): ?>
        type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ((int)$this->row->defer == 1): ?>
        defer="defer"<?php endif; ?><?php if ((int)$this->row->async == 1): ?>
        async="async"<?php endif; ?>></script><?php echo chr(10);
