<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die;
$template = ucfirst(strtolower($this->row->fieldset_template_view));
$parameters = $this->row->fieldset_template_view_parameter; ?>
<include:template name=<?php echo $template; ?> parameter=<?php echo $parameters; ?> wrap=div wrap_id=<?php echo $parameters; ?>/>
