<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

use Molajo\Application\Services;

use TwitterOAuth\Twitter;

/**
 * BroadcastService
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class BroadcastService
{
	/**
	 * Response instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * $on Switch
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $on;

	/**
	 * getInstance
	 *
	 * @static
	 * @return object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new BroadcastService();
		}
		return self::$instance;
	}



	/**
	 * Class constructor.
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function __construct()
	{
		/** Set debugging on or off */
		$this->on = (int)Services::Registry()->get('Configuration\\Broadcast', 0);
		if ($this->on == 0)
		{
			return true;
		}

		/** Valid Broadcast Services */

		$broadcastOptions = array();
		$broadcastOptions[] = 'Email';
		$broadcastOptions[] = 'Tweet';

		/** @var $options */
		$options = array();

		/** Logger Type */
		/** http://classes.verkoyen.eu/twitter_oauth/docs */
		$options['twitter_tweet'] = Services::Registry()->get('Configuration\\twitter_tweet', 1);
		$options['twitter_consumer_key'] = Services::Registry()->get('Configuration\\twitter_consumer_key', '5HroaAP3vnOE3Hqkpjh7og');
		$options['twitter_consumer_secret'] = Services::Registry()->get('Configuration\\twitter_consumer_secret', 'dtEht0raDHQjAvma2AhJCUhzeq5HyU6NPhhWNxo42Y');
		$this->authenticateTwitter ($options);

		/** Email */
		if ($options['logger'] == 'email')
		{
			$options['mailer'] = Services::Mail();
			$options['reply_to'] = Services::Registry()->get('Configuration\\mail_reply_to', '');
			$options['from'] = Services::Registry()->get('Configuration\\mail_from', '');
			$options['subject'] = Services::Registry()->get('Configuration\\debug_email_subject', '');
			$options['to'] = Services::Registry()->get('Configuration\\debug_email_to', '');
		}

		return $this;
	}

	/**
	 * Modifies a property of the Request Parameter object
	 *
	 * @param   string  $message
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function set($message)
	{
		if ((int)$this->on == 0) {
			return true;
		}

		try {
			Services::Log()->addEntry($message, LOG_TYPE_DEBUG, self::log_type, Services::Date()->getDate('now'));
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Unable to add Log Entry: ' . $message . ' ' . $e->getMessage());
		}

		return true;
	}

	public function authenticateTwitter($options)
	{

		// create instance
		$twitter = new Twitter($options['twitter_consumer_key'], $options['twitter_consumer_secret']);

		// get a request token
		//$twitter->oAuthRequestToken('<your-callback-url>');
		$twitter->oAuthRequestToken();

		// authorize
		if(!isset($_GET['oauth_token'])) $twitter->oAuthAuthorize();

		// get tokens
		$response = $twitter->oAuthAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);

		// output, you can use the token for setOAuthToken and setOAuthTokenSecret
		var_dump($response);

	}
}
