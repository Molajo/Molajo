<?php

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$listname = 'list_' . $this->row->listname . '*'; ?>
    <li class="filter">
        <include:template name=formselectlist wrap=div wrap_class=filter value=<?php echo $listname; ?>/>
    </li>
