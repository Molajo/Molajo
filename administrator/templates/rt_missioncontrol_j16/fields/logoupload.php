<?php
/**
 * @version � 1.6.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();
require_once(JPATH_ADMINISTRATOR.'/templates/rt_missioncontrol_j16/lib/missioncontrol.class.php');
/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JFormFieldLogoUpload extends JFormField  {


	protected $type = 'logoupload';
    protected $basetype = 'text';

	public function getInput(){

        global $mctrl;
        $mctrl =& MissionControl::getInstance();

        if($mctrl->_getCurrentAdminTemplate() == "rt_missioncontrol_j16") {
            $upload_url = "?process=ajax&amp;model=logoupload";
            $output = '<iframe src="'.$upload_url.'" class="mc-logoupload"></iframe>';
        } else {
            $output = '<b>* Feature only available within MissionControl</b>';
        }



        return $output;

	}

}
?>