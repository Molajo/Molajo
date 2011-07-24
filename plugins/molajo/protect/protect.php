<?php
/**
 * @package     Molajo
 * @subpackage  Molajo Protect Plugin
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;



class plgMolajoProtect extends JPlugin	{

    function onAfterInitialise () {

        /**
         * 	Get Tamka Plugin Information
         */
        $plugin 	=& JPluginHelper::getPlugin( 'molajo', 'backup');
        $pluginParams = new JParameter($plugin->params);

        /**
         * 	Look for backupMarker file older than -- (3600*24) is one day * $parameterDays
         */
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $parameterDays = $this->params->get('days', 7);
        $markerFilename = JPATH_ROOT.'/plugins/molajo/backup/files/backupmarker.txt';

        $path = JPATH_ROOT.'/plugins/molajo/backup/files';

        $fileexists = JFolder::files( $path, '(txt)$', false, false );

        if (count($fileexists) > 0 ) {
            if (filemtime($markerFilename) < (time() - ($parameterDays * (3600*24)))) {
                    return;
            }
            if (filemtime($markerFilename) > (time() - ($parameterDays * (3600*24)))) {
                    JFile::delete($markerFilename);
            }
        }

        /**
         * 	Retrieve Configuration Data
         */

        $config         = JFactory::getConfig();
        $sitename       = $config->get('sitename');
        $fromname       = $config->get('fromname');
        $mailfrom       = $config->get('mailfrom');
        $databaseName   = $config->get('db');

        $db		= &JFactory::getDBO();

        /**
         * 	Retrieve Mailing Address and Zip Option
         */
        $emailAddressess = Array();
        $emailParm = $this->params->get('email', '');
        if ($emailParm == '') {
                $emailParm = $mailfrom;
        }
        $emailAddressArray = explode(",", $emailParm);
        $zipBackup = $this->params->get('zip', 1);

        /**
         *  Retrieve Table Names from Database
         */
        $query = 'SHOW TABLES';
        $tables = array();
        $db->setQuery($query);

        $rows = $db->loadResultArray();

        $tables = array();
        $i=0;
        if (count($rows)) {
                foreach ( $rows as $row )	{
                        $tables[$i] = $row;
                        $i++;
                }
        }

        /**
         *  Process each table: Drop, Create, and Inserts
         */
        $databaseBackup = 'USE ' . $databaseName . ';';
        $databaseBackup .= "\n\n";

        foreach($tables as $table)
        {
            $backupData = mysql_query('SELECT * FROM ' . $table);
            $num_fields = mysql_num_fields($backupData);

            //	Retrieve
            $query = 'SHOW COLUMNS FROM ' . $table;
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            $num_fields = count($rows);

            //	Drop statement
            $databaseBackup .= "\n\n";
            $databaseBackup .= 'DROP TABLE ' . $table . ";\n\n";

            //	Create Table
            $row = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
            $databaseBackup.= $row[1].";\n\n";

            for ($i = 0; $i < $num_fields; $i++)	{

                while($row = mysql_fetch_row($backupData))  {

                    $databaseBackup .= "\n";
                    $databaseBackup .= 'INSERT INTO ' . $table . ' VALUES ( ';

                    for ($j=0; $j < $num_fields; $j++) {

                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                        if (isset($row[$j])) { $databaseBackup.= '"'.$row[$j].'"' ; } else { $databaseBackup.= '""'; }
                        if ($j<($num_fields-1)) { $databaseBackup.= ','; }
                    }
                    $databaseBackup.= ");";
                }
            }
        }

        /**
         * 	Write Backup file
         */
        $backupfilenameNoSuffix = $databaseName . '-' . time();

        $backupFilename = JPATH_ROOT.'/plugins/system/backup/'.$backupfilenameNoSuffix . '.sql';
        if (!JFile::write($backupFilename, $databaseBackup)) {
                $response->type = JAUTHENTICATE_STATUS_FAILURE;
                $response->error_message = "Could not create the file " . $file . " Please check permissions.";
                return false;
        }

        /**
         * 	Zip the Backup File and Delete Original
         */
        $executeFilename = JPATH_ROOT.'/plugins/system/backup/7z.exe';
        $zipFilename = JPATH_ROOT.'/plugins/system/backup/'.$backupfilenameNoSuffix . '.zip';
        $zipCommand = $executeFilename . ' a ' . $zipFilename . ' ' . $backupFilename;
        exec($zipCommand);

        $eraseFilenames = 'erase ' . JPATH_ROOT.'/plugins/system/backup/*.sql';
        exec($eraseFilenames);

        /**
         * 	Email Recipients
         */
        $backupCreatedDate = date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000));

        $sitename 		= $mainframe->getCfg('sitename');
        $fromname 		= $mainframe->getCfg('fromname');
        $mailfrom 		= $mainframe->getCfg('mailfrom');
        $databaseName 	= $mainframe->getCfg('db');

        $bcc = Array();
        foreach ( $emailAddressArray as $emailAddress ) {
                $bcc[] = $emailAddress;
        }
        $emailSubject	= '[' . $sitename . JText::_( ' Database Backup ') . $backupCreatedDate . ']';
        $emailMessage = JText::_( 'ATTACHED' );
        $mode = 1;
        $attachment = $zipFilename;

        $return = JUtility::sendMail($mailfrom, $fromname, '', $emailSubject, $emailMessage, $mode, '', $bcc, $attachment, '', '');
        if ($return !== true) {
                return;
        }

        /**
         * 	Delete Zip after email
         */
        $eraseFilenames = 'erase ' . $zipFilename;
        exec($eraseFilenames);

        JFile::write($markerFilename, 'backup');

        return;

    }
}
?>