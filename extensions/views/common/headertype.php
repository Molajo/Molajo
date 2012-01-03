<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2012 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

?>
<<?php echo $headertype;
if ($this->rowset[0]->wrap_id == '') :
else :
    echo ' id="' . $this->rowset[0]->wrap_id . '"';
endif;

$tmpClass = '';
if (isset($this->parameters->view_class_suffix)) {
    $tmpClass = $this->parameters->view_class_suffix;
}
if (isset($this->rowset[0]->wrap_class)) {
    $tmpClass .= $this->rowset[0]->wrap_class;
}
if ($tmpClass == '') :
else :
    echo ' class="' . $tmpClass . '"';
endif;
?>
>