<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
require_once dirname(__FILE__) . '/html/category.php';

/**
 * Utility class for Extension Categories
 *
 * @static
 * @package        Molajo
 * @subpackage          HTML
 * @since        1.6
 */
class JFormFieldCategory extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'Category';

    /**
     * Method to get the field options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {

        // Initialize variables.
        $session = MolajoFactory::getSession();
        $options = array();

        // Initialize some field attributes.
        $extension = $this->element['extension'] ? (string)$this->element['extension']
                : (string)$this->element['scope'];
        $published = (string)$this->element['published'];

        // Filter over published state or not depending upon if it is present.
        if ($published) {
            $options = JHtml::_('category.options', $extension, array('filter.published' => explode(',', $published)));
        }
        else {
            $options = JHtml::_('category.options', $extension);
        }

        // Verify permissions.  If the action attribute is set, then we scan the options.
        if ($action = (string)$this->element['action']) {

            // Get the current user object.
            $user = MolajoFactory::getUser();

            // TODO: Add a preload method to JAccess so that we can get all the asset rules in one query and cache them.
            // eg JAccess::preload('create', 'articles.category')
            foreach ($options as $i => $option) {
                // Unset the option if the user isn't authorised for it.
                if (!$user->authorise($action, $extension . '.category.' . $option->value)) {
                    unset($options[$i]);
                }
            }
        }

        if (isset($this->element['show_root'])) {
            array_unshift($options, JHtml::_('select.option', '0', MolajoTextHelper::_('JGLOBAL_ROOT')));
        }

        // if no value exists, try to load a selected filter category from the list view
        if (!$this->value && ($this->form instanceof JForm)) {
            $context = $this->form->getName();
            $this->value = $session->get($context . '.filter.category_id', $this->value);
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}