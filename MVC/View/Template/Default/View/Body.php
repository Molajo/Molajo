<?php

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die; ?>
<h2>
<?php if (isset($this->row->title)) {
    echo $this->row->title;
}?>
</h2>
<?php
if (isset($this->row->text)) {
    echo $this->row->text;
}
