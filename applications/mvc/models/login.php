<?php
/**
 * @package     Molajo
 * @subpackage  Login Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoModelLogin
 *
 * @package        Molajo
 * @subpackage    Login Model
 * @since       1.0
 */
//class MolajoModelLogin extends JModel
class MolajoModelLogin extends MolajoModel
{
    /**
     * Constructor.
     *
     * @param    array    $config    An optional associative array of configuration settings.
     *
     * @see    JController
     * @since    1.0
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

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

    public static function getLanguageList()
    {
        $languages = array();
        $languages = MolajoLanguageHelper::createLanguageList(null, MOLAJO_BASE_FOLDER, false, true);
        array_unshift($languages, MolajoHTML::_('select.option', '', MolajoTextHelper::_('JDEFAULT')));
        return MolajoHTML::_('select.genericlist', $languages, 'language', ' class="inputbox"', 'value', 'text', null);
    }

    /**
     * Get the redirect URI after login.
     *
     * @return    string
     */
    public static function getReturnURI()
    {
        $uri = MolajoFactory::getURI();
        $return = 'index.php' . $uri->toString(array('query'));
        if ($return != 'index.php?option=login') {
            return base64_encode($return);
        } else {
            return base64_encode('index.php');
        }
    }
}