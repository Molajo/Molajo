<?php
/**
 * @package   Molajo
 * @subpackage  Model
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * Sites
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class SitesModel extends DisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table_name = '#__sites';
        $this->primary_key = 'id';

        return parent::__construct($id);
    }
}
