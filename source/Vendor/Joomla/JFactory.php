<?php
/**
 * @package    Joomla.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla;

use Molajo\Application\Molajo;

use Molajo\Services;

use Simplepie\SimplePie;

use Joomla\log\JLog;

use Joomla\filesystem\JStream;

use Joomla\client\JClientHelper;

defined('JPATH_PLATFORM') or die;

/**
 * Hacked Joomla Platform Factory class to redirect to Molajo Services and Application Objects
 *
 * @package  Joomla.Platform
 * @since    11.1
 */
abstract class JFactory
{
	/**
	 * Get the Molajo application object.
	 *
	 * @param   mixed   $id      A client identifier or name.
	 * @param   array   $config  An optional associative array of configuration settings.
	 * @param   string  $prefix  Application prefix
	 *
	 * @return  Application object
	 *
	 * @since   1.0
	 */
	public static function getApplication($id = null, array $config = array(), $prefix = 'J')
	{
		return Molajo::Application();
	}

	/**
	 * Get the Molajo configuration object
	 *
	 * @param   string  $file  The path to the configuration file
	 * @param   string  $type  The type of the configuration file
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public static function getConfig($file = null, $type = 'PHP')
	{
		return Service::Registry()->get('Configuration');
	}

	/**
	 * Retrieves the Molajo Session Object
	 *
	 * @param   array  $options  An array containing session options
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public static function getSession(array $options = array())
	{
		return Service::Session();
	}

	/**
	 * Retrieves the Molajo Language object.
	 *
	 * @return  Language object
	 *
	 * @since   1.0
	 */
	public static function getLanguage()
	{
		return Service::Language();
	}

	/**
	 * Retrieves the Response Object
	 *
	 * @return  Response object
	 *
	 * @since   1.0
	 */
	public static function getDocument()
	{
		return Service::Response();
	}

	/**
	 * Retrieves the User Object
	 *
	 * @param   integer  $id  The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 *
	 * @return  JUser object
	 *
	 * @since   1.0
	 */
	public static function getUser($id = null)
	{
		return Service::User();
	}

	/**
	 * Get a cache object
	 *
	 * @param   string  $group    The cache group name
	 * @param   string  $handler  The handler to use
	 * @param   string  $storage  The storage method
	 *
	 * @return  Cache object
	 *
	 * @since   1.0
	 */
	public static function getCache($group = '', $handler = 'callback', $storage = null)
	{
		return Service::Cache()->get($group, $handler, $storage);
	}

	/**
	 * Get an authorization object
	 *
	 * Returns the global {@link JAccess} object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  JAccess object
	 */
	public static function getACL()
	{
		return null;
	}

	/**
	 * Get a database object.
	 *
	 * @return  JDatabaseDriver
	 *
	 * @since   1.0
	 */
	public static function getDbo()
	{
		return Service::Database();
	}

	/**
	 * Retrieves the Mailer Object
	 *
	 * @return  object Mail
	 *
	 * @since   1.0
	 */
	public static function getMailer()
	{
		return Service::Mail();
	}

	/**
	 * Get a parsed XML Feed Source
	 *
	 * @param   string   $url         Url for feed source.
	 * @param   integer  $cache_time  Time to cache feed for (using internal cache mechanism).
	 *
	 * @return  mixed  SimplePie parsed object on success, false on failure.
	 *
	 * @since   11.1
	 */
	public static function getFeedParser($url, $cache_time = 0)
	{


		$cache = self::getCache('feed_parser', 'callback');

		if ($cache_time > 0)
		{
			$cache->setLifeTime($cache_time);
		}

		$simplepie = new SimplePie(null, null, 0);

		$simplepie->enable_cache(false);
		$simplepie->set_feed_url($url);
		$simplepie->force_feed(true);

		$contents = $cache->get(array($simplepie, 'init'), null, false, false);

		if ($contents)
		{
			return $simplepie;
		}
		else
		{
			JLog::add(JText::_('JLIB_UTIL_ERROR_LOADING_FEED_DATA'), JLog::WARNING, 'jerror');
		}

		return false;
	}

