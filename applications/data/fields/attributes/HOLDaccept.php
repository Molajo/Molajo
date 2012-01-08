<?php
/**
 * @package     Molajo
 * @subpackage  Attributes
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
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
        $MIMEtypes = array();

        $filetypes = explode(',', $this->element['filetype']);
        if (count($filetypes) == 0) {
            $filetypes = array();
            $filetypes[] == MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO;
            $filetypes[] == MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO;
            $filetypes[] == MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO;
            $filetypes[] == MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO;
        }

        foreach ($filetypes as $type) {

            $request = 0;

            if ($type == MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO || strtolower($type = 'audio')) {
                $request = MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE;
                $literal = 'audio';
            } else if ($type == MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE || strtolower($type = 'image') || strtolower($type == 'images')) {
                $request = MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE;
                $literal = 'image';
            } else if ($type == MOLAJO_EXTENSION_OPTION_ID_MIMES_TEXT || strtolower($type = 'text')) {
                $request = MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE;
                $literal = 'text';
            } else if ($type == MOLAJO_EXTENSION_OPTION_ID_MIMES_VIDEO || strtolower($type = 'video') || strtolower($type == 'videos')) {
                $request = MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE;
                $literal = 'video';
            }

            if ((int)$request > 0) {
                $temp = $this->retrieveList($request, $literal);
            }

            $MIMEtypes = array_merge((array)$MIMEtypes, (array)$temp);
        }

        /** $this->value */
        if ($MIMEtypes == '') {
            $value = '';
        } else {
            $value = 'accept="' . implode(',', $MIMEtypes . '"');
        }
        parent::__set('value', $value);

        /** $this->rowset */
        $this->rowset[0]['accept'] = $this->value;

        /** return array of attributes */
        return $this->rowset;
    }

    /**
     * retrieveList
     *
     * Method to retrieve list of configuration values for MIME type
     *
     * @return  array   $rowset
     *
     * @since   1.1
     */
    protected function retrieveList($MIME_type_id, $MIME_literal)
    {
        $option = 'dealwiththis';
        $molajoConfig = new MolajoModelConfiguration(array('option' => $option));
        $MIMEtypes = $molajoConfig->getOptionList($MIME_type_id);

        $formattedList = array();

        if (count($MIMEtypes)) {
            foreach ($MIMEtypes as $type) {
                $formattedList[] = $MIME_literal . '/' . $type;
            }
        }

        return $formattedList;
    }
}
