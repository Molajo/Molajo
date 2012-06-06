<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die; ?>
<script<?php if ((int) Services::Registry()->get('Parameters', 'criteria_html5', 1) == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?>>
	<?php
	if ($page_mimetype == 'text/html') :
	else : ?>
	<![CDATA[
		<?php
	endif;
	echo '    ' . trim($this->row->content) . chr(10);
	if ($page_mimetype == 'text/html') :
	else : ?>
	]]>
		<?php
	endif; ?>
</script><?php echo chr(10); ?>
