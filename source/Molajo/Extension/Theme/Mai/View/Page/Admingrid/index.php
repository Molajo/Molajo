<?php
use Molajo\Service\Services;
/**
 * @package     Molajo
 * @copyright   2012 Babs Gösgens. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$pageUri = $_SERVER['REQUEST_URI'];
?>

<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>
<a href="<?php echo Services::Registry()->get('Triggerdata', 'full_page_url'); ?>#Filters"><?php echo Services::Language()->translate('Filters'); ?></a>

<include:request/>
