<?php

/**
 * @author Antonio Duran
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package xmlrpctest
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Xmlrpctest component
 */
class XmlrpctestViewXmlrpctest extends JView {
	function display($tpl = null) {

		/*
	global $mainframe;

	$params = &$mainframe->getParams();
	$this->assignRef('params',              $params);
*/

	parent::display($tpl);
    }
}
?>
