<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

?>
<<?php echo $headertype; ?>
<?php if ($this->rowset[0]->wrap_id == '') :
else :
    echo ' id="' . $this->rowset[0]->wrap_id . '"';
endif;

$tmpClass = $this->parameters->get('layout_class_suffix') . $this->rowset[0]->wrap_class;
if ($tmpClass == '') :
else :
    echo ' class="' . $tmpClass . '"';
endif;
?>
>