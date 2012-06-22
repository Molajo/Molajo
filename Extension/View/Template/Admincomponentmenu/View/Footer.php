<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
use Molajo\Service\Services;
?>
</dl>
<button><?php echo Services::Language()->translate('BATCH'); ?></button>
	<p>I am in a paragraph.</p>
<div id="Filter">
	<include:template name=Admingridfilters/>
</div>
<div id="Batch">
	<include:template name=Admingridbatch/>
</div>
