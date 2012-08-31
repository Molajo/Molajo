<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
?>
<h1 class="editable"><?php echo $this->row->title; ?></h1>
<include:ui name=button button_tag=button button_size=small button_type=secondary button_shape=radius button_icon_prepend=icon-wrench button_title=<?php echo str_replace(' ', '&nbsp;', htmlentities('Options', ENT_COMPAT, 'UTF-8')); ?>/>

<div class="editable"><?php echo $this->row->content_text; ?></div>
<include:ui name=button button_tag=button button_size=small button_type=secondary button_shape=radius button_icon_prepend=icon-wrench button_title=<?php echo str_replace(' ', '&nbsp;', htmlentities('Options', ENT_COMPAT, 'UTF-8')); ?>/>
