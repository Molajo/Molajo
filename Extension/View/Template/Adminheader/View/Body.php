<?php
use Molajo\Service\Services;
/**
 * @package   	Molajo
 * @copyright 	2012 Amy Stephen. All rights reserved.
 * @license   	GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$titlex = Services::Registry()->get('Trigger', 'AdminTitle');
//if ($title == '') {
//	$title = $this->row->criteria_title;
//} else {
//	$title .= '-' . $this->row->criteria_title;
//}
$homeURL = Services::Registry()->get('Configuration', 'application_base_url');
?>
<h1 id="site-title"><a href="<?php echo $homeURL; ?>"><?php echo $titlex; ?></a></h1>
