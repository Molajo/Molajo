<?php
/**
 * Contentlist Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Contentlist;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Contentlist Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class ContentlistPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Retrieves Contentlist of data, according to runtime_data
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterReadall()
    {
        if (isset($this->runtime_data->render->token)
            && $this->runtime_data->render->token->type == 'template'
            && strtolower($this->runtime_data->render->token->name) == 'contentlist'
        ) {
        } else {
            return $this;
        }

        /** Retrieve Data */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry(
            $this->get('model_type', '', 'runtime_data'),
            $this->get('model_name', '', 'runtime_data')
        );
        $controller->setDataobject();
        $controller->connectDatabase();

        $controller->set('get_customfields', 2);
        $controller->set('use_special_joins', 1);
        $controller->set('process_events', 1);

        $prefix = $controller->get('primary_prefix', 'a');

        $criteria_status = $this->get('criteria_status', '', 'runtime_data');
        $criteria_status = '1,2';
        if ($criteria_status == '') {
        } else {
            $controller->model->query->where(
                $controller->model->database->qn($prefix . '.' . 'status')
                . ' IN (' . $criteria_status . ')'
            );
        }

        $this->row = $controller->getData('list');

        return $this;

        $ordering = $this->runtime_data->criteria_ordering;
        if ($ordering == 'Popular') {
            $ordering  = 'a.ordering'; //@todo hits
            $direction = 'ASC';
        } elseif ($ordering == 'Ordering') {
            $ordering  = 'a.ordering';
            $direction = 'ASC';
        } elseif ($ordering == 'Stickied') {
            $ordering  = 'a.stickied';
            $direction = 'ASC';
        } elseif ($ordering == 'Featured') {
            $ordering  = 'a.featured';
            $direction = 'ASC';
        } else {
            $ordering  = 'a.start_publishing_datetime';
            $direction = 'DESC';
        }
        $controller->model->query->order($controller->model->database->qn($ordering) . ' ' . $direction);

        $controller->set('model_offset', 0);
        $count = $this->runtime_data->criteria_count;
        if ((int)$count == 0) {
            $count = 5;
        }
        $controller->set('model_count', $count);

        return $this;
    }
}
