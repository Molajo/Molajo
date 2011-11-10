<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('JPATH_BASE') or die;

/**
 * plgSystemExtend 
 *
 * NOTE: plgUserUserField and plgExtensionExtensionField are child objects of this Class for event standardization
 *
 * @package	Content
 * @subpackage	Fields
 * @version	1.6
 */
class plgSystemExtend extends MolajoPlugin
{
    /**
     * __construct
     *
     * @return	string
     */
    public function __construct(& $subject, $config = array())
    {
        parent::__construct($subject, $config);

        /** load mvc classes **/
        define('MOLAJO', true);
        define('MOLAJO_EXTEND_ROOT', dirname(__FILE__));
        
        /** load mvc classes **/
        require_once dirname(__FILE__).'/mvc/controller.php';
    }

    /**
     * onAfterInitialize
     *
     * @return	string
     */
    public function onAfterInitialize ()
    {
        return;
    }

    /**
     * onContentPrepare
     *
     *  $content nearly always the value "text"
     *
     *  Molajo 1.7 Wish List:
     *
     *  Provide descriptive $context values so that action can be taken or not taken, depending on the content
     *  All $context values and component table names should be available from component parameters
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content parameters
     * @param	stromg		The 'page' number
     * @return	string
     * @since	1.6
     */
    public function onContentPrepare ($context, &$content, &$parameters, $page = 0)
    {
        return true;
    }

    /**
     * onContentPrepareForm
     *
     * Event fired by JModelForm::preprocessForm for Add and Edit Tasks
     *
     * Molajo 1.7 Wish List:
     *
     * Fire OnContentPrepareFormData too like Core MVC Classes
     *
     * @param   JForm   $form   The form to be altered.
     * @param   array   $content   The associated data for the form.
     * @return  boolean
     *
     * @since	1.6
     */
    public function onContentPrepareForm ($form, $content)
    {

        /** before onContentAfterSave the onContentPrepareForm is run again without the content object for an unknown reason) **/
        if (!is_object($content)) {
            return;
        }
        return extendController::initiation ($task='edit', $form, $context=null, $content, $parameters=null, $limitstart=null, $isNew=null, $event='onContentPrepareForm');
    }

    /**
     * onContentValidate
     *
     * Molajo 1.7 Wish List
     *
     * 1. Create new onContentValidate event and fire from JModelForm::validate
     * 2. Make certain that Component Objects for Content and Form do not lose Custom Fields during error handling in parent MVC methods
     *
     * @param object $form
     * @param object $content
     *
     */
    public function onContentValidate ($form, $content)
    {
        return;
    }

    /**
     * onContentBeforeSave
     *
     * Method is called right before content is saved into the database.
     * Article object is passed by reference, so changes made here are saved
     * Returning false aborts the save with $content->setError($message)
     *
     * Molajo 1.7 Wish List
     *
     * Make certain that Component Objects for Content and Form do not lose Custom Fields during error handling in parent MVC methods
     *
     * @param	string		The context of the content passed to the plugin.
     * @param	object		A JTableContent object
     * @param	bool		If the content is just about to be created
     * @return	bool		If false, abort the save
     * @since	1.6
     */
    public function onContentBeforeSave ($context, &$content, $isNew)
    {
        return;
    }

    /**
     * onContentAfterSave and onExtensionAfterSave
     *
     * Method called after primary content is saved so that Custom Fields for the Component can also be saved
     *
     * Molajo 1.7 Wish List
     *
     * Make certain that Component Objects for Content and Form do not lose Custom Fields during error handling in parent MVC methods
     *
     * @param	string		The context of the content passed to the plugin (added in 1.6)
     * @param	object		A JTableContent object
     * @param	bool		If the content is just about to be created
     *
     * @since	1.6
     */
    public function onContentAfterSave ($context, &$content, $isNew)
    {
        return extendController::initiation ($task='save', $form=null, $context, $content, $parameters=null, $limitstart=null, $isNew=null, $event='onContentAfterSave');
    }
    /**
     * onContentBeforeDelete
     *
     * Editing could be added here
     *
     * @param	string	The context for the content passed to the plugin.
     * @param	object	The data relating to the content that is to be deleted.
     * @return	boolean
     * @since	1.6
     */
    public function onContentBeforeDelete ($context, $content)
    {
        return true;
    }

    /**
     * onContentAfterDelete
     *
     * Method called after Component data is deleted from the database
     *
     * Use to remove Custom Fields for Component when Component data is deleted
     *
     * @param	string		$content
     * @param	array           $content
     * @param	boolean		false
     *
     */
    public function onContentAfterDelete ($context, $content)
    {
        return extendController::initiation ($task='delete', $form=null, $context, $content, $parameters=null, $limitstart=null, $isNew=null, $event='onContentAfterDelete');
    }

    /**
     * onContentAfterTitle
     *
     * Method is called by the View
     *
     * Use to add custom fields to the Component Content object for the current record
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content parameters
     * @param	int		The 'page' number
     * @return	string
     * @since	1.6
     */
    public function onContentAfterTitle ($context, &$content, &$parameters, $limitstart)
    {
        return extendController::initiation ($task='display', $form=null, $context, $content, $parameters, $limitstart, $isNew=null, $event='onContentAfterTitle');
    }
}