<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldLanguage extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'Language';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   1.0
     */
    protected function getOptions()
    {
        // Initialise variables.
        $app = MolajoFactory::getApplication();

        // Detect the native language.
        $native = MolajoLanguageHelper::detectLanguage();
        if (empty($native)) {
            $native = 'en-GB';
        }

        // Get a forced language if it exists.
        $forced = $app->getLocalise();
        if (!empty($forced['language'])) {
            $native = $forced['language'];
        }

        // If a language is already set in the session, use this instead
        $session = MolajoFactory::getSession()->get('setup.options', array());
        if (!empty($session['language'])) {
            $native = $session['language'];
        }

        // Get the list of available languages.
        $options = MolajoLanguageHelper::createLanguageList($native);
        if (!$options || MolajoError::isError($options)) {
            $options = array();
        }

        // Set the default value from the native language.
        $this->value = $native;

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
