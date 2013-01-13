<?php
/**
 * User Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die; ?>
<h1><?php echo $this->row->username; ?></h1>

<p><?php echo '<strong>' . Services::Languages()->translate(
    'First Name'
) . ':</strong>: ' . $this->row->full_name; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate(
    'Last Name'
) . ':</strong>: ' . $this->row->full_name; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate(
    'Full Name'
) . ':</strong>: ' . $this->row->full_name; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate('Alias') . ':</strong>: ' . $this->row->full_name; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate('Text') . ':</strong>:' ?></p>
<?php echo $this->row->content_text; ?>
<p><?php echo '<strong>' . Services::Languages()->translate('Email') . ':</strong>: ' . $this->row->email; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate('Blocked') . ':</strong>: ' . $this->row->block; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate(
    'Registered'
) . ':</strong>: ' . $this->row->register_datetime; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate(
    'Activated'
) . ':</strong>: ' . $this->row->register_datetime; ?></p>
<p><?php echo '<strong>' . Services::Languages()->translate(
    'Last Visited'
) . ':</strong>: ' . $this->row->last_visit_datetime; ?></p>
