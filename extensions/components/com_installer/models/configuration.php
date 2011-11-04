<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Install Configuration model for the Installer.
 *
 * @package		Joomla.Installation
 * @since		1.6
 */
class InstallerModelConfiguration extends MolajoModelDummy
{
	/**
     * setup
     *
	 * @return boolean
	 */
	public function setup($config)
	{
		// Get the $config array as a JObject for easier handling.
		$config = JArrayHelper::toObject($config, 'JObject');

		// Attempt to create the root user.
		if (!$this->_createConfiguration($config)) {
			return false;
		}

		// Attempt to create the root user.
        /*
		if (!$this->_createRootUser($config)) {
			return false;
		}
        */

		return true;
	}

    /**
     * _createConfiguration
     *
     * @param $config
     * @return bool
     */
	function _createConfiguration($config)
	{
		// Create a new registry to build the configuration options.
		$registry = new JRegistry();

		/* Site Settings */
		$registry->set('offline', 0);
		$registry->set('offline_message', MolajoText::_('INSTL_STD_OFFLINE_MSG'));
		$registry->set('sitename', $config->sitename);
		$registry->set('editor', 'none');
		$registry->set('list_limit', 20);
		$registry->set('access', 1);

		/* Debug Settings */
		$registry->set('debug', 0);
		$registry->set('debug_lang', 0);

		/* Database Settings */
		$registry->set('dbtype', $config->db_type);
		$registry->set('host', $config->db_host);
		$registry->set('user', $config->db_username);
		$registry->set('password', $config->db_password);
		$registry->set('db', $config->db_scheme);
		$registry->set('dbprefix', $config->db_prefix);

		/* Server Settings */
		$registry->set('live_site', '');
		$registry->set('secret', MolajoUserHelper::genRandomPassword(16));
		$registry->set('gzip', 0);
		$registry->set('error_reporting', -1);
		$registry->set('helpurl', 'http://help.molajo.org/');
		$registry->set('ftp_host', $config->ftp_host);
		$registry->set('ftp_port', $config->ftp_port);
		$registry->set('ftp_user', $config->ftp_save ? $config->ftp_user : '');
		$registry->set('ftp_pass', $config->ftp_save ? $config->ftp_pass : '');
		$registry->set('ftp_root', $config->ftp_save ? $config->ftp_root : '');
		$registry->set('ftp_enable', $config->ftp_enable);

		/* Locale Settings */
		$registry->set('offset', 'UTC');
		$registry->set('offset_user', 'UTC');

		/* Mail Settings */
		$registry->set('mailer', 'mail');
		$registry->set('mailfrom', $config->admin_email);
		$registry->set('fromname', $config->sitename);
		$registry->set('sendmail', '/usr/sbin/sendmail');
		$registry->set('smtpauth', 0);
		$registry->set('smtpuser', '');
		$registry->set('smtppass', '');
		$registry->set('smtphost', 'localhost');
		$registry->set('smtpsecure', 'none');
		$registry->set('smtpport', '25');

		/* Cache Settings */
		$registry->set('caching', 0);
		$registry->set('cache_handler', 'file');
		$registry->set('cachetime', 15);

		/* Meta Settings */
		$registry->set('MetaDesc', $config->site_metadesc);
		$registry->set('MetaKeys', $config->site_metakeys);
		$registry->set('MetaAuthor', 1);

		/* SEO Settings */
		$registry->set('sef', 1);
		$registry->set('sef_rewrite', 0);
		$registry->set('sef_suffix', 0);
		$registry->set('unicodeslugs', 0);

		/* Feed Settings */
		$registry->set('feed_limit', 10);
		$registry->set('log_path', MOLAJO_PATH_ROOT . '/logs');
		$registry->set('tmp_path', MOLAJO_PATH_ROOT . '/tmp');

		/* Session Setting */
		$registry->set('lifetime', 15);
		$registry->set('session_handler', 'database');

		// Generate the configuration class string buffer.
		$buffer = $registry->toString('PHP', array('class'=>'MolajoConfig', 'closingtag' => false));


		// Build the configuration file path.
		$path = MOLAJO_PATH_CONFIGURATION . '/configuration.php';

		// Determine if the configuration file path is writable.
		if (file_exists($path)) {
			$canWrite = is_writable($path);
		} else {
			$canWrite = is_writable(MOLAJO_PATH_CONFIGURATION . '/');
		}

		/*
		 * If the file exists but isn't writable OR if the file doesn't exist and the parent directory
		 * is not writable we need to use FTP
		 */
        /*
		$useFTP = false;
		if ((file_exists($path) && !is_writable($path))
                || (!file_exists($path) && !is_writable(dirname($path).'/'))) {
			$useFTP = true;
		}

		// Check for safe mode
		if (ini_get('safe_mode')) {
			$useFTP = true;
		}

		// Enable/Disable override
		if (!isset($config->ftpEnable) || ($config->ftpEnable != 1)) {
			$useFTP = false;
		}

		if ($useFTP == true) {
			$ftp = JFTP::getInstance($config->ftp_host, $config->ftp_port);
			$ftp->login($config->ftp_user, $config->ftp_pass);

			// Translate path for the FTP account
			$file = JPath::clean(str_replace(MOLAJO_PATH_CONFIGURATION, $config->ftp_root, $path), '/');

			// Use FTP write buffer to file
			if (!$ftp->write($file, $buffer)) {
				// Set the config string to the session.
				$session = MolajoFactory::getSession();
				$session->set('setup.config', $buffer);
			}

			$ftp->quit();
		} else {
			if ($canWrite) {
				file_put_contents($path, $buffer);
				$session = MolajoFactory::getSession();
				$session->set('setup.config', null);
			} else {
				// Set the config string to the session.
				$session = MolajoFactory::getSession();
				$session->set('setup.config', $buffer);
			}
		}
        */

		return true;
	}

