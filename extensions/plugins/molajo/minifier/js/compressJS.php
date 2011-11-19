<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Compress JS Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.file');

class CompressJS extends MolajoPlugin	{
	
	function onAfterRender()	{		

	/**
	 * 	Initialization
	 */
		$app =& MolajoFactory::getApplication();
		if($app->getName() != 'site') {
			return true;
		}
	
	/**
	 * 	Create temporary folder, if it doesn't already exist, to store all dynamic Javascript files
	 */		
		$store_path = JPATH_ROOT.'/tmp/_js';
		
		if (!JFolder :: exists($store_path) && !JFolder :: create($store_path)) {
			$response->type = MOLAJO_AUTHENTICATE_STATUS_FAILURE;
			$response->error_message = "Could not create the folder ".$store_path." Please check permissions.";
			return false;
		}		
		
	/**
	 * 	Delete all files older than number of minutes specified -- (3600*24) is one day
	 */
		$plugin =& MolajoPluginHelper::getPlugin('system', 'tamka_compress_js');
		$pluginParameters = new JParameter( $plugin->parameters );
		
		$jsFiles = JFolder::files( $store_path, '(css|js)$', false, false );
		for($i = 0; $i < count($jsFiles); $i++) {
			if (filemtime($store_path.DS.$jsFiles[$i]) < (time() - ($pluginParameters->def('minutes', 60) * 60))) {
				if (!JFile::delete($store_path.DS.$jsFiles[$i])) {
					$response->type = MOLAJO_AUTHENTICATE_STATUS_FAILURE;
					$response->error_message = "Could not delete the file ".$store_path.DS.$jsFiles[$i]." Please check permissions.";
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
		$oldHtml = '';
			
		$headers = $document->getHeadData();
		$buffer = JResponse::getBody();		
		
		$uri	 = &MolajoFactory::getURI();
		$urlhost = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));		
		$urlhost .= JURI::base(true);	
		
	/**
	 * 	Process JS from extensions 
	 * 		a. Regenerate the same stylesheet set that has been output to Buffer
	 * 		b. Read all JS into a single variable to write to a single JS file 
	 */
   		$jsMergedFileContents = '';
   		
		foreach ($headers['scripts'] as $strSrc => $strType )
		{
			$oldHtml .= $tab.'<script type="'.$strType.'" src="'.$strSrc.'"></script>'.$lnEnd;
			if (substr($strSrc, 0, 4) == 'http') {
				$jsMergedFileContents .= JFile::read($strSrc);
			} else {
				$jsMergedFileContents .= JFile::read($urlhost.$strSrc);
			}
		}	
	
		$oldInlineScript = '';
		$newInlineScript = '';
		foreach ($headers['script'] as $type => $content)		
		{
			if ($type == 'text/javascript') {
				$oldInlineScript .= $tab.'<script type="'.$type.'">'.$lnEnd;			
				$oldInlineScript .= $content.$lnEnd;			
				$oldInlineScript .= $tab.'</script>'.$lnEnd;
	
				$newInlineScript .= $content.$lnEnd;	
			}		
		}
		
	/**
	 * 	There is no extension JS to combine and compress
	 */		
		if (($jsMergedFileContents == "") && ($oldInlineScript == "") ) {
			return;
		}

	/**
	 * 	Compress using Minify
	 */
		require_once(JPATH_BASE.DS.'plugins'.DS.'system'.DS. 'tamka_compress_js'.DS.'js.php');
		$jsMergedFileContents = JSMin::minify ($jsMergedFileContents);
				
	/**
	 *  Output merged JS into a single file
	 */		
		$file = MolajoUtility::getHash($cssMergedExtensionsFilenames).'.js';
		$compressedJSFile = $store_path.DS.$file;
		$hrefFileName = JURI::base(true).'/tmp/_js/'.$file;
		
		/*	Use existing file if it exists			*/
		if (!JFile::exists($compressedJSFile))	{		
			if (!JFile::write($compressedJSFile, $jsMergedFileContents.$lnEnd.$newInlineScript)) {
				$response->type = MOLAJO_AUTHENTICATE_STATUS_FAILURE;
				$response->error_message = "Could not create the file ".$compressedJSFile." Please check permissions.";
				return false;	
			}
		}

	/**
	 * 	Remove Javascript File links from buffer
	 */
		$buffer = JResponse::getBody();
		$buffer = str_ireplace($oldHtml, '', $buffer);

	/**
	 * 	Remove Inline Javascript from buffer
	 */		
		$buffer = str_ireplace($oldInlineScript, '', $buffer);		

	/**
	 * 	Create New Javascript File links for Merged file 
	 */		
		$newHtml = '';			
		$type = 'text/javascript';
		$newHtml .= '<script type="'.$type.'" src="'.$hrefFileName.'"></script>'.$lnEnd;
		
	/**
	 * 	Position new Javascript File link at bottom of page 
	 */
		$buffer = substr($buffer, 0, strripos ($buffer, '</body>')). $newHtml.'</body>'. $lnEnd .'</html>';
				
	/**
	 * 	Re-write buffer
	 */
		JResponse::setBody($buffer);
		
		return true;
	}
}