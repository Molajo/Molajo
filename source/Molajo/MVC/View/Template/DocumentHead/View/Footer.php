<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$defer = (int)$this->parameters->get('defer');
if ($defer == 1) {
	?>
</body>
</html>
<?php
} else {
	?>
</head>
<?php
	$bodyClassSuffix = $this->parameters->get('body_class_suffix');
	if (trim($this->parameters->get('body_class_suffix', '')) == '') {
		$bodyElement = '<body>';
	} else {
		$bodyElement = '<body ' . ' class="' . htmlspecialchars($this->parameters->get('body_class_suffix')) . '">';
	}
	echo $bodyElement;
}