    /**
     * _createRootUser
     *
     * @param $config
     * @return bool
     */
	function _createRootUser($config)
	{
		// Get a database object.
		$db = MolajoInstallationHelperDatabase::getDBO($config->db_type, $config->db_host, $config->db_user, $config->db_pass, $config->db_name, $config->db_prefix);

		// Check for errors.
		if (MolajoError::isError($db)) {
			$this->setError(MolajoText::sprintf('INSTL_ERROR_CONNECT_DB', (string)$db));
			return false;
		}

		// Check for database errors.
		if ($err = $db->getErrorNum()) {
			$this->setError(MolajoText::sprintf('INSTL_ERROR_CONNECT_DB', $db->getErrorNum()));
			return false;
		}

		// Create random salt/password for the admin user
		$salt = MolajoUserHelper::genRandomPassword(32);
		$crypt = MolajoUserHelper::getCryptedPassword($config->admin_password, $salt);
		$cryptpass = $crypt.':'.$salt;

		// create the admin user
		date_default_timezone_set('UTC');
		$installdate	= date('Y-m-d H:i:s');
		$nullDate		= $db->getNullDate();
        $randomID       = rand(1, 10000);

		$query	= 'REPLACE INTO #__users SET'
				. ' id = '.$randomID
				. ', name = '.$db->quote('Administrator')
				. ', username = '.$db->quote($config->admin_user)
				. ', email = '.$db->quote($config->admin_email)
				. ', password = '.$db->quote($cryptpass)
				. ', block = 0'
				. ', sendEmail = 1'
				. ', registerDate = '.$db->quote($installdate)
				. ', lastvisitDate = '.$db->quote($nullDate)
				. ', activation = '.$db->quote('')
				. ', params = '.$db->quote('')
				. ', asset_id = 5 ';

		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Map the super admin to the Super Admin Group
		$query = 'INSERT INTO #__user_groups (user_id, group_id) ' .
				' SELECT '.$randomID.', 4';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		// Map the super admin to the Super Admin Group
		$query = 'INSERT INTO #__user_groupings (user_id, view_group_id) ' .
				' SELECT '.$randomID.', 4';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		// Map the super admin to the Super Admin Group
		$query = 'INSERT INTO #__user_groupings (user_id, view_group_id) ' .
				' SELECT '.$randomID.', 5';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Map the super admin to login to all applications
		$query = 'INSERT INTO #__user_applications (user_id, application_id) ' .
				' VALUES ' .
                '('.$randomID.', 0), ' .
                '('.$randomID.', 1), ' .
                '('.$randomID.', 3); ';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Add user as group - type_id = 0 for User
		$query	= 'INSERT INTO #__groups (id, parent_id, lft, rgt, title, asset_id, type_id, protected) '
				. ' SELECT 5, 0, 0, 0, '.$db->quote('Administrator').', 6, 0, 1';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		// Map the super admin to the their personal group
		$query = 'INSERT INTO #__user_groups (user_id, group_id) ' .
				' SELECT '.$randomID.', 5';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

        MolajoInstallationModelConfiguration::createPermissions($config);
		return true;
	}

