<?php
/**
 * Application Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;
?>
$template   = ucfirst(strtolower($this->row->fieldset_template_view));
$parameters = $this->row->fieldset_template_view_parameter; ?>
<include:template name=<?php echo $template; ?> parameter=<?php echo $parameters; ?> wrap=none/>
