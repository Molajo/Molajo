<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$document = MolajoFactory::getDocument();
$browser = JBrowser::getInstance();

$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/system.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/template.css', $type='text/css', $media=null, $attribs=array(), $priority=999);

if ($this->direction == 'rtl') :
    $document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/template_rtl.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
endif;

if ($browser->getBrowser()=='msie') {
    if ($browser->getMajor() <= 7) {
        $document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/ie7.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
    } else {
        $document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/ie8.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
    }
}

if ($this->params->get('useRoundedCorners')) :
    $document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/rounded.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
else :
    $document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/norounded.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
endif;

if ($this->params->get('textBig')) :
    $document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/textbig.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
endif;

if ($this->params->get('highContrast')) :
    $document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/highcontrast.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
endif;