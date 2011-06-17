<?php
/**
 * @version � 1.6.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();

require_once(JPATH_ADMINISTRATOR.'/templates/rt_missioncontrol_j16/lib/missioncontrol.class.php');
require_once(JPATH_ADMINISTRATOR.'/templates/rt_missioncontrol_j16/lib/rtmcupdates.class.php');

/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JFormFieldUpdateCheck extends JFormField {
	
	public function getLabel() {}

	public function getInput() {
        global $mctrl;
        $mctrl =& MissionControl::getInstance();

        if($mctrl->_getCurrentAdminTemplate() == "rt_missioncontrol_j16") {
            $this->checkForGantryUpdate();

            $html = '';
            $updates = RTMCUpdates::getInstance();
            $currentVersion =  $updates->getCurrentVersion();
            $latest_version = $updates->getLatestVersion();

            if (version_compare($latest_version,$currentVersion,'>')){
                $klass="updates-true";
                $upd = JText::sprintf('TPL_MISSIONCONTROL_VERSION_UPDATE_OUTOFDATE',$latest_version,'index.php?option=com_installer&view=update');
            } else {
                $klass = "updates";
                jimport('joomla.utilities.date');
                $nextupdate = new JDate($updates->getLastUpdated()+(24*60*60));

                $upd = JText::sprintf('TPL_MISSIONCONTROL_VERSION_UPDATE_CURRENT',JHTML::_('date', $updates->getLastUpdated()+(24*60*60),JText::_('DATE_FORMAT_LC2'),true));
            }

            $html .= "
            <div id='updater' class='".$klass." mc-update-check'>
                <div id='updater-bar' class='h2bar'>MissionControl <span>v".$currentVersion."</span></div>
                <div id='updater-desc'>".$upd."</div>
            </div>";

            return $html;


        } else {
            $html = '<b>* Feature only available within MissionControl</b>';
        }
        return $html;
	}

    protected function checkForGantryUpdate()
    {
        $updates = RTMCUpdates::getInstance();

        $last_updated = $updates->getLastUpdated();
        $diff = time() - $last_updated;
        if ($diff > (60*60*24)){
            jimport('joomla.updater.updater');
            // check for update
            $updater = JUpdater::getInstance();
            $results = $updater->findUpdates($updates->getExtensionId());
            $updates->setLastChecked(time());
        }
    }



}
?>