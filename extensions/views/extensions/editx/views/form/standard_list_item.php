<?php
/**
 * @version     $id: view
 * @package     Molajo
 * @subpackage  Single View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<li>
    <?php echo $this->form->getLabel($this->tempColumnName) .
               $this->form->getInput($this->tempColumnName); ?>
</li>