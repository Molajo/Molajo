<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Compress CSS Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.file');

class compressCSS extends MolajoPlugin	{
	
	function onAfterRender()	{		

	/**
	 * 	Initialization
	 */
		$app =& MolajoFactory::getApplication();
		if($app->getName() != 'site') {
			return;
		}
			
	/**
	 * 	If not existing, create temporary folder to store dynamic CSS files
	 */
		jimport('joomla.filesystem.folder');		
		$store_path = JPATH_ROOT . '/tmp/_css';
		
		if (!JFolder :: exists($store_path) && !JFolder :: create($store_path)) {
			$response->type = MOLAJO_AUTHENTICATE_STATUS_FAILURE;
			$response->error_message = "Could not create the folder " . $store_path . " Please check permissions.";
			return;
		}

	/**
	 * 	Delete all files older than number of minutes specified -- (3600*24) is one day
	 */
		$plugin =& MolajoPluginHelper::getPlugin('system', 'tamka_compress_css');
		$pluginParams = new JParameter( $plugin->params );
		$pluginParams->def('minutes', 60);
		
		$cssFiles = JFolder::files( $store_path, '(css|js)$', false, false );
		for($i = 0; $i < count($cssFiles); $i++) {
			if (filemtime($store_path.DS.$cssFiles[$i]) < (time() - ($pluginParams->def('minutes', 60) * 60))) {
				if (!JFile::delete($store_path.DS.$cssFiles[$i])) {
					$response->type = MOLAJO_AUTHENTICATE_STATUS_FAILURE;
					$response->error_message = "Could not delete the file " . $store_path.DS.$cssFiles[$i] . " Please check permissions.";
					return;
				}				
			}
		}
		
	/**
	 * 	Initialization 
	 */ 
		$document =& MolajoFactory::getDocument();
		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();
		$tagEnd	= ' />';
		$oldHtml = '';
			
		$headers = $document->getHeadData();
		$buffer = JResponse::getBody();		
		
		$uri	 = &MolajoFactory::getURI();
		$urlhost = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));		
		$urlhost .= JURI::base(true);		

	/**
	 * 	Process CSS from extensions 
	 * 		a. Regenerate the same stylesheet set that has been output to Buffer
	 * 		b. Read all CSS into a single variable to write to a single CSS file 
	 * 		c. Append file names together to use as a comparison to previously created compressed files
	 */		
   		$cssMergedExtensions = '';
   		$cssMergedExtensionsFilenames = '';
   		
		foreach ($headers['styleSheets'] as $strSrc => $strAttr )
		{
			$oldHtml .= $tab . '<link rel="stylesheet" href="'.$strSrc.'" type="'.$strAttr['mime'].'"';
			if (!is_null($strAttr['media'])){
				$oldHtml .= ' media="'.$strAttr['media'].'" ';
			}
			if ($temp = JArrayHelper::toString($strAttr['attribs'])) {
				$oldHtml .= ' '.$temp;;
			}			
			$oldHtml .= $tagEnd.$lnEnd;
			
			$cssMergedExtensions .= JFile::read($strSrc);	
			$cssMergedExtensionsFilenames .= $strSrc;	
		}	
		
	/**
	 * 	Same as previous, except process jdoc Type2=CSS Statements
	 */		
		$matches = array();
		$cssMergedJDOC = array();
   		$cssMergedJDOCFilenames = array();
		$countJDOC = 0;		
		
		if (preg_match_all('#<jdoc:include\ type2="([^"]+)" (.*)\/>#iU', $buffer, $matches))
		{
			$countJDOC = count($matches[1]);

			for($i = 0; $i < $countJDOC; $i++)
			{
				$attribs = JUtility::parseAttributes( $matches[2][$i] );
				$type2  = $matches[1][$i];

				$files  = isset($attribs['files']) ? $attribs['files'] : null;
				$path  = isset($attribs['path']) ? $attribs['path'] : null;
				$media  = isset($attribs['media']) ? $attribs['media'] : null;	
				 
				$fileArray = explode(',', $files);
				
				$cssMergedJDOC[$i][0] = '';
				$cssMergedJDOC[$i][1] = $media;	
				$cssMergedJDOCFilenames[$i][0] = '';			
				
				$fileCount = count($fileArray);

				for($j = 0; $j < $fileCount; $j++)
				{				
					$cssMergedJDOC[$i][0] .= JFile::read($urlhost . JURI::base(true) . $path . trim($fileArray[$j]));
					$cssMergedJDOCFilenames[$i][0] .= JURI::base(true) . $path . trim($fileArray[$j]);
				}
			}

			//	Remove JDOC statement from buffer
			$buffer = str_replace($matches[0], '', $buffer);
		}
 
	/**
	 * 	There is no extension CSS to combine and compress
	 */		
		if (($cssMergedExtensions == "") && ($countJDOC == 0) ) {
			return;
		}

	/**
	 * 	Compress using Minify
	 */
		require_once(JPATH_BASE . DS . 'plugins' . DS . 'system' . DS. 'tamka_compress_css' . DS . 'css.php');		
		$cssMergedExtensions = Minify_CSS_Compressor::process ($cssMergedExtensions);
		
		for($i = 0; $i < $countJDOC; $i++) {
			$cssMergedJDOC[$i][0] = Minify_CSS_Compressor::process ($cssMergedJDOC[$i][0]);
		}
				
	/**
	 *  Write the merged CSS to a single file
	 *  	a. Extension CSS
	 *  	b. JDOC CSS by Media Type
	 */		
	
		$file = MolajoUtility::getHash($cssMergedExtensionsFilenames) . '.css';
		$compressedCSSFile = $store_path.DS.$file;
		$hrefFileName = $urlhost . JURI::base(true) . '/tmp/_css/' . $file;
		
		/*	Use existing file if it exists			*/
		if (!JFile::exists($compressedCSSFile))	{		
			if (!JFile::write($compressedCSSFile, $cssMergedExtensions)) {
				$response->type = MOLAJO_AUTHENTICATE_STATUS_FAILURE;
				$response->error_message = "Could not create the file " . $compressedCSSFile . " Please check permissions.";
				return false;	
			}
		}
		$hrefFileNameJDOC = array();
		
		for($i = 0; $i < $countJDOC; $i++) {
			
			$file = MolajoUtility::getHash($cssMergedJDOCFilenames [$i][0]) . '.css';
			$compressedCSSFile = $store_path.DS.$file;
			$hrefFileNameJDOC[$i][0] = $urlhost . JURI::base(true) . '/tmp/_css/' . $file;
			$hrefFileNameJDOC[$i][1] = $cssMergedJDOC[$i][1];
			
			/*	Use existing file if it exists			*/
			if (!JFile::exists($compressedCSSFile))	{
				if (!JFile::write($compressedCSSFile, $cssMergedJDOC[$i][0])) {
					$response->type = MOLAJO_AUTHENTICATE_STATUS_FAILURE;
					$response->error_message = "Could not create the file " . $compressedCSSFile . " Please check permissions.";
					return false;
				}
			}
		}	

	/**
	 * 	Create New Stylesheet statements
	 * 		a. For Extensions CSS
	 * 		b. For JDOC Statements
	 */		
		$newHtml = '';			
		$type = 'text/css';
		$media = 'screen,projection';
		$newHtml .= $tab . '<link rel="stylesheet" href="'.$hrefFileName.'" type="'.$type.'"';
		if (!is_null($media)){
			$newHtml .= ' media="'.$media.'" ';
		}
		$newHtml .= $tagEnd.$lnEnd;

		for($i = 0; $i < $countJDOC; $i++) {		
			$type = 'text/css';
			$media = $hrefFileNameJDOC[$i][1];
			$newHtml .= $tab . '<link rel="stylesheet" href="'.$hrefFileNameJDOC[$i][0].'" type="'.$type.'"';

			if (!is_null($media)){
				$newHtml .= ' media="'.$media.'" ';
			}
			$newHtml .= $tagEnd.$lnEnd;
		}		

	/**
	 * 	Replace existing CSS include statements in Buffer with single line
	 */
		if ($oldHtml == '') {
			$oldHtml = '</title>';
			$newHtml = '</title>' .$lnEnd . $newHtml;
		} 
		$buffer = str_ireplace($oldHtml, $newHtml, $buffer);

		JResponse::setBody($buffer);
		
		return true;
	}
}