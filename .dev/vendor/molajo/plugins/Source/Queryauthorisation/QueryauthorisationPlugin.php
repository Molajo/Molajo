<?php
/**
 * Query Authorisation Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins\Queryauthorisation;

use Molajo\Plugins\ReadEventPlugin;
use CommonApi\Event\ReadInterface;

/**
 * Query Authorisation Plugin
 *
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class QueryauthorisationPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Verify if Query Authorisation should be added to query
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRead()
    {
        if ($this->processQueryauthorisationPlugin() === false) {
            return $this;
        }

        return $this->setQueryAuthorisation();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processQueryauthorisationPlugin()
    {
        if (isset($this->model_registry['check_view_level_access'])) {
            $check = $this->model_registry['check_view_level_access'];
        } else {
            $check = 0;
        }

        if ((int)$check === 0) {
            return false;
        }

        if (count($this->runtime_data->user->view_groups) > 0) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Append Criteria for Authorisation to Query Object
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setQueryAuthorisation()
    {
        $prefix         = $this->model_registry['primary_prefix'];
        $id             = $prefix . '.' . $this->model_registry['primary_key'];
        $application_id = $this->runtime_data->application->id;
        $vg             = implode(',', array_unique($this->runtime_data->user->view_groups));

        $this->query->from('#__catalog', 'authorisation');

        $this->query->where('column', 'authorisation.source_id', '=', 'column', $id);
        $this->query->where('column', 'authorisation.catalog_type_id', '=', 'column', $prefix . '.catalog_type_id');
        $this->query->where('column', 'authorisation.application_id', '=', 'integer', (int)$application_id);
        $this->query->where('column', 'authorisation.view_group_id', 'IN', 'integer', $vg);
        $this->query->where('column', 'authorisation.redirect_to_id', '=', 'integer', '0');

        return $this;
    }
}
