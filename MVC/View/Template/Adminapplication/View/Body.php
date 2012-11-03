<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<include:template name=formbegin form_name=configuration/>
<include:ui name=navigationtab tab_array=<?php echo $this->row->tab_array; ?>/>
<include:template name=formend/>
