<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$application_html5 = $this->row->application_html5;
$end = $this->row->end;

if ($this->row->conditional == '' || $this->row->conditional === null) {
    $begin_conditional = '';
    $end_conditional = '';
} else {
    if ((int) $application_html5 == 1) {
        $end = '>';
    } else {
        $end = '/>';
    }
    $begin_conditional = '<!--[' . $this->row->conditional . ' ]>';
    $end_conditional = '<![endif]-->' . chr(10);
}
?>
<?php echo $begin_conditional; ?>
    <link rel="stylesheet" href="<?php echo $this->row->url; ?>"<?php if ((int) $this->row->application_html5 == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ($this->row->media != null): ?> media="<?php echo $this->row->media; ?>"<?php endif; ?><?php if (trim($this->row->attributes) != ''): ?><?php echo $this->row->attributes; ?><?php endif; ?><?php echo $end; ?><?php echo $end_conditional;
