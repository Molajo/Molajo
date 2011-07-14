<?php

/**
 * @author Antonio Duran
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package xmlrpctest
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

/**
 * Xmlrpctest Controller
 *
 * @package Joomla
 * @subpackage Xmlrpctest
 */
class XmlrpctestController extends JController {
    /**
     * Constructor
     * @access private
     * @subpackage Xmlrpctest
     */
    function __construct() {
        //Get View
        if(JRequest::getCmd('view') == '') {
            JRequest::setVar('view', 'default');
        }
        $this->item_type = 'Default';
        parent::__construct();
    }
}
?>
