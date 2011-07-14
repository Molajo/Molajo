<?
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');


class XmlrpctestControllerExample extends JController
{

	public function __construct($config = array())
    {
        parent::__construct($config);

        $this->registerTask('test',        'test');
    }

   public function test ()
    {
		echo "NORMAL HTTP TASK";
		return;
    }

}

?>
