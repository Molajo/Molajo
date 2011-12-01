<?php
/**
 * @version        $Id: language.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Framework
 * @subpackage    Form
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.language.helper');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class JFormFieldLanguage extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'Language';

    /**
     * Method to get the field options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        // Initialize some field attributes.
        $application = (string)$this->element['application'];
        if ($application != 'site' && $application != 'administrator') {
            $application = 'site';
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(
            parent::getOptions(),
            JLanguageHelper::createLanguageList($this->value, constant('JPATH_' . strtoupper($application)), true, true)
        );

        return $options;
    }
}
