<?php
/**
 * Fieldset Wrap View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;  ?>
<fieldset>
<?php if ($this->parameters['criteria_title'] == '') {
} else {
    ?>
    <legend><?php echo $this->parameters['criteria_title']; ?></legend>
<?php }
