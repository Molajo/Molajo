<?php
/**
 * Head Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;

$class = Services::Registry()->get('parameters', 'page_view_css_class');
if (trim($class) == '') {
    $class = '';
} else {
    $class = ' class="' . htmlspecialchars($class) . '"';
}

$id = Services::Registry()->get('parameters', 'page_view_css_id');
if (trim($id) == '') {
    $id = '';
} else {
    $id = ' id="' . htmlspecialchars($id) . '"';
}

$application_html5 = $this->row->application_html5;
$end               = $this->row->end;

$l = $this->row->language;
$d = $this->row->language_direction;
if ((int)$application_html5 == 1): ?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js ie oldie"<?php echo trim($d); ?> lang="<?php echo trim($l); ?>"><![endif]-->
<!--[if IE 7]>
<html class="no-js ie ie7"<?php echo trim($d); ?> lang="<?php echo trim($l); ?>"><![endif]-->
<!--[if IE 8]>
<html class="no-js ie ie8"<?php echo trim($d); ?> lang="<?php echo trim($l); ?>"><![endif]-->
<!--[if IE 9]>
<html class="no-js ie ie9"<?php echo trim($d); ?> lang="<?php echo trim($l); ?>"><![endif]-->
<!--[if gt IE 9]><!--><html class="no-js"<?php echo trim($d); ?> lang="<?php echo trim($l); ?>"><!--<![endif]-->
    <?php else : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
>
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo $this->row->language_direction; ?>
      lang="<?php echo $this->row->language; ?>">
<?php endif; ?>
<head>
    <title><?php echo $this->row->title; ?></title>
        <base href="<?php echo $this->row->base_url . '"' . $end; ?>
<?php if ((int)$application_html5 == 1): ?>
    <meta charset="utf-8"<?php echo $end; ?>
    <?php else : ?>
    <meta http-equiv="Content-Type"
          content="<?php echo $this->row->mimetype; ?>; charset=utf-8"<?php echo $end; ?>
    <?php endif; ?>
    <include:asset name=Assetslinks/>
        <include:metadata name=Metadata/>
            <include:asset name=Assetscss/>
                <include:asset name=Assetscssdeclarations/>
                    <include:asset name=Assetsjs/>
                        <include:asset name=Assetsjsdeclarations/>
</head>
<body<?php echo $id; ?><?php echo $class; ?>>
