<?php
/**
 * @package     Molajo
 * @subpackage  Attributes
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoAttributeAccept
 *
 * Populate Accept Attribute using Valid MIME Media Types for File Upload or Display
 *
 * @package     Molajo
 * @subpackage  Attributes
 * @since       1.0
 */
class MolajoAttributeAccept extends MolajoAttribute
{

    /**
     * __construct
     * 
	 * Method to instantiate the attribute object.
     * 
     * @param array $input
     * @param array $rowset
     * 
	 * @return  void
	 *
	 * @since   1.0
     */
	public function __construct($input = array(), $rowset = array())
	{
        parent::__construct();
        parent::__set('name', 'Accept');
        parent::__set('input', $input);        
        parent::__set('rowset', $rowset); 
	}

	/**
     * setValue
     *
	 * Method to set the Attribute Value
	 *
	 * @return  array   $rowset
     *
	 * @since   1.1
	 */
	protected function setValue()
	{

//accept="image/gif, image/jpeg"
  /**
  * MIME Media Types
  * - Source: ftp://ftp.iana.org/assignments/media-types/
  * MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES - 1000
  * MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES - 1010
  * MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES - 1020
  * MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES - 1030
  */
	}
}
