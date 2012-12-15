<?php
namespace Molajo\MVC\Model;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * As instructed by the Login Controller. the Login Model retrieves user data to verify the credentials
 *  provided by the user during system login activities. This model also updates user data for login information.
 *
 * @package      Niambie
 * @license      GPL v 2, or later and MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 * @since        1.0
 */
class LoginModel extends DisplayModel
{
    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState()
    {
        $credentials = array(
            'username' => JRequest::getVar('username', '', 'method', 'username'),
            'password' => JRequest::getVar('passwd', '', 'post', 'string', JREQUEST_ALLOWRAW)
        );
        $this->setState('credentials', $credentials);

        // check for return URL from the request first
        if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
            $return = base64_decode($return);
            if (!JURI::isInternal($return)) {
                $return = '';
            }
        }

        // Set the return URL if empty.
        if (empty($return)) {
            $return = 'index.php';
        }

        $this->setState('return', $return);
    }

    /**
     * @static
     * @return mixed
     */
    public static function getLanguageList()
    {
        $languages = array();
        $languages = LanguageServices::createLanguageList(null, BASE_FOLDER, false, true);
        array_unshift($languages, MolajoHTML::_('select.option', '', Services::Language()->translate('JDEFAULT')));

        return MolajoHTML::_('select.genericlist', $languages, CATALOG_TYPE_LANGUAGE_LITERAL, ' class="inputbox"', 'value', 'text', null);
    }

    /**
     * Get the redirect URI after login.
     *
     * @return string
     */
    public static function getReturnURI()
    {
        $uri = Molajo::getURI();
        $return = 'index.php' . $uri->toString(array('query'));
        if ($return != 'index.php?option=login') {
            return base64_encode($return);
        } else {
            return base64_encode('index.php');
        }
    }
}
