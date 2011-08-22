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
 * @subpackage  Image Helper
 * @since       1.0
 */
class MolajoImageHelper   {

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */

	/**
	 * getImage
     *
     * Build an SQL query to select an image.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function getImage($id, $size=0)
	{
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);

        $query->select('a.c_filename');
		$query->select('a.c_file');
		$query->select('a.c_filesize');
		$query->from('`#__images` AS a');
        if (is_numeric($id)) {
            $query->where('a.id = '.(int) $id);
        }
        $db->setQuery($query->__toString());
        $results = $db->loadAssocList();

        /** write the file */
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        require_once dirname(__FILE__).'/resize-class.php';

        /** folders */
        if (JFolder::exists(MOLAO_PATH_SITE.'/images/lc')) {
        } else {
            JFolder::create(MOLAO_PATH_SITE.'/images/lc');
        }
        if (JFolder::exists(MOLAO_PATH_SITE.'/images/lc/thumbs')) {
        } else {
            JFolder::create(MOLAO_PATH_SITE.'/images/lc/thumbs');
        }

        /** paths */
        $imagePath = MOLAO_PATH_SITE.'/images/lc/'.$results[0]['c_filename'];
        $imagePathThumb = MOLAO_PATH_SITE.'/images/lc/thumbs/'.$results[0]['c_filename'];
        $imagePathSrc = '../images/lc/'.$results[0]['c_filename'];
        $imagePathSrcThumb = '../images/lc/thumbs/'.$results[0]['c_filename'];

        /** save normal size */
        if (JFile::exists($imagePath)) {
        } else {
            header('Content-type: '.'jpg');
            header('Content-length: '.$results[0]['c_filesize']);

            if(JFile::write($imagePath, $results[0]['c_file'])) {
            } else {
                echo JText::_( 'Error writing image to images folder' );
                return;
            }
        }

        /** create thumb */
        if (JFile::exists($imagePathThumb)) {
        } else {
            // *** 1) Initialise / load image
            $resizeObj = new resize($imagePath);

            // *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
            $resizeObj -> resizeImage(75, 75, 'crop');

            // *** 3) Save image
            $resizeObj -> saveImage($imagePathThumb, 100);
        }

        /** return image thumb or normal size */
        if ($size == 1) {
            return $imagePathSrcThumb;
        } else {
            return $imagePathSrc;
        }
	}
}