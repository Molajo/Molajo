<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Validate Helper
 *
 * @package     Molajo
 * @subpackage  Validate Helper
 * @since       1.0
 */
class MolajoValidateHelper
{
    /**
     * checkRequest
     *
     * determine if it's a frontend or administrator request and process accordingly
     *
     * @return void
     */
    public function checkRequest ()
    {
        if (JFactory::getApplication()->getName() == 'site') {
            return $this->checkAdministratorRequest();
        } else {
            return $this->checkAdministratorRequest();
        }
    }

    /**
     *  checkRequest
     *
     *  @return boolean
     */
    public function checkSiteRequest ()
    {
    }

    /**
     *  getSite
     *
     *  @return boolean
     */
    public function checkAdministratorRequest ()
    {
/**
http://localhost/molajo2/administrator/index.php?option=com_samples
http://localhost/molajo2/administrator/index.php?option=com_samples&view=samples
http://localhost/molajo2/administrator/index.php?option=com_samples&task=sample.add
http://localhost/molajo2/administrator/index.php?option=com_samples&task=sample.edit&id=1
http://localhost/molajo2/administrator/index.php?option=com_samples&task=sample.cancel
http://localhost/molajo2/administrator/index.php?option=com_samples&task=sample.save&id=1

http://localhost/molajo2/administrator/index.php?option=com_samples&task=sample.edit&id=1
**/
        $lookupView = '';
        $molajoConfig = new MolajoModelConfiguration;

$debug = false;

        $layout = '';
        $format = '';
        $componentTable = '';

        /** loads keys for configuration **/
        $molajoConfig->getOptionOverrides (JRequest::getCmd('option'));

        /** 1. Get View and Layout **/
        $view = JRequest::getCmd('view', '');
        $layout = JRequest::getCmd('layout', 'default');

if ($debug) { echo 'view: '.$view.'<br />'; }

        /** 2. Retrieve Task **/
        $task = JRequest::getCmd('task', null);
        if ($task == null) {
            if ($layout == 'editor') {
                if (JRequest::getInt('id') == 0) {
                    $task = 'add';
                } else {
                    $task = 'edit';
                }
            } else {
                $task = 'display';
            }
        }

if ($debug) { echo 'task (from request): '.$task.'<br />'; }

        /** 3. Determine Controller **/
        if (strpos($task,'.')) {
            $controller = substr($task, 0, strpos($task,'.'));
            $task = substr($task, (strpos($task,'.')+1), 99);
        } else {
            $controller = '';
        }

        if ($view == '') {
            $view = $controller;
        }
if ($debug) { echo 'controller: '.$controller.'<br />'; }
if ($debug) { echo 'task (split): '.$task.'<br />'; }

        /** 4. Default View **/
        if ($view == '') {
            $lookupView = $molajoConfig->getDefaultView (MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS);

if ($debug) {
echo 'view (default): '.$lookupView.'<br />';
}
            if ($lookupView === false) {
                JFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_VIEW_DEFINED'), 'error');
                return false;
            }

        }

        /** 5. For view in hand, determine if it's single or default **/
        if ($lookupView == '') {
            $lookupView = $view;
        }
        $lookupViewType = $molajoConfig->getViewType (MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS, $lookupView);
        if ($lookupViewType === false) {

if ($debug) {
echo 'ERROR: type of view not found - bad error';

}
            JFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_VIEW_TYPE').' '.$lookupView, 'error');
            return false;

        }

if ($debug) {
echo 'type (of view): '.$lookupViewType.'<br />';
}

        /** 6. Knowing both view and type, determine values for single and default **/
        $otherView = $molajoConfig->getViewMatch (MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS, $lookupView, $lookupViewType);
        if ($otherView === false) {
            JFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_MATCHING_VIEW').' '.$lookupView, 'error');
            return false;
        }


if ($debug) {
echo 'otherView: '.$otherView.'<br />';
}

        if ($lookupViewType == 'default') {
            $defaultView = $lookupView;
            $singleView = $otherView;
        } else {
            $defaultView = $otherView;
            $singleView = $lookupView;
        }



if ($debug) { echo '$defaultView: '.$defaultView.'<br />';
echo '$singleView: '.$singleView.'<br />';
}

        /** 8. display controller for add and edit */
        if ($task == 'add' || $task == 'edit') {
            $view = $singleView;
            $controller = '';

        } else if ($task == 'display') {
            $view = $defaultView;
            $controller = '';

        } else if ($view == '' && ($controller == $defaultView || $controller == $singleView)) {
            /** used in redirects, not for an actual view/layout **/
            $view = $controller;
        }


if ($debug) {
echo 'view (from controller): '.$view.'<br />';
}

if ($debug) {
echo '$defaultView: '.$defaultView.'<br />';
echo '$controller: '.$controller.'<br />';
}

        /** 9. validate task and controller **/
        if ($controller == '') {
            $results = $molajoConfig->validateTask($task, MOLAJO_CONFIG_OPTION_ID_DISPLAY_CONTROLLER_TASKS);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_DISPLAY_CONTROLLER').' '.$task);
                return false;
            }

        } else if ($controller == $defaultView) {
            $results = $molajoConfig->validateTask ($task, MOLAJO_CONFIG_OPTION_ID_MULTIPLE_CONTROLLER_TASKS);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_MULTIPLE_CONTROLLER').' '.$task);
                return false;
            }

        } else if ($controller == $singleView) {
            $results = $molajoConfig->validateTask ($task, MOLAJO_CONFIG_OPTION_ID_SINGLE_CONTROLLER_TASKS);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_SINGLE_CONTROLLER').' '.$task);
                return false;
            }

        } else  {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_CONTROLLER').' '.$controller);
                return false;
        }


