<?php

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$listname = $row->listname;
if (count($listname) > 0) {
foreach ($listname as $list) { ?>
<li>
    <include:template name=formselectlist wrap=none datalist=<?php echo $list; ?>/>
</li>
<?php }
}
