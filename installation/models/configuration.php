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
class JInstallationModelConfiguration extends JModel
{
	/**
     * setup
     *
	 * @return boolean
	 */
	public function setup($options)
	{
		// Get the options as a JObject for easier handling.
		$options = JArrayHelper::toObject($options, 'JObject');

		// Attempt to create the root user.
		if (!$this->_createConfiguration($options)) {
			return false;
		}

		// Attempt to create the root user.
		if (!$this->_createRootUser($options)) {
			return false;
		}

		return true;
	}

    /**
     * _createConfiguration
     *
     * @param $options
     * @return bool
     */
	function _createConfiguration($options)
	{
		// Create a new registry to build the configuration options.
		$registry = new JRegistry();

		/* Site Settings */
		$registry->set('offline', 0);
		$registry->set('offline_message', JText::_('INSTL_STD_OFFLINE_MSG'));
		$registry->set('sitename', $options->site_name);
		$registry->set('editor', 'none');
		$registry->set('list_limit', 20);
		$registry->set('access', 1);

		/* Debug Settings */
		$registry->set('debug', 0);
		$registry->set('debug_lang', 0);

		/* Database Settings */
		$registry->set('dbtype', $options->db_type);
		$registry->set('host', $options->db_host);
		$registry->set('user', $options->db_user);
		$registry->set('password', $options->db_pass);
		$registry->set('db', $options->db_name);
		$registry->set('dbprefix', $options->db_prefix);

		/* Server Settings */
		$registry->set('live_site', '');
		$registry->set('secret', JUserHelper::genRandomPassword(16));
		$registry->set('gzip', 0);
		$registry->set('error_reporting', -1);
		$registry->set('helpurl', 'http://help.molajo.org/');
		$registry->set('ftp_host', $options->ftp_host);
		$registry->set('ftp_port', $options->ftp_port);
		$registry->set('ftp_user', $options->ftp_save ? $options->ftp_user : '');
		$registry->set('ftp_pass', $options->ftp_save ? $options->ftp_pass : '');
		$registry->set('ftp_root', $options->ftp_save ? $options->ftp_root : '');
		$registry->set('ftp_enable', $options->ftp_enable);

		/* Locale Settings */
		$registry->set('offset', 'UTC');
		$registry->set('offset_user', 'UTC');

		/* Mail Settings */
		$registry->set('mailer', 'mail');
		$registry->set('mailfrom', $options->admin_email);
		$registry->set('fromname', $options->site_name);
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
		$registry->set('MetaDesc', $options->site_metadesc);
		$registry->set('MetaKeys', $options->site_metakeys);
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
		if (!isset($options->ftpEnable) || ($options->ftpEnable != 1)) {
			$useFTP = false;
		}

		if ($useFTP == true) {
			$ftp = JFTP::getInstance($options->ftp_host, $options->ftp_port);
			$ftp->login($options->ftp_user, $options->ftp_pass);

			// Translate path for the FTP account
			$file = JPath::clean(str_replace(MOLAJO_PATH_CONFIGURATION, $options->ftp_root, $path), '/');

			// Use FTP write buffer to file
			if (!$ftp->write($file, $buffer)) {
				// Set the config string to the session.
				$session = JFactory::getSession();
				$session->set('setup.config', $buffer);
			}

			$ftp->quit();
		} else {
			if ($canWrite) {
				file_put_contents($path, $buffer);
				$session = JFactory::getSession();
				$session->set('setup.config', null);
			} else {
				// Set the config string to the session.
				$session = JFactory::getSession();
				$session->set('setup.config', $buffer);
			}
		}

		return true;
	}

    /**
     * _createRootUser
     *
     * @param $options
     * @return bool
     */
	function _createRootUser($options)
	{
		// Get a database object.
		$db = JInstallationHelperDatabase::getDBO($options->db_type, $options->db_host, $options->db_user, $options->db_pass, $options->db_name, $options->db_prefix);

		// Check for errors.
		if (JError::isError($db)) {
			$this->setError(JText::sprintf('INSTL_ERROR_CONNECT_DB', (string)$db));
			return false;
		}

		// Check for database errors.
		if ($err = $db->getErrorNum()) {
			$this->setError(JText::sprintf('INSTL_ERROR_CONNECT_DB', $db->getErrorNum()));
			return false;
		}

		// Create random salt/password for the admin user
		$salt = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($options->admin_password, $salt);
		$cryptpass = $crypt.':'.$salt;

		// create the admin user
		date_default_timezone_set('UTC');
		$installdate	= date('Y-m-d H:i:s');
		$nullDate		= $db->getNullDate();
        $randomID       = rand(1, 10000);

		$query	= 'REPLACE INTO #__users SET'
				. ' id = '.$randomID
				. ', name = '.$db->quote('Administrator')
				. ', username = '.$db->quote($options->admin_user)
				. ', email = '.$db->quote($options->admin_email)
				. ', password = '.$db->quote($cryptpass)
				. ', block = 0'
				. ', sendEmail = 1'
				. ', registerDate = '.$db->quote($installdate)
				. ', lastvisitDate = '.$db->quote($nullDate)
				. ', activation = '.$db->quote('')
				. ', params = '.$db->quote('');

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
		$query = 'INSERT INTO #__user_groupings (user_id, grouping_id) ' .
				' SELECT '.$randomID.', 4';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		// Map the super admin to the Super Admin Group
		$query = 'INSERT INTO #__user_groupings (user_id, grouping_id) ' .
				' SELECT '.$randomID.', 5';
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Add user as group - type_id = 0 for User
		$query	= 'INSERT INTO #__groups (parent_id, lft, rgt, title, asset_id, access, type_id) '
				. ' SELECT 5, 0, 0, '.$db->quote('Administrator').', 51, 4, 0';
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
		return true;
	}
}
