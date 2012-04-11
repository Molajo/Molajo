<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;
use Molajo\Application\MVC\Model\LoadModel;

defined('MOLAJO') or die;

/**
 * User Class
 *
 * @package   Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class UserService extends BaseService
{
    /**
     * Instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instances = array();

    /**
     * getInstance
     *
     * @param   string $identifier  Requested User (id or username or 0 for guest)
     *
     * @return  object  User
     * @since   1.0
     */
    public static function getInstance($id = 0)
    {
		$id = 42;
        if (empty(self::$instances[$id])) {
            $user = new UserService($id);
            self::$instances[$id] = $user;
        }
        return self::$instances[$id];
    }

    /**
     * __construct
     *
     * @param   integer  $identifier
     *
     * @return  object
     * @since   1.0
     */
    protected function __construct($id = 0)
    {
        $this->id = (int)$id;
        return $this->load();
    }

    /**
     * load
     *
     * Retrieve User Information (both authenticated and guest)
     *
     * @return  User
     * @since   1.0
     */
    protected function load()
    {
        $m = new LoadModel ('Users', $this->id);
        $results = $m->load();

        while (list($name, $value) = each($results)) {

            if ($name == 'parameters'
                || $name == 'custom_fields'
                || $name == 'metadata'
            ) {
            } else {
                Services::Registry()->set('User\\' . $name, $value);
            }
        }

        $v = simplexml_load_file(
            APPLICATIONS_MVC
                . '/Model/Table/'
                . substr($m->table_name, 3, 99)
                . '.xml'
        );

        $this->registry('UserCustomFields\\', $results, 'custom_fields', 'custom_field', $v);
        $this->registry('UserMetadata\\', $results, 'metadata', 'meta', $v);
        $this->registry('UserParameters\\', $results, 'parameters', 'parameter', $v);

/**
if ((int)$this->id == 0) {
$this->query_results['name'] = '';
$this->query_results['applications'] = array();
$this->query_results['groups'] = array(SYSTEM_GROUP_PUBLIC, SYSTEM_GROUP_GUEST);
$this->query_results['view_groups'] = array(SYSTEM_GROUP_PUBLIC, SYSTEM_GROUP_GUEST);
$this->query_results['public'] = 1;
$this->query_results['guest'] = 1;
$this->query_results['registered'] = 0;
$this->query_results['administrator'] = 0;
 */

        return $this;
    }

    /**
     * registry
     *
     * @param $namespace
     * @param $source
     * @param $field_group
     * @param $field_name
     * @param $v
     */
    protected function registry($namespace, $source, $field_group, $field_name, $v)
    {
        $registry = Services::Registry()->initialise();
        $registry->loadJSON($source[$field_group], array());

        if (isset($v->$field_group->$field_name)) {
            foreach ($v->$field_group->$field_name as $cf) {

                $name = (string)$cf['name'];
                $dataType = (string)$cf['filter'];
                $null = (string)$cf['null'];
                $default = (string)$cf['default'];
                $values = (string)$cf['values'];

                if ($default == '') {
                    $val = $registry->get($name, null);
                } else {
                    $val = $registry->get($name, $default);
                }

                Services::Registry()->set($namespace . $name, $val);
            }
        }
    }
}
