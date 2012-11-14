<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die;
$template = ucfirst(strtolower($this->row->page_form_fieldset_handler_view));
$parameters = $this->row->page_include_parameter; ?>
<include:template name=<?php echo $template; ?> parameter=<?php echo $parameters; ?> wrap=div wrap_id=<?php echo $parameters; ?>/>
