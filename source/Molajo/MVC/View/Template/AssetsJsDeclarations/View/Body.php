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
<script<?php if ((int) $html5 == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?>>
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
</script><?php echo chr(10); ?>
