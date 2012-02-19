<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * AdminToolbar
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoAdminToolbarModuleModel extends MolajoDisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);

        return parent::__construct($id);
    }

    /**
     * getData
     *
     * @return    array
     * @since    1.0
     */
    public function getData()
    {
        /** Component Buttons */
        $buttons =
            Molajo::Request()->
            parameters->
            get('toolbar_buttons');

            $buttonArray = explode(',', $buttons);

        /** User Permissions */
        $permissions =
            Services::Access()->
                authoriseTaskList(
                $buttonArray,
                Molajo::Request()
                 ->parameters
                 ->get('display_extension_asset_id')
        );

        $this->data = array();
        foreach ($buttonArray as $buttonname) {
            if ($permissions[$buttonname] === true) {
                $displayButtons = new stdClass();

                $displayButtons->name = $buttonname;
                $displayButtons->option = Molajo::Request()->
                            parameters->
                            get('display_extension_option');
                $displayButtons->task = $buttonname;

                $this->data[] = $displayButtons;
            }
        }

        return $this->data;
    }
}
