<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Asset Categories
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoAssetCategoriesModel extends MolajoModel
{
    /**
     * @param database a database connector object
     */
    public function __construct($db)
    {
        parent::__construct('#__asset_categories', 'id', $db);
    }

}
