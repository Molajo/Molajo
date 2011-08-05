<?php
/**
 * @version     $id: molajosample.php
 * @package     Molajo
 * @subpackage  Molajosample Plugin  
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajosample Content Plugin
 *
 * @package		Molajo
 * @subpackage	Content Plugin
 * @since		1.6
 */
class plgContentMolajosample extends JPlugin
{
    
/**
 * DISPLAY METHOD - Events in order of occurrence
 *
 * 1. onContentPrepare - prepares custom data used to extend primary data for save
 * 2. onContentAfterTitle - augments primary form fields with additional custom data
 * 3. onContentBeforeDisplay - validates custom data
 * 4. onContentAfterDisplay - perform processing needed before save
 */ 
	/**
	 * 1. onContentPrepare
     *
     * Example prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param	string	$context    The context of the content being passed to the plugin.
	 * @param	object	$item       The content object.  Note $content->text is also available
	 * @param	object	$params     The content params
	 * @param	int		$limitstart The 'page' number
	 * @since	1.6
	 */
	public function onContentPrepare($context, &$content, &$params, $limitstart)
	{
        return true;
    }

	/**
	 * 2. onContentAfterTitle
     *
     * Example after display title method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string	$context    The context of the content being passed to the plugin.
	 * @param	object	$item       The content object.  Note $content->text is also available
	 * @param	object	$params     The content params
	 * @param	int		$limitstart The 'page' number
	 * @since	1.6
	 */
	public function onContentAfterTitle($context, &$content, &$params, $limitstart)
	{
        return true;
    }

	/**
	 * 3. onContentBeforeDisplay
     *
     * Example before display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string	$context    The context of the content being passed to the plugin.
	 * @param	object	$item       The content object.  Note $content->text is also available
	 * @param	object	$params     The content params
	 * @param	int		$limitstart The 'page' number
	 * @since	1.6
	 */
	public function onContentBeforeDisplay($context, &$content, &$params, $limitstart)
	{
        return true;
    }

	/**
	 * 4. onContentAfterDisplay
     *
     * Example after display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string	$context    The context of the content being passed to the plugin.
	 * @param	object	$item       The content object.  Note $content->text is also available
	 * @param	object	$params     The content params
	 * @param	int		$limitstart The 'page' number
	 * @since	1.6
	 */
	public function onContentAfterDisplay($context, &$content, &$params, $limitstart)
	{
        return true;
    }

/**
 * SAVE METHOD - Events in order of occurrence
 *
 * 1. onContentPrepareData - prepares custom data used to extend primary data for save
 * 2. onContentPrepareForm - augments primary form fields with additional custom data
 * 3. onContentValidateForm - validates custom data
 * 4. onContentBeforeSave - perform processing needed before save
 * 5. onContentSaveForm - used to save additional form data beyond the primary data
 * 6. onContentChangeState - handle change in state actions
 * 7. onContentAfterSave - respond to save event
 * 
 */    
	/**
     * 1. onContentPrepareData
     *
     * Save Method: prepares data in addition to primary data for save
     *
     * $context = JRequest::getVar('option').'.'.JRequest::getCmd('view').'.'.JRequest::getCmd('layout').'.'.$task.'.'.JRequest::getInt('datakey');
     *
     * JRequest::getInt('datakey') can be used to retrieve the form object
     *
     * $formName = JRequest::getVar('option').'.'.JRequest::getCmd('view').'.'.JRequest::getCmd('layout').'.'.JRequest::getCmd('task').'.'.JRequest::getInt('id').'.'.JRequest::getVar('datakey');
	 *
	 * @param	string	$context    The context for the content passed to the plugin.
	 * @param	object	$data       The data relating to the content that is being prepared for save.
	 * @return	boolean
	 * @since	1.6
	 */
	public function onContentPrepareData($context, $data)
	{               
		return true;
	}

	/**
	 * 2. onContentPrepareForm
     *
     * Save Method: augments primary form fields with additional custom data
     *
	 * @param	object	$form  Form object to be used during save
	 * @param	object	$data  Data returned from the model as validated
	 * @return	boolean
	 * @return	string
	 * @since	1.6
	 */
	public function onContentPrepareForm($form, $data)
	{
		return true;
	}

	/**
     * 3. onContentValidateForm
     *
     * Save Method: used to validate the data that will be saved that is in addition to the primary data
     *
	 * @param	object	$form       Form object to be used during save
	 * @param	object	$validData  Data returned from the model as validated
	 * @return	boolean
	 * @return	string
	 * @since	1.6
	 */
	public function onContentValidateForm($form, &$validData)
	{
		return true;
	}

    /**
     * 4. onContentBeforeSave
     * 
     * Save Method: used to preprocess save request
     *
     * @param	string	$context    The context for the content passed to the plugin.
     * @param	object	$validData  Data returned from the model as validated
     * @param	int		$isNew      The value of the state that the content has been changed to.
     * @return	boolean
     * @since	1.6
     */
    public function onContentBeforeSave($context, &$validData, $isNew)
    {
        return true;
    }

	/**
	 * 5. onContentSaveForm
     *
     * Save Method: used to save additional form data beyond the primary data
     *
	 * @param	object	$form       Form object to be used during save
	 * @param	object	$validData  Data returned from the model as validated
	 * @return	boolean
	 * @since	1.6
     */
	public function onContentSaveForm($form, $validData)
	{
		return true;
	}

	/**
  	 * 6. onContentChangeState
     *
     * Save Method: used to respond to changes in state values
     *
	 * @param	string	$context    The context for the content passed to the plugin.
	 * @param	array	$pks        A list of primary key ids of the content that has changed state.
	 * @param	int		$value      The value of the state that the content has been changed to.
	 * @return	boolean
	 * @since	1.6
     */
	public function onContentChangeState($context, $pks, $value)
	{
		return true;
	}    

    /**
     * 7. onContentAfterSave
     *
     * Save Method: used to respond to a save event after the save has happened
     *
     * @param	string	$context    The context for the content passed to the plugin.
     * @param	object	$validData  Data returned from the model as validated
     * @param	int		$isNew      The value of the state that the content has been changed to.
     * @return	boolean
     * @since	1.6
     */
    public function onContentAfterSave($context, &$validData, $isNew)
    {
        return true;
    }

/**
 * DELETE METHOD - Events in order of occurrence
 *
 * 1. onContentBeforeDelete - processing required before delete
 * 2. onContentAfterDelete - respond to delete event
 *
 */

    /**
     * 1. onContentBeforeDelete
     *
     * Delete Method: Processing required before the deletion of content
     *
     * $context = JRequest::getVar('option').'.'.JRequest::getCmd('view').'.'.JRequest::getCmd('layout').'.'.'delete';
     *
     * @param	string	$context    The context for the content passed to the plugin.
     * @param	object	$data       Data returned from the model as validated
     * @return	boolean
     * @since	1.6
     */
    public function onContentBeforeDelete($context, $data)
    {
        return true;
    }

    /**
     * 2. onContentAfterDelete
     *
     * Delete Method: Processing needed before the deletion of content
     *
     * @param	string	$context    The context for the content passed to the plugin.
     * @param	object	$data       Data returned from the model as validated
     * @return	boolean
     * @since	1.6
     */
    public function onContentAfterDelete($context, $data)
    {
        return true;
    }
}
