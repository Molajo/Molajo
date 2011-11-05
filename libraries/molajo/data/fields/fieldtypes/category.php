<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoFormFieldCategory
 *
 * Utility class for Extension Categories
 *
 * @static
 * @package        Molajo
 * @subpackage          HTML
 * @since        1.6
 */
class MolajoFormFieldCategory extends MolajoFormFieldList
{
    /**
     * Field Type
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'Category';

    /**
     * getOptions
     *
     * Method to get selection criteria for list generation options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        $options = array();

        /** extension scope **/
        $extension = $this->element['extension'] ? (string)$this->element['extension']
                : (string)$this->element['scope'];
        if (isset($this->element['show_root'])) {
            array_unshift($options, MolajoHTML::_('select.option', '0', MolajoText::_('JGLOBAL_ROOT')));
        }

        /** state **/
        $published = (string)$this->element['published'];
        if ($published) {
            $options = MolajoHTML::_('category.options', $extension, array('filter.published' => explode(',', $published)));
        } else {
            $options = MolajoHTML::_('category.options', $extension);
        }

        /** acl **/
        if ($action = (string)$this->element['action']) {
            foreach ($options as $i => $option) {
                if (!MolajoFactory::getUser()->authorise($action, $extension . '.category.' . $option->value)) {
                    unset($options[$i]);
                }
            }
        }

        /** merge in additional options and return list **/
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}