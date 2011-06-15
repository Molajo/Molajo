<?php
/**
 * @package		Joomla! 1.6 Developer Distribution http://AllTogetherAsAWhole.org
 * @copyright	Copyright (C) 2010 Authors. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class testClass
{
	public function logFunction ()
	{
		/** Initialize */
		$today = getdate();
		$fileName = dirname(__FILE__).'/'.'log.txt';
		$fileContents = '';
		
		/** Create or Read Log file */		
		if (testClass::existsFile ($fileName)) {
			$fileContents = testClass::readFile ($fileName); 
		} else {
			testClass::createFile ($fileName, $fileContents);
		}
		
		/** Append new date time */		
		$fileContents .= 'testClass::testFunction'.$today['mon'].'/'.$today['mday'].'/'.$today['year'].' '.$today['hours'].':'.$today['minutes'].':'.$today['seconds'].chr(10);
		
		/** Write file */		
		testClass::writeFile ($fileName, $fileContents);		
	}
	
	/**
	 * existsFolder
	 * @param object $folderName
	 * @return 
	 */
		function existsFile ($fileName) 	
		{
			if (JFile::exists($fileName)) {
				return true;
			} else {
				return false;
			}		
		}	
			
	/**
	 * readFile
	 * @param object $fileName
	 * @return 
	 */
	function readFile ($fileName) 
	{		
		return JFile::read($fileName);
	}	
	
	/**
	 * makeSafe
	 * @param object $fileName
	 * @return 
	 */	
	function makeSafe ($fileName) 
	{				
		if (JFile::makeSafe($fileName))	{
			return $fileName;
		} else {
			return false;
		}	
	}
			
	/**
	 * createFile
	 * @param object $fileName
	 * @param object $fileContents
	 * @return 
	 */	
	function createFile ($fileName, $fileContents) 
	{				
		if (JFile::exists($fileName))	{
		} else {
				if (JFile::write($fileName, $fileContents)) {
				} else {
					return false;
				}
		}	
	}
	
			
	/**
	 * writeFile
	 * @param object $fileName
	 * @param object $fileContents
	 * @return 
	 */	
	function writeFile ($fileName, $fileContents) 
	{				
			if (JFile::write($fileName, $fileContents)) {
			} else {
				return false;
			}
	}	
}