<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die;
$template = ucfirst(strtolower($this->query_results[0]->page_form_fieldset_handler_view));
$parameters = $this->query_results[0]->page_include_parameter; ?>
<include:template name=<?php echo $template; ?> parameter=<?php echo $parameters; ?> wrap=none/>
