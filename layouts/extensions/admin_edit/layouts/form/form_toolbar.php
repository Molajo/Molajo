<?php
/**
 * @version     $id: form_toolbar.php
 * @package     Molajo
 * @subpackage  Display Toolbar
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$this->toolbar = MolajoToolbar::getInstance('toolbar')->render('toolbar');
?>
<div class="clr"></div>
<?php echo $this->toolbar; ?>
<div class="clr"></div>