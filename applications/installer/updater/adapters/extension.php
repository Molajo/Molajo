<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension class for updater
 *
 * @package     Joomla.Platform
 * @subpackage  Updater
 * @since       11.1
 * */
class MolajoUpdaterExtension extends MolajoUpdateAdapter
{
    /**
     * Start element parser callback.
     *
     * @param   object  $parser  The parser object.
     * @param   string  $name    The name of the element.
     * @param   array   $attrs   The attributes of the element.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function _startElement($parser, $name, $attrs = array())
    {
        array_push($this->_stack, $name);
        $tag = $this->_getStackLocation();
        // reset the data
        eval('$this->' . $tag . '->_data = "";');

        switch ($name)
        {
            case 'UPDATE':
                $this->current_update = MolajoTable::getInstance('update');
                $this->current_update->extension_site_id = $this->_extension_site_id;
                $this->current_update->details_url = $this->_url;
                break;
            // Don't do anything
            case 'UPDATES':
                break;
            default:
                if (in_array($name, $this->_updatecols)) {
                    $name = strtolower($name);
                    $this->current_update->$name = '';
                }
                if ($name == 'TARGETPLATFORMS') {
                    $this->current_update->targetplatform = $attrs;
                }
                break;
        }
    }

    /**
     * Character Parser Function
     *
     * @param   object  $parser  Parser object.
     * @param   object  $name    The name of the element.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function _endElement($parser, $name)
    {
        array_pop($this->_stack);
        //echo 'Closing: '. $name .'<br />';
        switch ($name)
        {
            case 'UPDATE':
                $ver = new JVersion;
                $product = strtolower(JFilterInput::getInstance()->clean($ver->PRODUCT, 'cmd')); // lower case and remove the exclamation mark
                // Check that the product matches and that the version matches (optionally a regexp)
                if ($product == $this->current_update->targetplatform['NAME']
                    && preg_match('/' . $this->current_update->targetplatform['VERSION'] . '/', $ver->RELEASE)
                ) {
                    // Target platform isn't a valid field in the update table so unset it to prevent J! from trying to store it
                    unset($this->current_update->targetplatform);
                    if (isset($this->latest)) {
                        if (version_compare($this->current_update->version, $this->latest->version, '>') == 1) {
                            $this->latest = $this->current_update;
                        }
                    }
                    else
                    {
                        $this->latest = $this->current_update;
                    }
                }
                break;
            case 'UPDATES':
                // :D
                break;
        }
    }

    /**
     * Character Parser Function
     *
     * @param   object  $parser  Parser object.
     * @param   object  $data    The data.
     *
     * @return  void
     *
     * @note    This is public because its called externally.
     * @since   1.0
     */
    protected function _characterData($parser, $data)
    {
        $tag = $this->_getLastTag();
        //if(!isset($this->$tag->_data)) $this->$tag->_data = '';
        //$this->$tag->_data .= $data;
        if (in_array($tag, $this->_updatecols) || $tag == 'INFOURL') {
            $tag = strtolower($tag);
            $this->current_update->$tag .= $data;
        }
    }

    /**
     * Finds an update.
     *
     * @param   array  $options  Update options.
     *
     * @return  array  Array containing the array of update sites and array of updates
     *
     * @since   1.0
     */
    public function findUpdate($options)
    {
        $url = $options['location'];
        $this->_url = &$url;
        $this->_extension_site_id = $options['extension_site_id'];
        //echo '<p>Find update for extension run on <a href="'. $url .'">'. $url .'</a></p>';
        if (substr($url, -4) != '.xml') {
            if (substr($url, -1) != '/') {
                $url .= '/';
            }
            $url .= 'extension.xml';
        }

        $dbo = $this->parent->getDbo();

        if (!($fp = @fopen($url, "r"))) {
            $query = $dbo->getQuery(true);
            $query->update('#__extension_sites');
            $query->set('enabled = 0');
            $query->where('extension_site_id = ' . $this->_extension_site_id);
            $dbo->setQuery($query);
            $dbo->Query();

            JLog::add("Error opening url: " . $url, JLog::WARNING, 'updater');

            MolajoController::getApplication()->setMessage(MolajoTextHelper::sprintf('JLIB_UPDATER_ERROR_EXTENSION_OPEN_URL', $url), 'warning');
            return false;
        }

        $this->xml_parser = xml_parser_create('');
        xml_set_object($this->xml_parser, $this);
        xml_set_element_handler($this->xml_parser, '_startElement', '_endElement');
        xml_set_character_data_handler($this->xml_parser, '_characterData');

        while ($data = fread($fp, 8192))
        {
            if (!xml_parse($this->xml_parser, $data, feof($fp))) {
                JLog::add("Error parsing url: " . $url, JLog::WARNING, 'updater');

                MolajoController::getApplication()->setMessage(MolajoTextHelper::sprintf('JLIB_UPDATER_ERROR_EXTENSION_PARSE_URL', $url), 'warning');
                return false;
            }
        }
        xml_parser_free($this->xml_parser);
        if (isset($this->latest)) {
            $updates = array($this->latest);
        }
        else
        {
            $updates = array();
        }
        return array('extension_sites' => array(), 'updates' => $updates);
    }
}
