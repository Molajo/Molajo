<?php
define('_JEXEC', 1);

require_once dirname(__FILE__).'/libraries/import.php';

jimport('joomla.application.cli');

/**
 * Simple class to retreive some tweets from Twitter and display using the Joomla! Platform CLI
 *
 * @author Michael Babker
 */
class LatestTweets extends JCli
{
	public function execute() {
		// Explain what is going on
		$this->out('This application retreives the latest tweets from a user on Twitter.', true);

		// Get the username
		$this->out('What is the Twitter username?');
		$username = $this->in();

		// Get the number of tweets
		$this->out('How many tweets would you like?');
		$count = $this->in();

		// Make sure the user has some hits on their account
		$hits	= $this->getLimit($username);
		if ($hits == 0) {
			$this->out('This user has no hits available at this time.');
			return;
		}

		// The URL to request the Twitter data from
		$req = 'http://api.twitter.com/1/statuses/user_timeline.json?count='.$count.'&screen_name='.$username.'&include_rts=1';

		// Get the data
		$obj = $this->getJSON($req);

		// Process the data
		$twitter = $this->processItem($obj);

		// Output the tweets
		foreach ($twitter as $o) {
			$this->out($o->tweet->text.' tweeted '.$o->tweet->created);
		}
	}

	/**
	 * All the config is user based
	 *
	 * @return  void
	 */
	protected function fetchConfigurationData() {
		return array();
	}

	/**
	 * Function to fetch a JSON feed
	 *
	 * @param	string	$req	The URL of the feed to load
	 *
	 * @return	array	$obj	The fetched JSON query
	 * @since	1.0.7
	 */
	function getJSON($req) {
		// Create a new CURL resource
		$ch = curl_init($req);

		// Set options
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Grab URL and pass it to the browser and store it as $json
		$json = curl_exec($ch);

		// Close CURL resource
		curl_close($ch);

		// Decode the fetched JSON
		$obj = json_decode($json, true);

		return $obj;
	}

    /**
	 * Function to get the rate limit of a Twitter user
	 *
	 * @param	string	$username	The Twitter username
	 *
	 * @return	string	$hits		The number of remaining hits on a user's rate limit
	 */
	function getLimit($username) {
		// Load the parameters
		$req = 'http://api.twitter.com/1/account/rate_limit_status.json?screen_name='.$username;

		// Fetch the decoded JSON
		$obj = $this->getJSON($req);

		// Get the remaining hits count
		if (isset ($obj['remaining_hits'])) {
		 	$hits = $obj['remaining_hits'];
		} else {
		 	$hits = '';
		}
		return $hits;
	}

	/**
	 * Function to process the Twitter feed into a formatted object
	 *
	 * @param	array	$obj		The data from Twitter
	 *
	 * @return	object	$twitter	The output
	 */
	function processItem($obj) {
		// Initialize
		$twitter = array();
		$i = 0;

		// Process the feed
		foreach ($obj as $o) {
			// Initialize a new object
			$twitter[$i]->tweet	= new stdClass();

			// The data we're using
			$twitter[$i]->tweet->user		= $o['user']['screen_name'];
			$twitter[$i]->tweet->text		= $o['text'];
			$twitter[$i]->tweet->created	= $this->renderRelativeTime($o['created_at']);
			$i++;
		}

		return $twitter;
	}

	/**
	 * Function to convert a static time into a relative measurement
	 *
	 * @param	string	$date	The date to convert
	 *
	 * @return	string	$date	A text string of a relative time
	 */
	function renderRelativeTime($date) {
		$diff = time() - strtotime($date);
		// Less than a minute
		if ($diff < 60) {
			return JText::_('Less than a minute ago');
		}
		$diff = round($diff/60);
		// 60 to 119 seconds
		if ($diff < 2) {
			return JText::sprintf('%s minute ago', $diff);
		}
		// 2 to 59 minutes
		if ($diff < 60) {
			return JText::sprintf('%s minutes ago', $diff);
		}
		$diff = round($diff/60);
		// 1 hour
		if ($diff < 2) {
			return JText::sprintf('%s hour ago', $diff);
		}
		// 2 to 23 hours
		if ($diff < 24) {
			return JText::sprintf('%s hours ago', $diff);
		}
		$diff = round($diff/24);
		// 1 day
		if ($diff < 2) {
			return JText::sprintf('%s day ago', $diff);
		}
		// 2 to 6 days
		if ($diff < 7) {
			return JText::sprintf('%s days ago', $diff);
		}
		$diff = round($diff/7);
		// 1 week
		if ($diff < 2) {
			return JText::sprintf('%s week ago', $diff);
		}
		// 2 or 3 weeks
		if ($diff < 4) {
			return JText::sprintf('%s weeks ago', $diff);
		}
		return JHTML::date($date);
	}
}

JCli::getInstance('LatestTweets')->execute();
