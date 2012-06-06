<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$page_mimetype = $this->row->mimetype;
$defer = (int) Services::Registry()->get('Parameters','defer');
if ($defer == 1) {
} else {

    if ((int) Services::Registry()->get('Parameters', 'criteria_html5', 1) == 1):
        $end = '>' . chr(10); ?>
<!DOCTYPE html>
<?php else :
        $end = '/>' . chr(10); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo Services::Registry()->get('Parameters', 'language_direction'); ?>" lang="<?php echo Services::Registry()->get('Parameters', 'language_tag'); ?>">
<head>
<base href="<?php echo BASE_URL . '"' . $end; ?>
<?php if ((int) Services::Registry()->get('Parameters', 'criteria_html5', 1) == 1): ?>
    <meta charset="UTF-8"<?php echo $end; ?>
    <?php else : ?>
    <meta http-equiv="Content-Type" content="<?php echo $page_mimetype; ?>; charset=UTF-8"<?php echo $end; ?>
<?php endif; ?>
    <title><?php echo $this->row->title; ?></title>
<?php }
