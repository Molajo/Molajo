<?php
/**
 * @version     $id: item_footer.php
 * @package     Molajo
 * @subpackage  Latest News Layout
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<table class="adminlist">
    <thead>
    <tr>
<?php
if (count($this->rowset[0]->columncount) > 0) :
    for ($i = 1; $i < count($this->rowset[0]->columncount + 1); $i++) {
        echo '<th>' . $this->rowset[0]->columnheading . $i . '</th>';
    }
endif; ?>
    </tr>
    </thead>