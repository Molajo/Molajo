<?php
/**
 * @package     Molajo
 * @subpackage  Content Type
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * modelContentType
 *
 * Extend Component Content with Content Types, which are XML files containing one or more Custom Fields
 *
 * @package	Content
 * @subpackage	Extend
 * @version	1.6
 */
class modelContentType
{
    /**
     * getFolderFilenames
     *
     * @param string $path
     *
     * @return object
     */
    public function getFolderFilenames ($path=null)
    {
        /** default path **/
        if ($path == null) {
            $path = MOLAJO_EXTEND_ROOT.'/contenttypes';
        }

        /** retrieve file names for folder **/
        if (JFolder::exists($path)) {
           return JFolder::files($path, '.xml');
        } else {
            return false;
        }
    }
}