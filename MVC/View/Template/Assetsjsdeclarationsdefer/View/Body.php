<?php
/**
 * Assetjsdeclarationsdefer Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die;

$application_html5 = $this->row->application_html5;
$end               = $this->row->end;
?>
<script<?php if ((int)$application_html5 == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?>>
    <?php
    if ($this->row->page_mimetype == 'text/html') :
    else : ?>
    <![CDATA[
        <?php
    endif;
    echo '    ' . trim($this->row->content) . chr(10);
    if ($this->row->page_mimetype == 'text/html') :
    else : ?>
    ]]>
        <?php
    endif; ?>
</script><?php echo chr(10);
