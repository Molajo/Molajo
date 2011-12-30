<?php
/**
 * @package     Molajo
 * @subpackage  Footer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** initialise  */
$tmpobj = new JObject();
$view = $parameters->def('view', 'footer');
$wrap = $parameters->def('wrap', 'footer');

/**
 *  Line 1
 */
if (JString::strpos(MolajoTextHelper :: _('FOOTER_LINE1'), '%date%')) {
    $line1 = str_replace('%date%', MolajoFactory::getDate()->format('Y'), MolajoTextHelper :: _('FOOTER_LINE1'));
} else {
    $line1 = MolajoTextHelper :: _('FOOTER_LINE1');
}
if (JString::strpos($line1, '%sitename%')) {
    $line1 = str_replace('%sitename%', MolajoFactory::getApplication()->get('sitename', 'Molajo'), $line1);
}
$tmpobj->set('line1', $line1);

/**
 *  Line 2
 */
$link = $parameters->def('link', 'http://molajo.org');
$linked_text = $parameters->def('linked_text', 'Molajo&#153;');
$remaining_text = $parameters->def('remaining_text', ' is free software.');
$version = $parameters->def('version', MolajoTextHelper::_(MOLAJOVERSION));

$tmpobj->set('link', $link);
$tmpobj->set('linked_text', $linked_text);
$tmpobj->set('remaining_text', $remaining_text);
$tmpobj->set('version', $version);

$line2 = '<a href="' . $link . '">' . $linked_text . ' v.' . $version . '</a>';
$line2 .= $remaining_text;
$tmpobj->set('line2', $line2);

/** save recordset */
$rowset[] = $tmpobj;