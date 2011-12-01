<?php 
/**
 * @id $Id$
 * @author  Joomla Bamboo
 * @package  JB Library
 * @copyright Copyright (C) 2006 - 2010 Joomla Bamboo. http://www.joomlabamboo.com  All rights reserved.
 * @license  GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
class JElementPanels extends JElement
{
    var $_name = 'Panels';

    function fetchElement($name, $value, &$node, $control_name)
    {
        $panels = '
				<script type="text/javascript">
					window.addEvent(\'domready\', function(){
						new Accordion(
							$$(\'.panel h3.jpane-toggler\'), 
							$$(\'.panel div.jpane-slider\'), 
							{onActive: function(toggler, i) { 
								toggler.addClass(\'jpane-toggler-down\'); 
								toggler.removeClass(\'jpane-toggler\'); 
							},onBackground: function(toggler, i) {
								toggler.addClass(\'jpane-toggler\'); 
								toggler.removeClass(\'jpane-toggler-down\');
							},duration: 300,opacity: false,alwaysHide: true});
						});
  				</script>';
        return $panels;
    }
}

?>