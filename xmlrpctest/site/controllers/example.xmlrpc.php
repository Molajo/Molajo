<?
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');


class XmlrpctestControllerExample extends JController
{
   public function test ()
    {
    //  $a = @file_get_contents('php://input');
    //  print_r( $a );
        $params = xmlrpc_decode ($_POST['params']);
        echo xmlrpc_encode ($params);
    }

}

?>
