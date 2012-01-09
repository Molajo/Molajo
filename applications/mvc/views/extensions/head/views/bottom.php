<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
</head><?php
$bodyClassSuffix = $this->parameters->get('body_class_suffix');
if (trim($this->parameters->get('body_class_suffix', '')) == '') {
    $bodyElement = '<body>';
} else {
    $bodyElement = '<body '.' class="'.htmlspecialchars($this->parameters->get('body_class_suffix')).'">';
}
echo $bodyElement;
