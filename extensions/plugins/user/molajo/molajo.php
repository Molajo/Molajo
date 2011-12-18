<?php
/**
 * @version        $Id: molajo.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright    Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('MOLAJO') or die;

/**
 * Joomla User plugin
 *
 * @package        Joomla.Plugin
 * @subpackage    User.molajo
 * @since        1.5
 */
class plgUserMolajo extends MolajoPlugin
{
    /**
     * Utility method to act on a user after it has been saved.
     *
     * This method sends a registration email to new users created in the backend.
     *
     * @param    array        $user        Holds the new user data.
     * @param    boolean        $isnew        True if a new user is stored.
     * @param    boolean        $success    True if user was succesfully stored in the database.
     * @param    string        $msg        Message.
     *
     * @return    void
     * @since    1.6
     */
    public function onUserAfterSave($user, $isnew, $success, $msg)
    {
        // Initialise variables.

        $config = MolajoFactory::getApplication()->getConfig();

        if ($isnew) {
            // TODO: Suck in the frontend registration emails here as well. Job for a rainy day.

            // Load user_molajo plugin language (not done automatically).
            $lang = MolajoFactory::getLanguage();
            $lang->load('plg_user_molajo', JPATH_ADMINISTRATOR);

            // Compute the mail subject.
            $emailSubject = MolajoTextHelper::sprintf(
                'PLG_USER_JOOMLA_NEW_USER_EMAIL_SUBJECT',
                $user['name'],
                $config->get('sitename')
            );

            // Compute the mail body.
            $emailBody = MolajoTextHelper::sprintf(
                'PLG_USER_JOOMLA_NEW_USER_EMAIL_BODY',
                $user['name'],
                $config->get('sitename'),
                JUri::root(),
                $user['username'],
                $user['password_clear']
            );

            // Assemble the email data...the sexy way!
            $mail = MolajoFactory::getMailer()
                    ->setSender(
                array(
                     $config->get('mailfrom'),
                     $config->get('fromname')
                )
            )
                    ->addRecipient($user['email'])
                    ->setSubject($emailSubject)
                    ->setBody($emailBody);

            if (!$mail->Send()) {
                // TODO: Probably should raise a plugin error but this event is not error checked.
                JError::raiseWarning(500, MolajoTextHelper::_('ERROR_SENDING_EMAIL'));
            }
        }
        else {
            // Existing user - nothing to do...yet.
        }
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param    array    $user        Holds the user data
     * @param    array    $options    Array holding options (remember, autoregister, group)
     *
     * @return    boolean    True on success
     * @since    1.5
     */
    public function onUserLogin($user, $options = array())
    {
        $instance = $this->_getUser($user, $options);
        if (JError::isError($instance)) {
            return $instance;
        }

        if ($instance->get('block') == 1) {
            return JError::raiseWarning('SOME_ERROR_CODE', MolajoTextHelper::_('JERROR_NOLOGIN_BLOCKED'));
        }

        // Register the needed session variables
        $session = MolajoFactory::getSession();
        $session->set('user', $instance);

        $db = MolajoFactory::getDbo();

        MolajoFactory::getApplication()->checkSession();

        // Update the user related fields for the Joomla sessions table.
        $db->setQuery(
            'UPDATE `#__sessions`' .
            ' SET `user_id` = ' . (int)$instance->get('id') .
            ' WHERE `session_id` = ' . $db->quote($session->getId())
        );
        $db->query();

        // Hit the user last visit field
        $instance->setLastVisit();

        return true;
    }

    /**
     * This method should handle any logout logic and report back to the subject
     *
     * @param    array    $user        Holds the user data.
     * @param    array    $options    Array holding options (client, ...).
     *
     * @return    object    True on success
     * @since    1.5
     */
    public function onUserLogout($user, $options = array())
    {
        $my = MolajoFactory::getUser();
        $session = MolajoFactory::getSession();


        // Make sure we're a valid user first
        if ($user['id'] == 0 && !$my->get('tmp_user')) {
            return true;
        }

        // Check to see if we're deleting the current session
        if ($my->get('id') == $user['id']
            && $options['application_id'] == MOLAJO_APPLICATION_ID
        ) {
            // Hit the user last visit field
            $my->setLastVisit();

            // Destroy the php session for this user
            $session->destroy();
        }

        // Force logout all users with that user_id
        $db = MolajoFactory::getDbo();
        $db->setQuery(
            'DELETE FROM `#__sessions`' .
            ' WHERE `user_id` = ' . (int)$user['id'] .
            ' AND `application_id` = ' . (int)$options['application_id']
        );
        $db->query();

        return true;
    }

    /**
     * This method will return a user object
     *
     * If options['autoregister'] is true, if the user doesn't exist yet he will be created
     *
     * @param    array    $user        Holds the user data.
     * @param    array    $options    Array holding options (remember, autoregister, group).
     *
     * @return    object    A MolajoUser object
     * @since    1.5
     */
    protected function _getUser($user, $options = array())
    {
        $instance = MolajoUser::getInstance($user->username);

        if ($id = intval(MolajoUserhelper::getUserId($user->username))) {
            $instance->load($id);
            return $instance;
        }

        //TODO : move this out of the plugin
        $config = MolajoComponent::getParameters('users');
        // Default to Registered.
        $defaultUserGroup = $config->get('new_user_group', 2);

        $acl = MolajoFactory::getACL();

        $instance->set('id', 0);
        $instance->set('name', $user['fullname']);
        $instance->set('username', $user['username']);
        $instance->set('password_clear', $user['password_clear']);
        $instance->set('email', $user['email']); // Result should contain an email (check)
        $instance->set('groups', array($defaultUserGroup));

        //If autoregister is set let's register the user
        $autoregister = isset($options['autoregister']) ? $options['autoregister']
                : $this->parameters->get('autoregister', 1);

        if ($autoregister) {
            if (!$instance->save()) {
                return JError::raiseWarning('SOME_ERROR_CODE', $instance->getError());
            }
        } else {
            // No existing user and autoregister off, this is a temporary user.
            $instance->set('tmp_user', true);
        }

        return $instance;
    }

    /**
     * Remove all sessions for the user name
     *
     * Method is called after user data is deleted from the database
     *
     * @param    array        $user    Holds the user data
     * @param    boolean        $succes    True if user was succesfully stored in the database
     * @param    string        $msg    Message
     *
     * @return    boolean
     * @since    1.6
     */
    public function onUserAfterDelete($user, $success, $msg)
    {
        if ($success) {
        } else {
            return false;
        }

        $db = MolajoFactory::getDbo();
        $db->setQuery(
            'DELETE FROM `#__sessions`' .
            ' WHERE `user_id` = ' . (int)$user['id']
        );
        $db->Query();

        return true;
    }
}
