<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$page_mimetype = $this->row->mimetype;
$defer = (int) $this->parameters->get('defer');
if ($defer == 1) {
} else {

if ((int)Services::Configuration()->get('html5', 1) == 1):
    $end = '>'. chr(10) . chr(13); ?>
<!DOCTYPE html>
<?php else :
    $end = '/>'. chr(10) . chr(13); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo Services::Language()->get('direction'); ?>" lang="<?php echo Services::Language()->get('tag'); ?>">
<head>
    <base href="<?php echo MOLAJO_BASE_URL . '"' . $end; ?>
<?php if ((int)Services::Configuration()->get('html5', 1) == 1): ?>
    <meta charset="UTF-8"<?php echo $end; ?>
    <?php else : ?>
    <meta http-equiv="Content-Type" content="<?php echo $page_mimetype; ?>; charset=UTF-8"<?php echo $end; ?>
<?php endif; ?>
    <title><?php echo $this->row->title; ?></title>
<?php }
