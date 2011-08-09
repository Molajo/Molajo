<?php
/**
 * @package     Molajo
 * @subpackage  Wrap
 * @copyright   Copyright (C)  2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

static $css=false;

if ($css) {
} else {
    $css=true;
    jimport('joomla.environment.browser');
    $this->document = MolajoFactory::getDocument();
    $browser = JBrowser::getInstance();

    $this->document->addStyleDeclaration(".mod-preview-info { padding: 2px 4px 2px 4px; border: 1px solid black; position: absolute; background-color: white; color: red;}");
    $this->document->addStyleDeclaration(".mod-preview-wrapper { background-color:#eee; border: 1px dotted black; color:#700;}");
    if ($browser->getBrowser()=='msie') {
        if ($browser->getMajor() <= 7) {
            $this->document->addStyleDeclaration(".mod-preview-info {filter: alpha(opacity=80);}");
            $this->document->addStyleDeclaration(".mod-preview-wrapper {filter: alpha(opacity=50);}");
        } else {
            $this->document->addStyleDeclaration(".mod-preview-info {-ms-filter: alpha(opacity=80);}");
            $this->document->addStyleDeclaration(".mod-preview-wrapper {-ms-filter: alpha(opacity=50);}");
        }
    } else {
        $this->document->addStyleDeclaration(".mod-preview-info {opacity: 0.8;}");
        $this->document->addStyleDeclaration(".mod-preview-wrapper {opacity: 0.5;}");
    }
}