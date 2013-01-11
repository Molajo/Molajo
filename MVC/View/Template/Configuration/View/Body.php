<?php
/**
 * Configuration Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;
$template   = ucfirst(strtolower($this->row->fieldset_template_view));
$parameters = $this->row->fieldset_template_view_parameter; ?>
<include:template name=<?php echo $template; ?> parameter=<?php echo $parameters; ?> wrap=none/>
