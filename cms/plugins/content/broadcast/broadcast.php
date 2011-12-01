<?php
/**
 * @package     Molajo
 * @subpackage  Broadcast
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class plgMolajoBroadcast extends MolajoPlugin
{

    /**
     * plgMolajoBroadcast::MolajoOnContentChangeState
     *
     * System Component Plugin activates tasks:
     *
     * 1: Email
     * 2: Ping
     * 3: Tweet
     *
     * @param    string        The context for the content passed to the plugin.
     * @param    object        primary key
     * @param    object        value
     * @since    1.6
     */
    function MolajoOnContentChangeState($context, $pks, $value)
    {
        /** broadcast published state, only **/
        if ($value == 1) {
        } else {
            return;
        }

        /** responses parameters **/
        $responsesParameters = MolajoComponent::getParameters('responses', true);

        /** broadcasting enabled **/
        if ($responsesParameters->def('enable_broadcast', 0) == '1') {
        } else {
            return;
        }

        /** extract table name from component name  **/
        $tempArray = array();
        $tempArray = explode('.', $context);
        $tableName = '#__' . substr($tempArray[0], 4, 999);

        /** implode primary keys into a list **/
        $idArray = implode(',', $pks);

        /** implode configured published categories into a list **/
        $categories = $responsesParameters->get('enable_broadcast_categories', array());

        if (count($categories) == 0) {
            return;
        }
        $categoryArray = implode(',', $categories);

        /** run query to retrieve key data **/
        $db = MolajoFactory::getDBO();

        //need to add this ->                       AND a.state <> '.(int) $value .'

        $query = 'SELECT a.id, a.title, a.alias, a.created_by, a.created_by_alias,
                    b.id as category_id, b.alias as category_alias
                    FROM ' . $tableName . ' a,
                        #__categories b 
                    WHERE a.catid = b.id
                      AND a.catid IN (' . $categoryArray . ')
                      AND a.id IN (' . $idArray . ')';

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if (count($rows) == 0) {
            return;
        }

        foreach ($rows as $row) {

            if ($row->created_by == 0) {
                $row->name = $row->created_by_alias;
                $row->username = '';
                $row->email = '';
            } else {

                $userQuery = 'SELECT a.name, a.username, a.email
                                FROM #__users a
                                WHERE a.id = ' . (int)$row->created_by . '
                                  AND a.block = 0
                                  AND a.send_email = 1
                                  AND a.activated = "" ';

                $db->setQuery($userQuery);
                $userResults = $db->loadObjectList();

                if (count($userResults) == 0) {
                    $row->name = $row->created_by_alias;
                    $row->username = '';
                    $row->email = '';
                } else {
                    foreach ($userResults as $user) {
                        $row->name = $user->name;
                        $row->username = $user->username;
                        $row->email = $user->email;
                    }
                }
            }
        }

        // $email_author = TamkaContentHelperRoute::getAuthorInfo ($article->id, $pluginParameters->get('author'));

        /** email **/
        if ($responsesParameters->def('enable_subscriptions', 0) == '1') {
            require_once dirname(__FILE__) . '/email/driver.php';
            MolajoBroadcastEmail::driver($rows);
        }

        /** ping **/
        if ($responsesParameters->def('enable_ping', 0) == '1') {
            require_once dirname(__FILE__) . '/ping/driver.php';
            MolajoBroadcastPing::driver($rows);
        }

        /** tweet **/
        if ($responsesParameters->def('enable_tweet', 0) == '1') {
            require_once dirname(__FILE__) . '/tweet/driver.php';
            MolajoBroadcastTweet::driver($rows);
        }

        /** Processing Complete **/
        return;
    }
}