<?php
/**
 * Assetscss Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die;

$application_html5 = $this->row->application_html5;
$end               = $this->row->end;

if ($this->row->conditional == '' || $this->row->conditional === null) {
    $begin_conditional = '';
    $end_conditional   = '';
} else {
    if ((int)$application_html5 == 1) {
        $end = '>';
    } else {
        $end = '/>';
    }
    $begin_conditional = '<!--[' . $this->row->conditional . ' ]>';
    $end_conditional   = '<![endif]-->' . chr(10);
}
?>
<?php echo $begin_conditional; ?>
        <link rel="stylesheet"
              href="<?php echo $this->row->url; ?>"<?php if ((int)$this->row->application_html5 == 0): ?>
              type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ($this->row->media != null): ?>
              media="<?php echo $this->row->media; ?>"<?php endif; ?><?php if (trim(
    $this->row->attributes
) != ''
): ?><?php echo $this->row->attributes; ?><?php endif; ?><?php echo $end; ?><?php echo $end_conditional;
