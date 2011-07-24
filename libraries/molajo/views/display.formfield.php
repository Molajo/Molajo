<?php
/**
 * @package     Molajo
 * @subpackage  Formfield View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * View to Display Output for Formfields
 *
 * @package	    Molajo
 * @subpackage	Form Fields View
 * @since	    1.0
 */
class MolajoViewFormfield extends MolajoView
{
    /**
     * @var $options object
     *
     * Contains all options which can be retrieved as this->state->get('option_name')
     *
     * 1. Filters and filtered values (for Administrator) - ex. $this->state->get('filter.category')
     *
     * 2. Merged Component Parameters (Global Options, Menu Item, Item)
     *    A. Including those used as selection criteria - ex. $this->state->get('filter.category')
     *    B. And those parameters needed by the layout - ex. $this->option->get('layout.show_title')
     *
     * 3. Component Request Variables
     *    $this->state->get('request.option'), and 'component_' + model, view, layout, default_view, single_view and task
     *
     * 4. 
     *
     */

    /**
     * display
     *
     * View for Display View that uses no forms
     *
     * @param null $tpl
     * @return bool
     */
    public function display($tpl = null)
    {
        /** 1. Get State */
        $this->state      = $this->get('State');

        /** 2. Get System Variables */
//        parent::display($tpl);

        /** 3. Output Layout for System Requests */
        //echo $this->getColumns ('system');

        /** 4. Retrieve Query Results */
        $this->rowset     = $this->get('Items');

        /** 5. Retrieve Layout Parameters */
        if (JFactory::getApplication()->getName() == 'site') {
           $this->params = JFactory::getApplication()->getParams();
   //         $this->_mergeParams ();
//		$this->getState('request.option')->get('page_class_suffix', '') = htmlspecialchars($this->params->get('pageclass_sfx'));
        } else {
           $this->params = MolajoComponentHelper::getParams(JRequest::getCmd('option'));
        }

        /** process model errors */
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $layoutFolder = $this->findPath($this->state->get('request.layout'));
        if ($layoutFolder === false) {
            parent::display($tpl);
        } else {
            echo $this->renderMolajoLayout ($layoutFolder);
        }
    }
}