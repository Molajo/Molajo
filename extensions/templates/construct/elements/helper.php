<?php
/**
 * @package		Unified HTML5 Template Framework for Joomla!+
 * @author		Cristina Solana http://nightshiftcreative.com
 * @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die;

/**
 * ConstructTemplateHelper
 *
 * Helper functions for the Construct Template Framework
 *
 * @package	Molajo
 * @subpackage	Helper
 * @since	1.0
 */
class ConstructTemplateHelper
{
    /** @var array */
    public $includeFile = array ();

    /**
     * getLayoutOverride
     *
     * determine if file is available and return path or false condition
     *
     * usage:
     *
     * 1. include Joomla File and Folder classes
     *
     * jimport('joomla.filesystem.file');
     * jimport('joomla.filesystem.folder');
     *
     * 2. instantiate the ConstructTemplateHelper class
     * if (JFile::exists(dirname(__FILE__).'/library/template.php')) {
     *     include dirname(__FILE__).'/library/template.php';
     * }
     * $helper = new ConstructTemplateHelper ();
     *
     * 3. populate the ConstructTemplateHelper property $this->includeFile (an associative array)
     *
     * $helper->includeFile = array
     *  ('file1Index'       => $this->template.'/layouts/index.php',
     * 	    'file2Index' 	=> $this->template.'/layouts/component/'.$currentComponent.'.php',
     * 		'file3Index' 	=> $this->template.'/layouts/section/section-'.$sectionId.'.php',
     * 	);
     *
     * 4. call the ConstructTemplateHelper getIncludeFile method
     *
     * 	$results = $helper->getIncludeFile ();
     * 	if ($results === false) {
     * 	    $alternateIndexFile = $this->template.'/layouts/index.php';
     * 	} else {
     * 	    $alternateIndexFile = $results;
     * 	}
     *
     * @return string
     */
    function getIncludeFile ()
    {
        if (count($this->includeFile) == 0) {
            return false;
        }

        foreach ($this->includeFile as $name => $path) {
            // return first file that exists
            if(JFile::exists(JPATH_BASE.'/'.$path)) {
                RETURN $path;
            }
        }
        // not found
        RETURN false;
    }
}