<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined("MOLAJO") or die;
$template = ucfirst(strtolower($this->row->fieldset_template_view));
$parameters = $this->row->fieldset_template_view_parameter; ?>
<include:template name=<?php echo $template; ?> parameter=<?php echo $parameters; ?> wrap=div wrap_id=<?php echo $parameters; ?>/>
