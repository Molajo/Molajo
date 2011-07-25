<?php
/**
 * @version  1.6.2 June 9, 2011
 * @author  ÊRocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license  http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();

require_once(JPATH_ADMINISTRATOR.'/templates/rt_missioncontrol_j16/lib/missioncontrol.class.php');

/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JFormFieldColorChooser extends JFormField  {

    protected $type = 'colorchooser';
    protected $basetype = 'text';

	public function getInput(){

		global $mctrl;
		$mctrl =& MissionControl::getInstance();
		
		$doc =& MolajoFactory::getDocument();
		
		$output = '';

		$transparent = 1;
		
		if ($this->element->attributes('transparent') == 'false') $transparent = 0;
		
		if (!defined('MC_MOORAINBOW')) {
			
			$doc->addStyleSheet($mctrl->templateUrl.'/fields/colorchooser/css/mooRainbow.css');
			$doc->addScript($mctrl->templateUrl.'/fields/colorchooser/js/mooRainbow.js');
			$doc->addScript($mctrl->templateUrl.'/fields/colorchooser/js/colorchooser.js');
			$doc->addScriptDeclaration("var MCURL = '".$mctrl->templateUrlAbsolute."'");
			define('MC_MOORAINBOW',1);

		}
			
		$doc->addScriptDeclaration("MCColorChooser.add('".$this->id."', ".$transparent.");");

		$output .= "<div class='wrapper'>";
		$output .= "<input class=\"picker-input text-color\" id=\"".$this->id."\" name=\"".$this->name."\" type=\"text\" size=\"7\" maxlength=\"11\" value=\"".$this->value."\" />";
		$output .= "<div class=\"picker\" id=\"myRainbow_".$this->id."_input\"><div class=\"overlay".(($this->value == 'transparent') ? ' overlay-transparent' : '')."\" style=\"background-color: ".$this->value."\"><div></div></div></div>\n";
		$output .= "</div><div class=\"clr\"></div>";
		
		return $output;
	}
	
	function getJSVersion(){
	  if (version_compare(JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')) {
	    if (MolajoFactory::getApplication()->get('MooToolsVersion', '1.11') != '1.11') return "-mt1.2";
	    else return "";
	  }
	  else {
	    return "";
	  }
	}

	// public function getInput(){
	//         //($name, $value, &$node, $control_name)
	// 	//global $stylesList;
	//         /**
	//          * @global Gantry $gantry
	//          */
	// 	global $gantry;
	// 	$output = '';
	// 
	// 	$this->template = end(explode(DS, $gantry->templatePath));
	// 	$transparent = 1;
	// 	
	// 	if ($this->element->attributes('transparent') == 'false') $transparent = 0;
	//             if (!defined('GANTRY_CSS')) {
	// 		$gantry->addStyle($gantry->gantryUrl.'/admin/widgets/gantry.css');
	// 		define('GANTRY_CSS', 1);
	// 	}
	// 	
	// 	if (!defined('GANTRY_MOORAINBOW')) {
	// 		
	// 		$gantry->addStyle($gantry->gantryUrl.'/admin/widgets/colorchooser/css/mooRainbow.css');
	// 		$gantry->addScript($gantry->gantryUrl.'/admin/widgets/colorchooser/js/mooRainbow.js');
	// 		$gantry->addScript($gantry->gantryUrl.'/admin/widgets/colorchooser/js/colorchooser.js');
	// 		
	// 		define('GANTRY_MOORAINBOW',1);
	// 	}
	// 			
	// 	$gantry->addDomReadyScript("GantryColorChooser.add('".$this->id."', ".$transparent.");");
	// 
	// 	$output .= "<div class='wrapper'>";
	// 	$output .= "<input class=\"picker-input text-color\" id=\"".$this->id."\" name=\"".$this->name."\" type=\"text\" size=\"7\" maxlength=\"11\" value=\"".$this->value."\" />";
	// 	$output .= "<div class=\"picker\" id=\"myRainbow_".$this->id."_input\"><div class=\"overlay".(($this->value == 'transparent') ? ' overlay-transparent' : '')."\" style=\"background-color: ".$this->value."\"><div></div></div></div>\n";
	// 	$output .= "</div>";
	// 	
	// 	return $output;
	// }
}