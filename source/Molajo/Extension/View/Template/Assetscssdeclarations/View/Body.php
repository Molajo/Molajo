<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$html5 = $this->row->html5;
$end = $this->row->end;
?>
	<style<?php if ((int) $html5 == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php echo $end; ?>
<?php if ($this->row->page_mime_type == 'text/html') :
else : ?>
<![CDATA[
<?php endif;
echo $this->row->content . chr(10);
if ($this->row->page_mime_type == 'text/html') :
else : ?>
]]>
<?php
endif; ?>
</style><?php echo chr(10); ?>