	/**
	 * Reads a XML file.
	 *
	 * @param   string   $data    Full path and file name.
	 * @param   boolean  $isFile  true to load a file or false to load a string.
	 *
	 * @return  mixed    JXMLElement on success or false on error.
	 *
	 * @see     JXMLElement
	 * @since   11.1
	 * @note    This method will return SimpleXMLElement object in the future. Do not rely on JXMLElement's methods.
	 * @todo    This may go in a separate class - error reporting may be improved.
	 */
	public static function getXML($data, $isFile = true)
	{
		return null;
	}

	/**
	 * Get an editor object.
	 *
	 * @param   string  $editor  The editor to load, depends on the editor triggers that are installed
	 *
	 * @return  JEditor object
	 *
	 * @since   11.1
	 */
	public static function getEditor($editor = null)
	{
		return Service::Editor();
	}

	/**
	 * Return a reference to the {@link JURI} object
	 *
	 * @param   string  $uri  Uri name.
	 *
	 * @return  JURI object
	 *
	 * @see     JURI
	 * @since   11.1
	 */
	public static function getURI($uri = 'SERVER')
	{
		jimport('joomla.environment.uri');

		return JURI::getInstance($uri);
	}

	/**
	 * Return the {@link JDate} object
	 *
	 * @param   mixed  $time      The initial time for the JDate object
	 * @param   mixed  $tzOffset  The timezone offset.
	 *
	 * @return  JDate object
	 *
	 * @see     JDate
	 * @since   11.1
	 */
	public static function getDate($time = 'now', $tzOffset = null)
	{
		static $classname;
		static $mainLocale;

		$language = self::getLanguage();
		$locale = $language->getTag();

		if (!isset($classname) || $locale != $mainLocale)
		{
			// Store the locale for future reference
			$mainLocale = $locale;

			if ($mainLocale !== false)
			{
				$classname = str_replace('-', '_', $mainLocale) . 'Date';

				if (!class_exists($classname))
				{
					// The class does not exist, default to JDate
					$classname = 'JDate';
				}
			}
			else
			{
				// No tag, so default to JDate
				$classname = 'JDate';
			}
		}

		$key = $time . '-' . ($tzOffset instanceof DateTimeZone ? $tzOffset->getName() : (string) $tzOffset);

		if (!isset(self::$dates[$classname][$key]))
		{
			self::$dates[$classname][$key] = new $classname($time, $tzOffset);
		}

		$date = clone self::$dates[$classname][$key];

		return $date;
	}

	/**
	 * Creates a new stream object with appropriate prefix
	 *
	 * @param   boolean  $use_prefix   Prefix the connections for writing
	 * @param   boolean  $use_network  Use network if available for writing; use false to disable (e.g. FTP, SCP)
	 * @param   string   $ua           UA User agent to use
	 * @param   boolean  $uamask       User agent masking (prefix Mozilla)
	 *
	 * @return  JStream
	 *
	 * @see JStream
	 * @since   11.1
	 */
	public static function getStream($use_prefix = true, $use_network = true, $ua = null, $uamask = false)
	{
		// Setup the context; Joomla! UA and overwrite
		$context = array();
		$version = new JVersion;

		// Set the UA for HTTP and overwrite for FTP
		$context['http']['user_agent'] = $version->getUserAgent($ua, $uamask);
		$context['ftp']['overwrite'] = true;

		if ($use_prefix)
		{
			$FTPOptions = JClientHelper::getCredentials('ftp');
			$SCPOptions = JClientHelper::getCredentials('scp');

			if ($FTPOptions['enabled'] == 1 && $use_network)
			{
				$prefix = 'ftp://' . $FTPOptions['user'] . ':' . $FTPOptions['pass'] . '@' . $FTPOptions['host'];
				$prefix .= $FTPOptions['port'] ? ':' . $FTPOptions['port'] : '';
				$prefix .= $FTPOptions['root'];
			}
			elseif ($SCPOptions['enabled'] == 1 && $use_network)
			{
				$prefix = 'ssh2.sftp://' . $SCPOptions['user'] . ':' . $SCPOptions['pass'] . '@' . $SCPOptions['host'];
				$prefix .= $SCPOptions['port'] ? ':' . $SCPOptions['port'] : '';
				$prefix .= $SCPOptions['root'];
			}
			else
			{
				$prefix = JPATH_ROOT . '/';
			}

			$retval = new JStream($prefix, JPATH_ROOT, $context);
		}
		else
		{
			$retval = new JStream('', '', $context);
		}

		return $retval;
	}
}
