<?php
/**
 * @package     Molajo
 * @subpackage  Molajo System Plugin
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://Molajo.org/Copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Molajo System Plugin
 */
class plgSystemMolajo extends JPlugin
{
    var $app;

    /**
     * System Event: onAfterInitialise
     *
     * @return	string
     */
    public function __construct(& $subject, $config = array())
    {
        define('MOLAJO', true);
        parent::__construct($subject, $config);
	$this->loadLanguage();
        $this->app =& JFactory::getApplication();
        $this->dispatcher = JDispatcher::getInstance();
    }
    
    /**
     * System Event: onAfterInitialise
     *
     * @return	string
     */
    function onAfterInitialise()
    {

    /**                                                                                                 **/
    /** Overriding Core Molajo 1.6 and loading Molajo Library                                          **/
    /**                                                                                                 **/

    /**                                                                                                 **/
    /** 1. defines.php can be used to move sensitive files http://tinyurl.com/MoveSensitiveFiles16      **/
    /**                                                                                                 **/

    /**                                                                                                 **/
    /** 2. JForm Field Definitions                                                                      **/
    /**                                                                                                 **/

    /** Overriden JForm fields are in the molajo.xml file - addfieldpath contains link to files         **/

    /**                                                                                                 **/
    /** 3. JHTML Field Definitions                                                                      **/
    /**                                                                                                 **/

    /** view the JForm field previously mentioned for include that overrides core JHTML Field           **/

    /**                                                                                                 **/
    /** 4. Load Molajo library and classes (uncomment enqueueMessage to see in the Administrator)       **/
    /**                                                                                                 **/
        require_once JPATH_PLUGINS.'/molajo/libraries/mloader.php';        
        mimport('molajo.application.plugins.pluginhelper');
        mimport('molajo.helper.date');
        mimport('molajo.helper.image');
        mimport('molajo.helper.oembed');
        mimport('molajo.helper.text');
        mimport('molajo.helper.url');

        require_once JPATH_PLUGINS.'/molajo/libraries/curl.php';

    /**                                                                                                 **/
    /** 5. Register specific Classes (Note: those loaded in defines are needed before framework loads   **/
    /**                                                                                                 **/
//        JLoader::register('PageHelper', JPATH_PLUGINS.'/molajo/libraries/molajo/administrator/helper/page.php');

    /**                                                                                                 **/
    /** 6. Trigger Molajo Plugins for OnAfterInitialize                                                 **/
    /**                                                                                                 **/
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnAfterInitialise', array());
    }

