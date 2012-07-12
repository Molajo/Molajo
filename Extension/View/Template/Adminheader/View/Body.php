<?php
use Molajo\Service\Services;

/**
 * @package       Molajo
 * @copyright     2012 Amy Stephen. All rights reserved.
 * @license       GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$title = Services::Registry()->get('Triggerdata', 'AdminTitle');
if ($title == '') {
    $title = $this->row->criteria_title;
} else {
    $title .= '-' . $this->row->criteria_title;
}
$homeURL = Services::Registry()->get('Configuration', 'application_base_url');
?>
<h1 id="site-title"><a href="<?php echo $homeURL; ?>"><?php echo $title; ?></a></h1>
