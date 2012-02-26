<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
if ((int)Services::Configuration()->get('html5', 1) == 1):
    $end = '>'; ?>
<!DOCTYPE html>
<?php else :
    $end = '/>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo Services::Language()->get('direction'); ?>" lang="<?php echo Services::Language()->get('tag'); ?>">
<head>
    <base href="<?php echo MOLAJO_BASE_URL.'"'.$end; ?><?php echo chr(10).chr(13); ?>
<?php if ((int) Services::Configuration()->get('html5', 1) == 1): ?>
    <meta charset="UTF-8"<?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php else : ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"<?php echo $end; ?>
<?php endif; ?>
      <title><?php echo $this->row->title; ?></title><?php echo chr(10).chr(13); ?>
<?php if (trim($this->row->title) == ''):
else : ?>
    <meta name="title" content="<?php echo $this->row->title; ?>"<?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php endif; ?>
<?php if (trim($this->row->description) == ''):
else : ?>
    <meta name="description" content="<?php echo $this->row->description; ?>"<?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php endif; ?>
<?php if (trim($this->row->keywords) == ''):
else : ?>
    <meta name="keywords" content="<?php echo $this->row->keywords; ?>"<?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php endif; ?>
<?php if (trim($this->row->author) == ''):
else : ?>
    <meta name="author" content="<?php echo $this->row->author; ?>"<?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php endif; ?>
<?php if (trim($this->row->content_rights) == ''):
else : ?>
    <meta name="content_rights" content="<?php echo $this->row->content_rights; ?>"<?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php endif; ?>
<?php if (trim($this->row->robots) == ''):
else : ?>
    <meta name="robots" content="<?php echo $this->row->robots; ?>"<?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php endif; ?>
<?php if (trim($this->row->favicon) == ''):
else : ?>
    <link rel="shortcut icon" href="<?php echo $this->row->favicon; ?>"<?php if ((int) Services::Configuration()->get('html5', 1) == 0): ?>type="image/x-icon"<?php endif;?><?php echo $end; ?><?php echo chr(10).chr(13); ?>
<?php endif; ?>
