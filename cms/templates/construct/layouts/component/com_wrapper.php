<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// To enable use of site configuration
$app 					= JFactory::getApplication();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />
  <style type="text/css">
  #wrapper-header {padding:5px 0 5px 15px;border-bottom:3px solid #ff0;border-top:3px solid #ccc;background:#000;color:#fff;text-align:center; text-transform:capitalize;font-family:"Lucida Grande",Lucida,Verdana,sans-serif}
  #wrapper-header a{color:#fff;text-decoration:none}
  #wrapper-header a:hover{text-decoration:underline}
  </style>
</head>

<body>
	<div id="wrapper-header">
		Return To <a href="<?php echo $this->baseurl ?>/" title="<?php echo $app->getCfg('sitename');?>"><?php echo $app->getCfg('sitename');?></a>
	</div>
	<jdoc:include type="component" />
</body>
</html>