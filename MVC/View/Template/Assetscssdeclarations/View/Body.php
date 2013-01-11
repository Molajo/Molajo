<?php
/**
 * Assetcssdeclarations Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;
?>
$application_html5 = $this->row->application_html5;
$end               = $this->row->end; ?>
        <style<?php if ((int)$application_html5 == 0): ?>
        type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php echo $end; ?>
<?php if ($this->row->page_mimetype == 'text/html') :
else : ?>
<![CDATA[
<?php endif;
echo $this->row->content . chr(10);
if ($this->row->page_mimetype == 'text/html') :
else : ?>
]]>
<?php
endif; ?>
</style><?php echo chr(10);
