<?php
/**
 * @version     $id: mlist.php
 * @package     Molajo
 * @subpackage  List Options
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**  REMOVE WHEN COMPLETE WITH Fields **/

/**
 * Utility class for creating HTML Lists
 *
 * @package        Molajo
 * @subpackage    HTML
 * @since        1.6
 */
abstract class MolajoHtmlList
{
    /**
     * accessOptions
     *
     * points to Core JHTML Library
     *
     * @since    1.6
     */
    public static function accessOptions($config = array())
    {
        return MolajoHTML::_('access.assetgroups');
    }

    /**
     * authorsOptions
     *
     * points to Core JHTML Library
     *
     * @since    1.6
     */
    public static function authorOptions($config = array())
    {
        $defaultView = JRequest::getCmd('DefaultView');
        $authorModel = JModel::getInstance('Model'.ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $authorModel->getAuthors();
    }

    /**
     * categoryOptions
     *
     * points to Core JHTML Library
     *
     * @since    1.6
     */
    public static function categoryOptions($config = array())
    {
        return MolajoHTML::_('category.options', JRequest::getCmd('option'));
    }

    /**
     * featureOptions
     *
     * Returns an array of standard featured state filter options.
     *
     * @param    array            An array of configuration options.
     * @return    string            The HTML code for the select tag
     *
     * @since    1.6
     */
    public static function featureOptions($config = array())
    {
        $options = array();
        if (!array_key_exists('unfeatured', $config) || $config['unfeatured']) {
            $options[] = MolajoHTML::_('select.option', '0', MolajoText::_('MOLAJO_OPTION_UNFEATURED'));
        }
        if (!array_key_exists('featured', $config) || $config['featured']) {
            $options[] = MolajoHTML::_('select.option', '1', MolajoText::_('MOLAJO_OPTION_FEATURED'));
        }

        return $options;
    }

    /**
     * languageOptions
     *
     * @since    1.6
     */
    public static function languageOptions($config = array())
    {
        return MolajoHTML::_('contentlanguage.existing', true, true);
    }

    /**
     * publishDateOptions
     *
     * @since    1.6
     */
    public static function publishDateOptions($config = array())
    {
        $defaultView = JRequest::getCmd('DefaultView');
        $publishDateModel = JModel::getInstance('Model'.ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $publishDateModel->getMonthsPublish();
    }

    /**
     * createDateOptions
     *
     * @since    1.6
     */
    public static function createDateOptions($config = array())
    {
        $defaultView = JRequest::getCmd('DefaultView');
        $createDateModel = JModel::getInstance('Model'.ucfirst(JRequest::getCmd('DefaultView')), ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
        return $createDateModel->getMonthsCreate();
    }

    /**
     * updateDateOptions
     *
     * @since    1.6
     */
    public static function updateDateOptions($config = array())
    {
        $defaultView = JRequest::getCmd('DefaultView');
        $updateDateModel = JModel::getInstance('Model'.ucfirst($defaultView), ucfirst($defaultView), array('ignore_request' => true));
        return $updateDateModel->getMonthsUpdate();
    }

    /**
     * titleOptions
     *
     * points to Core JHTML Library
     *
     * @since    1.6
     */
    public static function titleOptions($config = array())
    {
        $defaultView = JRequest::getCmd('DefaultView');
        $updateDateModel = JModel::getInstance('Model'.ucfirst($defaultView), ucfirst($defaultView), array('ignore_request' => true));
        return $updateDateModel->getTitles();
    }

    /**
     * publishOptions
     *
     * Returns an array of standard published state filter options.
     *
     * @param    array            An array of configuration options.
     * @return    string            The HTML code for the select tag
     *
     * @since    1.6
     */
    public static function stateOptions($config = array())
    {
        $options = array();
        if (!array_key_exists('archived', $config) || $config['archived']) {
            $options[] = MolajoHTML::_('select.option', '2', MolajoText::_('MOLAJO_OPTION_ARCHIVED'));
        }
        if (!array_key_exists('published', $config) || $config['published']) {
            $options[] = MolajoHTML::_('select.option', '1', MolajoText::_('MOLAJO_OPTION_PUBLISHED'));
        }
        if (!array_key_exists('unpublished', $config) || $config['unpublished']) {
            $options[] = MolajoHTML::_('select.option', '0', MolajoText::_('MOLAJO_OPTION_UNPUBLISHED'));
        }
        if ($parameters->def(config_component_state_spam, 0) == '1') {
            if (!array_key_exists('spam', $config) || $config['spam']) {
                $options[] = MolajoHTML::_('select.option', '-1', MolajoText::_('MOLAJO_OPTION_SPAMMED'));
            }
        }
        if (!array_key_exists('trash', $config) || $config['trash']) {
            $options[] = MolajoHTML::_('select.option', '-2', MolajoText::_('MOLAJO_OPTION_TRASHED'));
        }
        if ($parameters->def(config_component_version_management, 1) == '1') {
            if (!array_key_exists('version', $config) || $config['version']) {
                $options[] = MolajoHTML::_('select.option', '-10', MolajoText::_('MOLAJO_OPTION_VERSION'));
            }
        }
        if (!array_key_exists('all', $config) || $config['all']) {
            $options[] = MolajoHTML::_('select.option', '*', MolajoText::_('MOLAJO_OPTION_ALL'));
        }
        return $options;
    }

    /**
     * stickyOptions
     *
     * Returns an array of standard stickied state filter options.
     *
     * @param    array            An array of configuration options.
     * @return    string            The HTML code for the select tag
     *
     * @since    1.6
     */
    public static function stickyOptions($config = array())
    {
        $options = array();
        if (!array_key_exists('unstickied', $config) || $config['unstickied']) {
            $options[] = MolajoHTML::_('select.option', '0', MolajoText::_('MOLAJO_OPTION_UNSTICKIED'));
        }
        if (!array_key_exists('stickied', $config) || $config['stickied']) {
            $options[] = MolajoHTML::_('select.option', '1', MolajoText::_('MOLAJO_OPTION_STICKIED'));
        }
        return $options;
    }
}