<?php
/**
 * @package     Molajo
 * @subpackage  Head
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
if ((int)MolajoController::getApplication()->get('html5', 1) == 1): ?>
<!DOCTYPE html>
<?php else : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo MolajoController::getApplication()->getDirection(); ?>" lang="<?php echo MolajoController::getApplication()->getLanguage()->getDefault(); ?>">
<head>
    <base href="<?php echo $this->row->base; ?>" />
<?php if ((int)MolajoController::getApplication()->get('html5', 1) == 1): ?>
    <meta charset="UTF-8" />
<?php else : ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php endif; ?>
    <title><?php echo $this->row->title; ?></title>
<?php if (trim($this->row->description) == ''):
else : ?>
    <meta name="description" content="<?php echo $this->row->description; ?>" />
<?php endif; ?>
<?php if (trim($this->row->robots) == ''):
else : ?>
    <meta name="robots" content="<?php echo $this->row->robots; ?>" />
<?php endif; ?>
<?php if (trim($this->row->keywords) == ''):
else : ?>
    <meta name="keywords" content="<?php echo $this->row->keywords; ?>" />
<?php endif; ?>
<?php if (trim($this->row->generator) == ''):
else : ?>
    <meta name="generator" content="<?php echo $this->row->generator; ?>" />
<?php endif; ?>
<?php if (trim($this->row->favicon) == ''):
else : ?>
    <link rel="shortcut icon" href="<?php echo $this->row->favicon; ?>" <?php if ((int)MolajoController::getApplication()->get('html5', 1) == 0): ?>type="image/x-icon" <?php endif;?>/>
<?php endif; ?>
