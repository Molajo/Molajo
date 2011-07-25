<?php
/**
 * @version Ê 1.6.2 June 9, 2011
 * @author Ê ÊRocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license Ê http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted index access');

class RTCacheClean {
	
	function clean($ids = array())
	{
		$conf = MolajoFactory::getConfig();

		// setup options with site cachebase
		$options = array(
			'defaultgroup'	=> '',
			'storage' 		=> $conf->get('cache_handler', ''),
			'caching'		=> true,
			'cachebase'		=> $conf->get('cache_path', JPATH_SITE.DS.'cache')
		);

		// clean out site caches
		$cache = JCache::getInstance('', $options);
		$site_caches = array_keys($cache->getAll());
		foreach ($site_caches as $key=>$group) {
			$cache->clean($group);
		}

		// modify options to use admin cachebase
		$options['cachebase'] = JPATH_ADMINISTRATOR.DS.'cache';
		$cache = JCache::getInstance('', $options);
		$admin_caches = array_keys($cache->getAll());
		foreach ($admin_caches as $key=>$group) {
			$cache->clean($group);
		}

	}

	function getCount() {

			$conf = MolajoFactory::getConfig();
			$count = 0;

			// setup options with site cachebase
			$options = array(
				'defaultgroup'	=> '',
				'storage' 		=> $conf->get('cache_handler', ''),
				'caching'		=> true,
				'cachebase'		=> $conf->get('cache_path', JPATH_SITE.DS.'cache')
			);

			// clean out site caches
			$cache = JCache::getInstance('', $options);
			$site_caches = array_keys($cache->getAll());
			
			$count = sizeof($site_caches);

			// modify options to use admin cachebase
			$options['cachebase'] = JPATH_ADMINISTRATOR.DS.'cache';
			$cache = JCache::getInstance('', $options);
			$admin_caches = array_keys($cache->getAll());
			
			$count += sizeof($admin_caches);
			

			return($count);
	}
	
}