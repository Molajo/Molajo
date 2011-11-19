<?php
/**
 * @package     Molajo
 * @subpackage  Molajito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$document = MolajoFactory::getDocument();
$document->template = 'molajito';           //todo: amy fix
$lang = MolajoFactory::getLanguage();

$document->addStyleSheet($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/css/jquery.ui.all.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/css/custom.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addScript($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/js/jquery-1.6.2.js');
$document->addScript($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/js/jquery-ui-1.8.15.custom.js');
$document->addScript($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/js/scripts.js');

if (MolajoFactory::getApplication()->getConfig('html5', true)): ?>
<!DOCTYPE html>
<?php else: ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MOLAJO_BASE_FOLDER; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
    <doc:include type="head" />
</head>
<body>
	<div class="container">
        <doc:include type="modules" name="header" wrap="header" />
        <doc:include type="message" />
        <section>
            <?php if (MolajoFactory::getUser()->id == 0) :
            else : ?>
                <doc:include type="modules" name="menu" wrap="none" />
            <?php endif; ?>
            <doc:include type="component" />
        </section>
        <doc:include type="modules" name="footer" wrap="footer" />
    </div>
<doc:include type="modules" name="debug" />
</body>
<noscript>
    <?php echo MolajoText::_('JGLOBAL_WARNJAVASCRIPT') ?>
</noscript>
</html>