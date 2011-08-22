<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Image Helper
 *
 * @package     Molajo
 * @subpackage  Query Helper
 * @since       1.0
 */
class MolajoQueryHelper
{
	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param	string	$orderby	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * @since	1.5
	 */
	public static function orderbyPrimary($orderby)
	{
		switch ($orderby)
		{
			case 'alpha' :
				$orderby = 'c.path, ';
				break;

			case 'ralpha' :
				$orderby = 'c.path DESC, ';
				break;

			case 'order' :
				$orderby = 'c.lft, ';
				break;

			default :
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for secondary category ordering.
	 *
	 * @param	string	$orderby	The ordering code.
	 * @param	string	$orderDate	The ordering code for the date.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * @since	1.5
	 */
	public static function orderbySecondary($orderby, $orderDate = 'created')
	{
		$queryDate = self::getQueryDate($orderDate);

		switch ($orderby)
		{
			case 'date' :
				$orderby = $queryDate;
				break;

			case 'rdate' :
				$orderby = $queryDate.' DESC ';
				break;

			case 'alpha' :
				$orderby = 'a.title';
				break;

			case 'ralpha' :
				$orderby = 'a.title DESC';
				break;

			case 'hits' :
				$orderby = 'a.hits DESC';
				break;

			case 'rhits' :
				$orderby = 'a.hits';
				break;

			case 'order' :
				$orderby = 'a.ordering';
				break;

			case 'author' :
				$orderby = 'author';
				break;

			case 'rauthor' :
				$orderby = 'author DESC';
				break;

			case 'front' :
				$orderby = 'fp.ordering';
				break;

			default :
				$orderby = 'a.ordering';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param	string	$orderDate	The ordering code.
	 *
	 * @return	string	The SQL field(s) to order by.
	 * @since	1.0
	 */
	public static function getQueryDate($orderDate) {

		switch ($orderDate)
		{
			case 'modified' :
				$queryDate = ' CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END';
				break;

			// use created if publish_up is not set
			case 'published' :
				$queryDate = ' CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END ';
				break;

			case 'created' :
			default :
				$queryDate = ' a.created ';
				break;
		}

		return $queryDate;
	}

	/**
	 * Get join information for the voting query.
	 *
	 * @param	MolajoRegistry	$param	An options object for the dog.
	 *
	 * @return	array		A named array with "select" and "join" keys.
	 * @since	1.5
	 */
	public static function buildVotingQuery($params=null)
	{
		if (!$params) {
			$params = MolajoComponentHelper::getParams('com_dogs');
		}

		$voting = $params->get('show_vote');

		if ($voting) {
			// calculate voting count
			$select = ' , ROUND(v.rating_sum / v.rating_count) AS rating, v.rating_count';
			$join = ' LEFT JOIN #__dogs_rating AS v ON a.id = v.content_id';
		}
		else {
			$select = '';
			$join = '';
		}

		$results = array ('select' => $select, 'join' => $join);

		return $results;
	}

	/**
	 * Method to order the intro dogs array for ordering
	 * down the columns instead of across.
	 * The layout always lays the introtext dogs out across columns.
	 * Array is reordered so that, when dogs are displayed in index order
	 * across columns in the layout, the result is that the
	 * desired dog ordering is achieved down the columns.
	 *
	 * @param	array	$dogs	Array of intro text dogs
	 * @param	integer	$numColumns	Number of columns in the layout
	 *
	 * @return	array	Reordered array to achieve desired ordering down columns
	 * @since	1.0
	 */
	public static function orderDownColumns($dogs, $numColumns = 1)
	{
		$count = count($dogs);

		// just return the same array if there is nothing to change
		if ($numColumns == 1 || !is_array($dogs) || $count <= $numColumns) {
			$return = $dogs;
		}
		// we need to re-order the intro dogs array
		else {
			// we need to preserve the original array keys
			$keys = array_keys($dogs);

			$maxRows = ceil($count / $numColumns);
			$numCells = $maxRows * $numColumns;
			$numEmpty = $numCells - $count;
			$index = array();

			// calculate number of empty cells in the array


			// fill in all cells of the array
			// put -1 in empty cells so we can skip later

			for ($row = 1, $i = 1; $row <= $maxRows; $row++)
			{
				for ($col = 1; $col <= $numColumns; $col++)
				{
					if ($numEmpty > ($numCells - $i)) {
						// put -1 in empty cells
						$index[$row][$col] = -1;
					}
					else {
						// put in zero as placeholder
						$index[$row][$col] = 0;
					}
					$i++;
				}
			}

			// layout the dogs in column order, skipping empty cells
			$i = 0;
			for ($col = 1; ($col <= $numColumns) && ($i < $count); $col++)
			{
				for ($row = 1; ($row <= $maxRows) && ($i < $count); $row++)
				{
					if ($index[$row][$col] != - 1) {
						$index[$row][$col] = $keys[$i];
						$i++;
					}
				}
			}

			// now read the $index back row by row to get dogs in right row/col
			// so that they will actually be ordered down the columns (when read by row in the layout)
			$return = array();
			$i = 0;
			for ($row = 1; ($row <= $maxRows) && ($i < $count); $row++)
			{
				for ($col = 1; ($col <= $numColumns) && ($i < $count); $col++)
				{
					$return[$keys[$i]] = $dogs[$index[$row][$col]];
					$i++;
				}
			}
		}
		return $return;
	}
}