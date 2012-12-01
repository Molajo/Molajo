<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$application_html5 = $this->row->application_html5;
$end = $this->row->end;
?>
<script<?php if ((int) $application_html5 == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?>>
    <?php
    if ($this->row->page_mime_type == 'text/html') :
    else : ?>
    <![CDATA[
        <?php
    endif;
    echo '    ' . trim($this->row->content) . chr(10);
    if ($this->row->page_mime_type == 'text/html') :
    else : ?>
    ]]>
        <?php
    endif; ?>
</script><?php echo chr(10);
