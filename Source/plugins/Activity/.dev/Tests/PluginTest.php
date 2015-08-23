<?php
/**
 * Plugin Test
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller\Test;

use stdClass;

/**
 * Plugin Controller Test
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Instance
     *
     * @var    array
     * @since  1.0.0
     */
    protected $instance;

    /**
     * Database
     *
     * @var    object
     * @since  1.0.0
     */
    protected $database;

    /**
     * Query
     *
     * @var    object
     * @since  1.0.0
     */
    protected $query;

    /**
     * Fieldhandler
     *
     * @var    object
     * @since  1.0.0
     */
    protected $fieldhandler;

    /**
     * Request Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $request_path;

    /**
     * Plugins
     *
     * @var    array
     * @since  1.0.0
     */
    protected $applications;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0.0
     */
    protected $model_registry;

    /**
     * Setup testing
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setUp()
    {

    }

    /**
     * Instantiate the class to test
     *
     * @return  array
     * @since   1.0.0
     */
    public function instantiateClass()
    {
        $this->instance = new MockPlugin(
            $this->database,
            $this->query,
            $this->fieldhandler,
            $this->request_path,
            $this->applications,
            $this->model_registry
        );
    }

    /**
     *
     * @return  $this
     * @since   1.0.0
     */
    public function testSetPluginTrailSlashSite()
    {
        $this->assertEquals(1, 1);

        return $this;
    }
}
