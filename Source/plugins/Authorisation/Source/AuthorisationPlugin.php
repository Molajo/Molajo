<?php
/**
 * Authorisationisation Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Authorisationisation;

use CommonApi\Event\UserEventInterface;
use Molajo\Plugins\UserEvent;

/**
 * Authorisationisation Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class AuthorisationisationPlugin extends UserEvent implements UserEventInterface
{
    /**
     * Actions used to establish authorisation
     *
     *  0 => 'none',
     *  1 => 'login',
     *  2 => 'create',
     *  3 => 'read',
     *  4 => 'update',
     *  5 => 'publish',
     *  6 => 'delete',
     *  7 => 'administer'
     *
     * @var    array
     * @since  1.0
     */
    protected $actions = array();

    /**
     * Actions used to establish authorisation
     *
     * 'none'       => 0,
     * 'login'      => 1,
     * 'create'     => 2,
     * 'read'       => 3,
     * 'update'     => 4,
     * 'publish'    => 5,
     * 'delete'     => 6,
     * 'administer' => 7
     *
     * @var    array
     * @since  1.0
     */
    protected $action_ids = array();

    /**
     * Disable Filter for Groups
     *
     * @var    array
     * @since  1.0
     */
    protected $disable_filter_for_groups = array(5, 6);

    /**
     * Task to Action Id
     *
     * @var    array
     * @since  1.0
     */
    protected $task_to_action_id = array();

    /**
     * Task to Action
     *
     * @var    array
     * @since  1.0
     */
    protected $task_to_action = array();

    /**
     * Before Authorisationisation processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeAuthorisationisation()
    {
        /** Step 3. Authorisationised to Access Site */
        $options    = array(
            'action_id'  => null,
            'catalog_id' => null,
            'type'       => 'Site'
        );
        $authorised = $this->isUserAuthorisationised($options);
        if ($authorised === false) {
//todo: finish authorisation
            // 301 redirect
        }

        /** Step 3. Authorisationised to Access Application */
        $options = array(
            'action_id'  => null,
            'catalog_id' => $this->runtime_data->application->catalog_id,
            'type'       => 'Application'
        );

        $authorised = $this->isUserAuthorisationised($options);
        if ($authorised === false) {
            //todo: finish authorisation
            // 301 redirect
        }

        /** Step 4. Authorisationised for Catalog */
        $options = array(
            'action'     => $this->runtime_data->route->action,
            'catalog_id' => $this->runtime_data->route->catalog_id,
            'type'       => 'Catalog'
        );

        $authorised = $this->isUserAuthorisationised($options);
        if ($authorised === false) {
            // 301 redirect
        }

        /** Step 5. Validate if site is set to offline mode that user has access */
        $options    = array(
            'type' => 'OfflineAccess'
        );
        $authorised = $this->isUserAuthorisationised($options);
        if ($authorised === false) {
            // 301 redirect
        }

        /** Step 3. Thresholds: Lockout */
        // IP address
        // Hits
        // Time of day
        // Visits
        // Login Attempts
        // Upload Limits
        // CSFR
        // Captcha Failure

        return $this;
    }

    /**
     * Verify User Authorisationisation to take Action on Resource
     *
     * @param   array $options
     *
     * @return  bool
     * @since   1.0.0
     */
    public function isUserAuthorisationised(array $options = array())
    {
        $action_id = (int)$this->initialiseKey('action_id', $options);

        if (isset($options['action'])) {
            $action    = $options['action'];
            $action_id = (int)$this->action_ids[$action];
        }

        $task        = (string)$this->initialiseKey('task', $options);
        $group_id    = (int)$this->initialiseKey('group_id', $options);
        $resource_id = (int)$this->initialiseKey('resource_id', $options);
        $type        = (string)$this->initialiseKey('type', $options);

        return $this->isUserAuthorisationisedMethod($type, $action_id, $group_id, $task, $resource_id);
    }

    /**
     * Initialise Value
     *
     * @param   string|integer $key
     * @param   array          $options
     *
     * @return  string
     * @since   1.0.0
     */
    protected function initialiseKey($key, array $options = array())
    {
        if (isset($options[$key])) {
            $value = $options[$key];
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * Is User Authorisationised Method
     *
     * @param   string  $type
     * @param   integer $action_id
     * @param   integer $group_id
     * @param   string  $task
     * @param   integer $resource_id
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedMethod($type, $action_id, $group_id, $task, $resource_id)
    {
        if ($this->executeIsUserAuthorisationisedMethod($type) === true) {
            $method = 'isUserAuthorisationised' . ucfirst(strtolower($type));
            return $this->$method();
        }

        if ($action_id == 3) {
            return $this->isUserAuthorisationisedViewAccess($group_id);
        }

        if ($task === null) {
            return $this->isUserAuthorisationisedAction($action_id, $resource_id);
        }

        return $this->isUserAuthorisationisedTask($task, $resource_id);
    }

    /**
     * Execute isUserAuthorisationisedMethod
     *
     * @param   string $type
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function executeIsUserAuthorisationisedMethod($type)
    {
        $method = 'isUserAuthorisationised' . ucfirst(strtolower($type));
        if (method_exists($this, $method)) {
            return true;
        }

        return false;
    }

    /**
     * Is User Authorisationised for this Site
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedSite()
    {
        if (in_array($this->site_id, $this->runtime_data->user->sites)) {
            return true;
        }

        return false;
    }

    /**
     * Is User Authorisationised for this Application
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedApplication()
    {
        if (in_array($this->runtime_data->application->id, $this->runtime_data->user->applications)) {
            return true;
        }

        return false;
    }

    /**
     * Check if Application has been set to "offline" and, if so, verify if user has offline access
     *
     * @return  boolean
     * @since   1.0.0
     */
    public function isUserAuthorisationisedOfflineAccess()
    {
        if ((int)$this->runtime_data->application->parameters->offline_switch == 1) {
            if ($this->runtime_data->user->authorised_for_offline_access == 1) {
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Is User Authorisationised to View this Catalog ID which has this View Group ID
     *
     * @param   integer $group_id
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedViewAccess($group_id)
    {
        if (in_array($group_id, $this->runtime_data->user->view_groups)) {
            return true;
        }

        return false;
    }

    /**
     * After Initialise Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterStart()
    {
        return $this;
    }

    /**
     * Before Route Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRoute()
    {
        return $this;
    }

    /**
     * After Route Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRoute()
    {
        return $this;
    }

    /**
     * Before Resource Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeDispatcher()
    {
        return $this;
    }

    /**
     * After Dispatcher Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterDispatcher()
    {
        return $this;
    }

    /**
     * Before Execute Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeExecute()
    {
        return $this;
    }

    /**
     * After Execute Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterExecute()
    {
        return $this;
    }

    /**
     * Before Response Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeResponse()
    {
        return $this;
    }

    /**
     * After Response Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterResponse()
    {
        return $this;
    }

    /**
     * After Authorisationisation processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterAuthorisationisation()
    {
        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        return false;
    }

    /**
     * Is User Authorisationised to View this Catalog ID
     *
     * @param   string  $task
     * @param   integer $resource_id
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedTask($task, $resource_id)
    {
        $action    = $this->task_to_action[$task];
        $action_id = $this->task_to_action_id[$action];

        if ($action_id == 3) {
            return $this->isUserAuthorisationisedViewAction($resource_id);
        }

        return $this->isUserAuthorisationisedAction($action_id, $resource_id);
    }

    /**
     * Is User Authorisationised to View this Catalog ID
     *
     * @param   integer $resource_id
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedViewAction($resource_id)
    {
        $parameter1 = $resource_id;
        $method     = 'isUserAuthorisationisedViewActionQuery';
        $message    = 'Language Database Adapter isUserAuthorisationisedViewAction Query Failed: ';

        return $this->isUserAuthorisationisedQuery($parameter1, null, $method, $message);
    }

    /**
     * Is User Authorisationised to View this Catalog ID
     *
     * @param   integer $action_id
     * @param   integer $resource_id
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedAction($action_id, $resource_id)
    {
        $parameter1 = $action_id;
        $parameter2 = $resource_id;
        $method     = 'isUserAuthorisationisedActionQuery';
        $message    = 'Language Database Adapter isUserAuthorisationisedViewAction Query Failed: ';

        return $this->isUserAuthorisationisedQuery($parameter1, $parameter2, $method, $message);
    }

    /**
     * Is User Authorisationised to View this Catalog ID
     *
     * @param   string      $parameter1
     * @param   null|string $parameter2
     * @param   string      $method
     * @param   string      $message
     *
     * @return  boolean
     * @since   1.0.0
     *
     */
    protected function isUserAuthorisationisedQuery($parameter1, $parameter2 = null, $method = '', $message = '')
    {
        try {
            $this->query->clearQuery();

            $this->$method($parameter1, $parameter2);

            return $this->loadResult();

        } catch (Exception $e) {
            throw new RuntimeException($message . $e->getMessage());
        }
    }

    /**
     * Is User Authorisationised View Action Query
     *
     * @param   integer $resource_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedViewActionQuery($resource_id)
    {
        $this->query->select('COUNT(*)');
        $this->query->from('#__view_group_permissions');
        $this->query->where('column', 'catalog_id', '=', 'integer', (int)$resource_id);
        $this->query->where('column', 'view_group_id', 'IN', 'integer',
            implode(',', $this->runtime_data->user->view_groups));

        return $this;
    }

    /**
     * Is User Authorisationised Action Query
     *
     * @param   integer $action_id
     * @param   integer $resource_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function isUserAuthorisationisedActionQuery($action_id, $resource_id)
    {
        $this->query->select('COUNT(*)', null, null, 'special');
        $this->query->from('#__group_permissions');
        $this->query->where('column', 'catalog_id', '=', 'integer', (int)$resource_id);
        $this->query->where('column', 'action_id', '=', 'integer', (int)$action_id);
        $this->query->where('column', 'group_id', 'IN', 'integer', implode(',', $this->runtime_data->user->groups));

        return $this;
    }

    /**
     * Load Result
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function loadResult()
    {
        $count = $this->query->loadResult($this->query->getSQL());

        if ($count > 0) {
            return true;
        }

        return false;
    }
}
