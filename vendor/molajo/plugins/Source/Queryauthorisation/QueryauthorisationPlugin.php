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
     * Append Criteria for Authorisation to Query Object
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRead()
    {
        if (isset($this->model_registry['check_view_level_access'])) {
            $check = $this->model_registry['check_view_level_access'];
        } else {
            $check = 1;
        }

        if ((int)$check === 0) {
            return $this;
        }

        if (is_array($this->permissions->view_groups)) {
        } else {
            return $this->query;
        }

        if (isset($model_registry['select'])
            && $model_registry['select'] === true
        ) {
            $this->query->select(
                'authorisation'
                . '.'
                . 'view_group_id'
            );

            $this->query->select(
                'authorisation'
                . '.'
                . 'id'
                . ' as '
                . 'catalog_id'
            );
        }

        $this->query->from(
            '#__catalog'
            . ' as '
            . 'authorisation'
        );

        $this->query->where(
            'authorisation'
            . '.'
            . 'source_id'
            . ' = '
            . $model_registry['primary_prefix']
            . '.'
            . $model_registry['primary_key']
        );

        $this->query->where(
            'authorisation'
            . '.'
            . 'catalog_type_id'
            . ' = '
            . $model_registry['primary_prefix']
            . '.'
            . 'catalog_type_id'
        );

        $this->query->where(
            'authorisation'
            . '.'
            . 'application_id'
            . ' = '
            . $this->application_id
        );

        $vg = implode(',', array_unique($this->permissions->view_groups));

        $this->query->where(
            'authorisation'
            . '.'
            . 'view_group_id'
            . ' IN '
            . '(' . $vg . ')'
        );

        $this->query->where(
            'authorisation'
            . '.'
            . 'redirect_to_id'
            . ' = '
            . '0'
        );

        return $this;
    }
}