    /**
     * System Event: onAfterRoute
     *
     * @return	string
     */
    function onAfterRoute()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnAfterRoute', array());
    }

    /**
     * Example prepare content method
     *
     * Method is called by the view
     *
     * @param	string	The context of the content being passed to the plugin.
     * @param	object	The form object.
     * @param	object	The form data
     * @param	int	The 'page' number
     * @since	1.6
     */
    function onContentPrepare($context, &$content, &$params, $page = 0)
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentPrepare', array($context, &$content, &$params, $page = 0));
    }

    /**
     * onContentPrepareData
     *
     * Method is called by the view
     *
     * @param	object	The context for the content passed to the plugin.
     * @param	object	The form data
     * @since	1.6
     */
    function onContentPrepareData($context, $data)
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentPrepareData', array($context, $data));
    }

    /**
     * onContentPrepareForm
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	int		The 'page' number
     * @return	string
     * @since	1.6
     */
    function onContentPrepareForm ($form, $data)
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentPrepareForm', array($form, $data));
    }

    /**
     * onContentAfterTitle
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	int		The 'page' number
     * @return	string
     * @since	1.6
     */
    function onContentAfterTitle($context, &$content, &$params, $page = 0)
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentAfterTitle', array($context, &$content, &$params, $limitstart));
    }

    /**
     * Example before display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	int		The 'page' number
     * @return	string
     * @since	1.6
     */
    function onContentBeforeDisplay ($context, &$content, &$params, $page = 0)
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentBeforeDisplay', array($context, &$content, &$params, $page = 0));
    }

    /**
     * Example after display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param	string		The context for the content passed to the plugin.
     * @param	object		The content object.
     * @param	object		The content params
     * @param	int		The 'page' number
     * @return	string
     * @since	1.6
     */
    function onContentAfterDisplay ($context, &$content, &$params, $page = 0)
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentAfterDisplay', array($context, &$content, &$params, $page = 0));
    }

    /**
     * System Event: onAfterDispatch
     *
     * @return	string
     */
    function onAfterDispatch()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnAfterDispatch', array());
    }

    /**
     * System Event: onBeforeRender
     *
     * @return	string
     */
    function onBeforeRender()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnBeforeRender', array());
    }

    /**
     * System Event: onAfterRender
     *
     * @return	string
     */
    function onAfterRender()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnAfterRender', array());
    }

    /**
     * System Event: onBeforeCompileHead
     *
     * @return	string
     */
    function onBeforeCompileHead()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnBeforeCompileHead', array());
    }

    /**
     * System Event: onSearch
     *
     * @return	string
     */
    function onSearch()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnSearch', array());
    }

    /**
     * System Event: onSearchAreas
     *
     * @return	string
     */
    function onSearchAreas()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnSearchAreas', array());
    }

    /**
     * System Event: onGetWebServices
     *
     * @return	string
     */
    function onGetWebServices()
    {
        if ($this->app->getName() == 'administrator') { return; }

        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnGetWebServices', array());
    }

   /**
     * CONTENT CRUD
     */

    /**
     * Content Event: onContentChangeState
     *
     * @param	string	The context for the content passed to the plugin.
     * @param	array	A list of primary key ids of the content that has changed state.
     * @param	int		The value of the state that the content has been changed to.
     * @return	boolean
     * @since	1.6
     */
    public function onContentChangeState($context, $pks, $value)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentChangeState', array($context, $pks, $value));
    }

    /**
     * Content Event: onContentBeforeSave
     *
     * Method is called right before content is saved into the database.
     * Article object is passed by reference, so any changes will be saved!
     * NOTE:  Returning false will abort the save with an error.
     * You can set the error by calling $content->setError($message)
     *
     * @param	string		The context of the content passed to the plugin.
     * @param	object		A JTableContent object
     * @param	bool		If the content is just about to be created
     * @return	bool		If false, abort the save
     * @since	1.6
     */
    public function onContentBeforeSave ($context, &$content, $isNew)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentBeforeSave', array($context, &$content, $isNew));
    }

    /**
     * Content Event: onContentAfterSave
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     * @param	string		The context of the content passed to the plugin (added in 1.6)
     * @param	object		A JTableContent object
     * @param	bool		If the content is just about to be created
     * @since	1.6
     */
    public function onContentAfterSave ($context, &$content, $isNew)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentAfterSave', array($context, &$content, $isNew));
    }

    /**
     * Content Event: onContentBeforeDelete
     *
     * @param	string	The context for the content passed to the plugin.
     * @param	object	The data relating to the content that is to be deleted.
     * @return	boolean
     * @since	1.6
     */
    public function onContentBeforeDelete($context, $data)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentBeforeDelete', array($context, $data));
    }

   /**
     * Content Event: onContentAfterDelete
     *
     * @param	string	The context for the content passed to the plugin.
     * @param	object	The data relating to the content that was deleted.
     * @return	boolean
     * @since	1.6
     */
    public function onContentAfterDelete ($context, $data)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnContentAfterDelete', array($context, $data));
    }

   /**
     * USER
     */

    /**
     * onUserBeforeSave
     *
     * Method is called before user data is stored in the database
     *
     * @param	array		$user	Holds the old user data.
     * @param	boolean		$isnew	True if a new user is stored.
     * @param	array		$new	Holds the new user data.
     *
     * @return	void
     * @since	1.6
     * @throws	Exception on error.
     */
    public function onUserBeforeSave($user, $isnew, $new)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnUserBeforeSave', array($user, $isnew, $new));
    }

    /**
     * onUserAfterSave
     *
     * Method is called after user data is stored in the database
     *
     * @param	array		$user		Holds the new user data.
     * @param	boolean		$isnew		True if a new user is stored.
     * @param	boolean		$success	True if user was succesfully stored in the database.
     * @param	string		$msg		Message.
     *
     * @return	void
     * @since	1.6
     * @throws	Exception on error.
     */
    public function onUserAfterSave($user, $isnew, $success, $msg)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnUserAfterSave', array($user, $isnew, $success, $msg));
    }

    /**
     * onUserBeforeDelete
     *
     * Method is called before user data is deleted from the database
     *
     * @param	array		$user	Holds the user data.
     *
     * @return	void
     * @since	1.6
     */
    public function onUserBeforeDelete($user)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnUserBeforeDelete', array($user));
    }

    /**
     * onUserAfterDelete
     *
     * Method is called after user data is deleted from the database
     *
     * @param	array		$user	Holds the user data.
     * @param	boolean		$succes	True if user was succesfully stored in the database.
     * @param	string		$msg	Message.
     *
     * @return	void
     * @since	1.6
     */
    public function onUserAfterDelete($user, $success, $msg)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnUserAfterDelete', array($user, $success, $msg));
    }

    /**
     * onUserLogin
     *
     * This method should handle any login logic and report back to the subject
     *
     * @param	array	$user		Holds the user data.
     * @param	array	$options	Extra options.
     *
     * @return	boolean	True on success
     * @since	1.5
     */
    public function onUserLogin($user, $options)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnUserLogin', array($user, $options));
    }

    /**
     * onUserLogout
     *
     * This method should handle any logout logic and report back to the subject
     *
     * @param	array	$user	Holds the user data.
     *
     * @return	boolean	True on success
     * @since	1.5
     */
    public function onUserLogout($user)
    {
        JPluginHelper::importPlugin('molajo');
        $results = $this->dispatcher->trigger('MolajoOnUserLogout', array($user));
    }
}