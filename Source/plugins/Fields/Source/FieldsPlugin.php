<?php
/**
 * Fields Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fields;

use CommonApi\Event\SystemEventInterface;
use Molajo\Plugins\SystemEvent;

/**
 * Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class FieldsPlugin extends SystemEvent implements SystemEventInterface
{
    /**
     * Before Execute
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeExecute()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->buildLists();

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
        if (strtolower($this->runtime_data->route->page_type) === 'configuration') {
            return true;
        }

        return false;
    }

    /**
     * Build Common Configuration lists
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function buildLists()
    {
        $this->setList('Groups', 'datalist_groups');
        $this->setList('Themes', 'datalist_themes');
        $this->setList('Pageviews', 'datalist_pageviews');
        $this->setList('Templates', 'datalist_templates');
        $this->setList('Wraps', 'datalist_wraps');

        return false;
    }

    /**
     * Set Specific List
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setList($source, $destination)
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        $this->plugin_data->$destination = $this->getDatalist($source, $options);

        return false;
    }
}
