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
	$class = '';
} else {
	$class = ' class="' . htmlspecialchars($class) . '"';
}

$id = Services::Registry()->get('Parameters', 'page_view_css_id');
if (trim($id) == '') {
	$id = '';
} else {
	$id = ' id="' . htmlspecialchars($id) . '"';
}

$html5 = $this->query_results[0]->html5;
$end = $this->query_results[0]->end;
if ((int)$html5 == 1): ?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"<?php echo $this->query_results[0]->language_direction; ?> lang="<?php echo $this->query_results[0]->language; ?>"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"<?php echo $this->query_results[0]->language_direction; ?> lang="<?php echo $this->query_results[0]->language; ?>"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"<?php echo $this->query_results[0]->language_direction; ?> lang="<?php echo $this->query_results[0]->language; ?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html<?php echo trim($this->query_results[0]->language_direction); ?> lang="<?php echo $this->query_results[0]->language; ?>"> <!--<![endif]-->
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
<include:asset name=Assetslinks/>
<include:metadata name=Metadata/>
<include:asset name=Assetscss/>
<include:asset name=Assetscssdeclarations/>
<include:asset name=Assetsjs/>
<include:asset name=Assetsjsdeclarations/>
	<table border = "0">
		<tr>
			<td nowrap><font face="monospace, Courier" size = "1" color = "000000">MMMMM$777ONMNZ777DMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM<br>
				MMM$77MMMN7DMMMMM77MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM<br>
				MMI7MMMM8DD7M$MMMMM77MMMMMMMMMMMMMMMMMMMMMMMMMMMMMM77MMMMMMMMMMD7MMMMMMMMMMMMMMM<br>
				M7OMMMM77$77$77MMMMM77MMMMMMMMMMMMMMMMMMMMMMMMMMMMD77MMMMMMMMMM77$MMMMMMMMMMMMMM<br>
				77MMM$ZMN77$7$MD$MMMM7OMMMMMMMMNDNMMNNMMMMMMNDMMMMD77MMMNDDMMMMMMMMMMMDNMMMMO8OZ<br>
				7MMM7877Z7$77777O7MMM$7MMMMMM87777777777MM777777ZMD77MMM77777MM77OMN777777MMMMMM<br>
				7MMZ777OO7MMZZO77777ND7MMMMMM877MM77MM77MM77MMM77MD77MMMMDD77OM77OM77MMM77NMMMMM<br>
				7M$MMZ77$$$$77778O7NMD7MMMMMM877MM77MM77MM77MMM77MD77MM777O77OM77OM77MMM778MMMMM<br>
				7MMMZ77DZZOO77D777MMM77MMMMMM877MM77MM77MM777M777MM77OM77ON77OM77OM777M777MMMMMM<br>
				77MMMMMO$7777$8MMMMMM78MMMMMM8$$MM$$MM$$MMMD777NMMMN77MM$777$DM77OMMD777OMMMMMMM<br>
				M7OMMMM77M77M77MMMMM77MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM7777MMMMMMMMMMMMMMM<br>
				MM77MMMMM877ZMMMMMM77MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNMMMMMMMMMMMMMMMMM<br>
				MMM$77MMMM8$MMMMN77MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM<br>
				MMMMMZ777ZDN8$777NMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM<br>
				MMMMMMMMMO$$Z8MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM<br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GlassGiant.com </font></td>
		</tr>
	</table>
</head>
<body<?php echo $id; ?><?php echo $class; ?>>
