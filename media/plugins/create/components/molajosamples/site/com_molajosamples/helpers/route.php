<?php
/**
 * @version     $id: route.php
 * @package     Molajo
 * @subpackage  Route Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajosamplesRouteHelper
 *
 * @package	Molajo
 * @subpackage	HelperRoute
 * @since	1.6
 */
class MolajosamplesRouteHelper
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $singleItemParam = 'molajosamples';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $typeParam = 'Molajosample';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $componentOptionParam = 'com_molajosamples';

    /**
     * getMolajosampleRoute
     *
     * @param  int      $id
     * @param  int      $catid
     * @param  int      $data
     *
     * @return string
     */
	public function getMolajosampleRoute ($id, $catid, $data)
    {
        $routeHelper = new MolajoRouteHelper ();
        return $routeHelper->getItemRoute($id, $catid, $data, $this->singleItemParam, $this->typeParam, $this->componentOptionParam);
    }

    /**
     * getCategoryRoute
     *
     * @param  $data
     * @param  $catid
     * @return string
     */
	public function getCategoryRoute($data, $catid)
    {
        $routeHelper = new MolajoRouteHelper ();
        return $routeHelper->getCategoryRoute($data, $catid, $this->singleItemParam, $this->typeParam, $this->componentOptionParam);
    }

    /**
     * getFormRoute
     *
     * @param  $data
     * @param  $catid
     * @param  $id
     * @return string
     */
	public function getFormRoute($data, $catid, $id)
    {
        $routeHelper = new MolajoRouteHelper ();
        return $routeHelper->getFormRoute($data, $catid, $id, $this->singleItemParam, $this->typeParam, $this->componentOptionParam);
    }
}