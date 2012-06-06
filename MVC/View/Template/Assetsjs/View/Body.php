<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$html5 = $this->row->html5;
$end = $this->row->end;
?>
<script src="<?php echo $this->row->url; ?>"<?php if ((int) $html5 == 0): ?> type="<?php echo $this->row->mimetype; ?>"<?php endif; ?><?php if ((int) $this->row->defer == 1): ?> defer="defer"<?php endif; ?><?php if ((int) $this->row->async == 1): ?> async="async"<?php endif; ?>></script><?php echo chr(10); ?>
