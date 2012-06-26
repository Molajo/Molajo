<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;

$class = Services::Registry()->get('Parameters', 'page_view_css_class');
if (trim($class) == '') {
} else {
	$class = ' class="' . htmlspecialchars($class) . '"';
}
$id = Services::Registry()->get('Parameters', 'page_view_css_id');
if (trim($id) == '') {
} else {
	$id = ' id="' . htmlspecialchars($id) . '"';
}

$html5 = $this->query_results[0]->html5;
$end = $this->query_results[0]->end;
if ((int) $html5 == 1): ?>
<!DOCTYPE html>
<?php else : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html<?php if ((int) $html5 == 0) : ?> xmlns="http://www.w3.org/1999/xhtml"<?php endif; ?><?php echo $this->query_results[0]->language_direction; ?> lang="<?php echo $this->query_results[0]->language; ?>">
<head>
	<title><?php echo $this->query_results[0]->title; ?></title>
	<base href="<?php echo $this->query_results[0]->base_url . '"' . $end; ?>
<?php if ((int) $html5 == 1): ?>
    <meta charset="utf-8"<?php echo $end; ?>
<?php else : ?>
    <meta http-equiv="Content-Type" content="<?php echo $this->query_results[0]->mimetype; ?>; charset=utf-8"<?php echo $end; ?>
	<?php endif; ?>
	<include:asset name=Assetslinks wrap=None value=Links/>
	<include:metadata name=Metadata wrap=None value=Metadata/>
	<include:asset name=Assetscss wrap=None value=Css/>
	<include:asset name=Assetscssdeclarations wrap=None value=CssDeclarations/>
	<include:asset name=Assetsjs wrap=None value=Js/>
	<include:asset name=Assetsjsdeclarations wrap=None value=JsDeclarations/>
</head>
<body<?php echo $id; ?><?php echo $class; ?>>
