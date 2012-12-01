<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$application_html5 = $this->row->application_html5;
$end = $this->row->end;
if (trim($this->row->content) == '') {
} elseif ($this->row->name == 'mimetype' && (int) $this->row->application_html5 === 1) {
} else { ?>
    <meta <?php echo $this->row->label; ?>="<?php echo $this->row->name; ?>" content="<?php echo $this->row->content; ?>"<?php echo $end;
}