    /**
     * createPermissions
     *
     * Populates the Permissions Tables
     * 
     * @param $config
     * @return bool
     */
    function createPermissions ($config)
    {
		$db = MolajoInstallationHelperDatabase::getDBO($config->db_type, $config->db_host, $config->db_user, $config->db_pass, $config->db_name, $config->db_prefix);

		$query = 'INSERT INTO `#__permissions_groups` (`group_id`,`asset_id`,`action_id`)
                  SELECT DISTINCT c.group_id as group_id, b.id as asset_id, 3 as `action_id`
                    FROM `#__groups`          a,
                      `#__assets`             b,
                      `#__group_view_groups` c
                    WHERE a.id = c.group_id
                      AND b.access = c.view_group_id';

		$db->setQuery($query);
		if ($db->query()) {
        } else {
			$this->setError($db->getErrorMsg());
			return false;
		}

        /** 4-Edit, 5-Publish, 6-Delete */
		$query = 'INSERT INTO `#__permissions_groups` (`group_id`, `asset_id`, `action_id`)
                      SELECT DISTINCT a.id as group_id, b.id as asset_id, c.id as action_id
                        FROM `#__groups`          a,
                          `#__assets`             b,
                          `#__actions`            c
                        WHERE a.id = 4
                          AND c.id IN (4, 5, 6)';

		$db->setQuery($query);
		if ($db->query()) {
        } else {
			$this->setError($db->getErrorMsg());
			return false;
		}

        /** 2-Create, 7-Admin for Components */
		$query = 'INSERT INTO `#__permissions_groups` (`group_id`, `asset_id`, `action_id`)
                      SELECT DISTINCT a.id as group_id, b.asset_id, c.id as action_id
                        FROM `#__groups`          a,
                          `#__extensions`         b,
                          `#__actions`            c
                        WHERE a.id = 4
                          AND c.id IN (2, 7)
                          AND b.type = "component"
                          AND b.application_id = 1';

		$db->setQuery($query);
		if ($db->query()) {
        } else {
			$this->setError($db->getErrorMsg());
			return false;
		}

        /** 1-Login in Site Application */
		$query = 'INSERT INTO `#__permissions_groups` (`group_id`, `asset_id`, `action_id`)
                      SELECT DISTINCT a.id as group_id, b.asset_id, c.id as action_id
                        FROM `#__groups`          a,
                          `#__extensions`         b,
                          `#__actions`            c
                        WHERE a.id = 4
                          AND c.id IN (2, 7)
                          AND b.type = "component"
                          AND b.application_id = 1';

		$db->setQuery($query);
		if ($db->query()) {
        } else {
			$this->setError($db->getErrorMsg());
			return false;
		}

        /** 1-Login in Site Application */
		$query = 'INSERT INTO `#__permissions_groups` (`group_id`, `asset_id`, `action_id`)
                      SELECT DISTINCT a.id as group_id, b.asset_id, c.id as action_id
                        FROM `#__groups`        a,
                          `#__applications`     b,
                          `#__actions`          c
                        WHERE a.id = 4
                          AND c.id IN (1)
                          AND b.application_id = 0';

		$db->setQuery($query);
		if ($db->query()) {
        } else {
			$this->setError($db->getErrorMsg());
			return false;
		}

        /** Permission Groupings */
		$query = 'INSERT INTO `#__view_group_permissions` ( `view_group_id`, `asset_id`, `action_id`)
                      SELECT DISTINCT b.view_group_id, a.asset_id, a.action_id
                      FROM #__permissions_groups a,
                        #__group_view_groups b
                      WHERE a.group_id = b.group_id';

		$db->setQuery($query);
		if ($db->query()) {
        } else {
			$this->setError($db->getErrorMsg());
			return false;
		}
    }
}
