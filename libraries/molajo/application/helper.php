<?php
/**
 * @package     Molajo
 * @subpackage  Application Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo helper class.
 *
 * @package		Helper
 * @subpackage	Application
 */
class MolajoHelper
{
	/**
	 * Verifies login requirement for application and default options
	 *
	 * @return	string		option
	 * @since	1.0
	 */
	public static function findOption()
	{
        $option = strtolower(JRequest::getCmd('option', ''));

        if (MolajoFactory::getUser()->get('guest') === true
            && MolajoFactory::getConfig()->get('application_logon_requirement', true) === true) {

            $option = MolajoFactory::getConfig()->get('application_guest_option', 'com_login');

        } elseif ($option == '') {
            $option = MolajoFactory::getConfig()->get('application_default_option', 'com_dashboard');
        }

		JRequest::setVar('option', $option);
		return $option;
	}
}