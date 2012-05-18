<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$defer = (int)Services::Registry()->get('Parameters', 'defer');
if ($defer == 1) {
	?>
</body>
</html>
<?php
} else {
	?>
</head>
<?php
	$bodyClassSuffix = Services::Registry()->get('Parameters', 'body_class_suffix');
	if (trim(Services::Registry()->get('Parameters', 'body_class_suffix', '')) == '') {
		$bodyElement = '<body>';
	} else {
		$bodyElement = '<body ' . ' class="' . htmlspecialchars(Services::Registry()->get('Parameters', 'body_class_suffix')) . '">';
	}
	echo $bodyElement;
}
