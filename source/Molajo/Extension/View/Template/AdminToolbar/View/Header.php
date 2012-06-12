<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
Use Molajo\Service\Services;
echo '<pre>';
$parms = $this->get('parameters');
var_dump($parms);
echo '</pre>';
defined('MOLAJO') or die; ?>
<ul role="toolbar">
    <li><strong><?php echo $this->get('extension_title'); ?></strong></li>
