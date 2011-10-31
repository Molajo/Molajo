<?php
/**
 * @package		Molajo 1.6 Developer Distribution http://AllTogetherAsAWhole.org
 * @copyright	Copyright (C) 2010 Authors. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * 	Cron Job
 */
class plgSystemCron extends MolajoPlugin
{

	public function onAfterRender ()
	{

		/**
		 * Method fired off by first visitor and must continue, without timing out, after visitor leaves the site
		 */
		ignore_user_abort(true);
		set_time_limit(0);
		$count = 0;
		$time = 60 * (int) $this->params->get('minutes', 60);
		
		while(1) {

			// See if the current url exists in the database as a redirect.
			$db = MolajoFactory::getDBO();
			$db->setQuery(
				'SELECT `require_once`, `user_function`' .
				' FROM `#__cron`' .
				' WHERE `enabled` = 1 ' .
				' ORDER BY ordering '
			);

			$results = $db->loadRowList();

			if ($db->getErrorNum()) {
				$this->_subject->setError($db->getErrorMsg());
				return false;
			}
			foreach ($results as $cron) {

				if (JFile::exists(JPATH_SITE.'/'.$cron[0])) {
					require_once JPATH_SITE.'/'.$cron[0];
					call_user_func($cron[1]);
				} else {
					return false;
				}
			}
							
		    // Sleep Minutes and Continue
    		sleep($time);
		}		
	}
}