if ($debug) {
echo 'valid tasks for controller: '.$task.'<br />';
}


        if ($task == 'display' || $task == 'add' || $task == 'edit') {

            /** 4. display controller */
            if ($task == 'add' || $task == 'edit') {
                $view = $singleView;
            } else {
                $view = $defaultView;
            }

            /** 10. validate view (display only) **/
            if (JFactory::getApplication()->getName() == 'administrator') {
                $folder = MOLAJO_PATH_ADMINISTRATOR.'/components/'.JRequest::getCmd('option').'/views/'.$view;
            } else {
                $folder = MOLAJO_PATH_SITE.'/components/'.JRequest::getCmd('option').'/views/'.$view;
            }

            if (JFolder::exists($folder)) {
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_VIEW_FOLDERNAMES').' '.$folder, 'error');
                return false;
            }


if ($debug) {
echo 'view folder (display only) '.$folder.'<br />';
}



           /** 12. validate format (display only) **/
            $format = JRequest::getCmd('format', 'html');
            $results = $molajoConfig->validateFormat ($format, MOLAJO_CONFIG_OPTION_ID_FORMAT);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_FORMAT').' '.$format);
                return false;
            }


if ($debug) {
echo 'format (display only): '.$format.'<br />';
}

            /** 13. validate layout (display only) **/
            $layout = JRequest::getCmd('layout', '');

            if ($layout == '') {
                //if (JFactory::getApplication()->getName() == 'administrator') {
                    $layout = 'default';
                //} else {
//                    $menus = JFactory::getApplication()->getMenu('site');
//                    $active = $menus->getActive();
                    
//                    if ($active && $active->component == $option) {
//                        echo $active->id;
//                    }
                //}
            }
 
            if (JFactory::getApplication()->getName() == 'administrator') {
                $fileName = MOLAJO_PATH_ADMINISTRATOR.'/components/'.JRequest::getCmd('option').'/views/'.$view.'/tmpl/'.$layout.'.php';
                if ($view == $defaultView && $layout == 'default') {
                    $layout = 'manager';
                }
                if ($view == $singleView && $layout == 'default') {
                    $layout = 'edit';
                }
            } else {
                $fileName = MOLAJO_PATH_SITE.'/components/'.JRequest::getCmd('option').'/views/'.$view.'/tmpl/'.$layout.'.php';
            }

            if (JFile::exists($fileName)) {

            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_LAYOUT').' '.$fileName, 'error');
                return false;
            }

if ($debug) {
echo 'layout (display only): '.$layout.'<br />';
echo 'layout folder (display only): '.$layout.'<br />';
}


        }

        /** 15. set acl implementation selected for component **/
        $aclImplementation = $molajoConfig->getSingleConfigurationValue (MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION);
        if ($aclImplementation == false) {
            $aclImplementation = 'core';
        }

        /** 15. id **/
        $id = JRequest::getInt('id');
        $cids = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($cids);

        if ($task == 'add') {
            $id = 0;
            $cids = array();

        } else if ($task == 'edit' || $task == 'restore') {
            if ($id > 0 && count($cids) == 0) {
            } else if ($id == 0 && count($cids) == 1) {
                $id = $cids[0];
                $cids = array();
            } else if ($id == 0 && count($cids) == 0) {
                JError::raiseError(500, JText::_('MOLAJO_ERROR_TASK_MUST_HAVE_REQUEST_ID_TO_EDIT'));
                return false;
            } else if (count($cids) > 1) {
                JError::raiseError(500, JText::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_REQUEST_ID'));
                return false;
            }
        }


        /** 11. validate table (display only) **/
        $componentTable = $molajoConfig->getSingleConfigurationValue (MOLAJO_CONFIG_OPTION_ID_TABLE);
        if ($componentTable == false || $componentTable == '') {
             $componentTable = '__'.$defaultView;
        }

        /** frontend */

        if ($layout == 'editor') {
            if ((int) $id == 0) {
                $task = 'add';
            } else {
                $task = 'edit';
            }
        }

if ($debug) {
echo 'id: '.$id.'<br />';
}
        /** add '.' back to interface with JController **/
        if ($task == 'edit') {
            $layout = 'editor';
        }

        /** add '.' back to interface with JController **/
        if ($task == 'add' || $task == 'edit' || $task == 'display') {
        } else {
            $task = $controller.'.'.$task;
        }

        /** set request to validated values **/
        JRequest::setVar('single_view', $singleView);
        JRequest::setVar('default_view', $defaultView);

        JRequest::setVar('format', $format);
        JRequest::setVar('id', (int) $id);
        JRequest::setVar('cid', (array) $cids);

        JRequest::setVar('component_table', $componentTable);
        JRequest::setVar('filterFieldName', 'config_manager_list_filters');
        JRequest::setVar('selectFieldName', 'config_manager_grid_column');
        JRequest::setVar('aclImplementation', $aclImplementation);

        JRequest::setVar('controller', $controller);

        JRequest::setVar('task', $task);
        JRequest::setVar('layout', $layout);
        JRequest::setVar('view', $view);

if ($debug) {
echo 'the very end of validate'.'<br />';
echo '$lookupViewType '.$lookupViewType.'<br />';
echo '$controller '.$controller.'<br />';
echo '$view '.$view.'<br />';
echo '$defaultView '.$defaultView.'<br />';
echo '$singleView '.$singleView.'<br />';
echo '$layout '.$layout.'<br />';
echo '$task '.$task.'<br />';
echo '$controller '.$controller.'<br />';
echo 'the very end of validate'.'<br />';
die();
}
        return true;
    }
}