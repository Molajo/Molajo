<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Login Model
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoLoginModel extends MolajoDisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table = '';
        $this->primary_key = '';

        return parent::__construct($id);
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

    /**
     * @static
     * @return mixed
     */
    public static function getLanguageList()
    {
        $languages = array();
        $languages = LanguageServices::createLanguageList(null, MOLAJO_BASE_FOLDER, false, true);
        array_unshift($languages, MolajoHTML::_('select.option', '', Services::Language()->_('JDEFAULT')));
        return MolajoHTML::_('select.genericlist', $languages, 'language', ' class="inputbox"', 'value', 'text', null);
    }

    /**
     * Get the redirect URI after login.
     *
     * @return    string
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
