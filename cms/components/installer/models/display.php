<?php
/**
 * @package     Molajo
 * @subpackage  Display Model
 * @copyright   Copyright (C) 2011 Babs Gösgens. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display Model
 *
 * InstallerModelDisplay extends MolajoModelDisplay extends JModel extends JObject
 *
 * @package	    Molajo
 * @subpackage	Model
 * @since       1.0
 */
class InstallerModelDisplay extends MolajoModelDummy
{

    var $system = null;
    var $setup = null;
    var $can_install = false;

    public function __construct($properties = null)
	{
		parent::__construct($properties);

        $this->_init();
	}

    /**
     * display
     *
     * @return void
     */
    public function SystemChecks()
    {
        $system_checks = array();

        if(1==1) {
            $this->can_install = true;
        }
        
        return $system_checks;
    }

    /**
     * getLanguageList
     *
     * Retrieves the list of installable languages
     *
     * @return void
     */
    public function getLanguageList()
    {
        //MolajoLanguageHelper::createLanguageList
        $language_list = array(
            'en-UK' => 'UK English',
            'en-US' => 'US English',
            'nl-NL' => 'Dutch'
        );
        return $language_list;
    }

    /**
     * getDBTypes
     *
     * Retrieves the list of available database types
     *
     * @return void
     */
    public function getDBTypes()
    {
        require_once MOLAJO_CMS_COMPONENTS.'/installer/helpers/installer.php';

        return InstallerHelper::detectDBTypes();
    }

    /**
     * getMockDataTypes
     *
     * Retrieves the list of available mock data
     *
     * @return void
     */
    public function getMockDataTypes()
    {
        require_once MOLAJO_CMS_COMPONENTS.'/installer/helpers/installer.php';

        return InstallerHelper::detectMockDataTypes();
    }

    /**
     * getFormFields
     *
     * @return void
     */
    public function getFormFields()
    {
        if(JRequest::get('post')) {
            foreach(JRequest::get('post') AS $name => $value) {
                if(key_exists($name, $this->setup)) {
                    $this->setup[$name] = $value;
                }
            }
        }
    }

    public function getSetup()
    {
        if(is_null($this->setup)) {
            $this->_init();
        }
        return $this->setup;
    }

    /**
     * getFormEdits
     *
     * @return void
     */
    public function getFormEdits()
    {

        //retrieve ALL of the form fields off of the post
        //doesn't matter which page you are on -- get them all with the defaults
        //some will be hidden
        $form_edits = array();
        return $form_edits;
    }

    protected function _init()
    {
        require_once(MOLAJO_CMS_COMPONENTS.'/installer/helpers/installer.php');
        var_dump(MolajoLanguageHelper::detectLanguage());
        if(is_null($this->setup)) {
            $this->setup = array(
                'language' => MolajoLanguageHelper::detectLanguage(), // This actually doesn't work right now but that may well be caused by the splitting up of classes
                'sitename' => '',
                'name' => '',
                'admin_email' => '',
                'admin_password' => '',
                'db_host' => 'localhost',
                'db_scheme' => '',
                'db_username' => '',
                'db_password' => '',
                'db_prefix' => 'jos_',
                'db_type' => 'pdo_mysql',
                'remove_tables' => false,
                'sample_data' => 0,
                'ftp_host' => '127.0.0.1'
            );
        }

        $this->getFormFields();
    }

    public function install($config=array())
    {
		// Get the $config array as a JObject for easier handling.
		$config = JArrayHelper::toObject($config, 'JObject');

        var_dump(get_class_methods($this));

        // Remove or backup existing tables based on config
        if($config->remove_tables) {
            $this->deleteTables();
        }
        else {
            $this->backupTables();
        }

        // Install sample data if required
        if($config->sample_data) {

        }

    }


}