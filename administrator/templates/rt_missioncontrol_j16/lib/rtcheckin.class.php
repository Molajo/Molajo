<?php
/**
 * @version Ê 1.6.2 June 9, 2011
 * @author Ê ÊRocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license Ê http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted index access');

class RTCheckin {
	
	function checkin($ids = array())
	{
		$app		=& MolajoFactory::getApplication();
		$db			=& MolajoFactory::getDBO();
		$nullDate	= $db->getNullDate();

		if (!is_array($ids)) {
			return;
		}

		// this int will hold the checked item count
		$results = 0;

		foreach ($ids as $tn) {

			// make sure we get the right tables based on prefix
			if (stripos($tn, $app->getCfg('dbprefix')) !== 0) {
				continue;
			}

			$fields = $db->getTableFields(array($tn));

			if (!(isset($fields[$tn]['checked_out']) && isset($fields[$tn]['checked_out_time']))) {
				continue;
			}

			$query = $db->getQuery(true)
				->update($db->nameQuote($tn))
				->set('checked_out = 0')
				->set('checked_out_time = '.$db->Quote($nullDate))
				->where('checked_out > 0');
			if (isset($fields[$tn]['editor'])) {
				$query->set('editor = NULL');
			}

			$db->setQuery($query);
			if ($db->query()) {
				$results = $results + $db->getAffectedRows();
			}
		}
		return $results;
	}

	function getCheckouts($total = false) {

			$app		=& MolajoFactory::getApplication();
			$db			=& MolajoFactory::getDBO();
			$nullDate	= $db->getNullDate();
			$tables 	= $db->getTableList();

			// this array will hold table name as key and checked in item count as value
			if ($total)
				$results = 0;
			else
				$results = array();

			foreach ($tables as $i => $tn)
			{
				// make sure we get the right tables based on prefix
				if (stripos($tn, $app->getCfg('dbprefix')) !== 0)
				{
					unset($tables[$i]);
					continue;
				}

				$fields = $db->getTableFields(array($tn));

				if (!(isset($fields[$tn]['checked_out']) && isset($fields[$tn]['checked_out_time'])))
				{
					unset($tables[$i]);
					continue;
				}
			}
			foreach ($tables as $tn)
			{

				$query=$db->getQuery(true)
					->select('COUNT(*)')
					->from($db->nameQuote($tn))
					->where('checked_out > 0');

				$db->setQuery($query);
				if ($db->query()) {
					$result = $db->loadResult();
					if ($total)
						$results += (int)$result;
					else
						$results[$tn] = $result;
				} else {
					continue;
				}
			}

			return($results);
	}
	
}