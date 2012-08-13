<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */

defined('MOLAJO') or die;

$class = Services::Registry()->get('Parameters', 'page_view_css_class');
if (trim($class) == '') {
} else {
	$class = ' class="' . htmlspecialchars($class) . '"';
}
$class = ' class="' . htmlspecialchars('dashboard') . '"';
$id = Services::Registry()->get('Parameters', 'page_view_css_id');
if (trim($id) == '') {
} else {
	$id = ' id="' . htmlspecialchars($id) . '"';
}

$html5 = $this->query_results[0]->html5;
$end = $this->query_results[0]->end;
if ((int)$html5 == 1): ?>
<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]>
<html
	class="no-js lt-ie9 lt-ie8 lt-ie7"<?php echo $this->query_results[0]->language_direction; ?> lang="<?php echo $this->query_results[0]->language; ?>"> <![endif]-->
<!--[if IE 7]>
<html
	class="no-js lt-ie9 lt-ie8"<?php echo $this->query_results[0]->language_direction; ?> lang="<?php echo $this->query_results[0]->language; ?>"> <![endif]-->
<!--[if IE 8]>
<html
	class="no-js lt-ie9"<?php echo $this->query_results[0]->language_direction; ?> lang="<?php echo $this->query_results[0]->language; ?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html<?php echo $this->query_results[0]->language_direction; ?>
	lang="<?php echo $this->query_results[0]->language; ?>"> <!--<![endif]-->
	<?php else : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
>
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo $this->query_results[0]->language_direction; ?>
	  lang="<?php echo $this->query_results[0]->language; ?>">
<?php endif; ?>
<head>
	<title><?php echo $this->query_results[0]->title; ?></title>
	<base href="<?php echo $this->query_results[0]->base_url . '"' . $end; ?>
<?php if ((int)$html5 == 1): ?>
    <meta charset="utf-8"<?php echo $end; ?>
	<?php else : ?>
    <meta http-equiv="Content-Type"
		  content="<?php echo $this->query_results[0]->mimetype; ?>; charset=utf-8"<?php echo $end; ?>
    <?php endif; ?>
	<include:asset name=Assetslinks value=Links/>
		<include:metadata name=Metadata value=Metadata/>
			<include:asset name=Assetscss value=Css/>
				<include:asset name=Assetscssdeclarations value=CssDeclarations/>
					<include:asset name=Assetsjs value=Js/>
						<include:asset name=Assetsjsdeclarations value=JsDeclarations/>
							<?php // include __DIR__ . '/alohahead.php'; ?>
</head>
<body<?php echo $id; ?><?php echo $class; ?>>
