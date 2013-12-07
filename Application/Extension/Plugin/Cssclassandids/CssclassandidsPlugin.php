<?php
/**
 * Cssclassandids Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Cssclassandids;

use Molajo\Plugin\ReadEventPlugin;
use CommonApi\Event\ReadInterface;

/**
 * Cssclassandids Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class CssclassandidsPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Add CSS class and ID to each row
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        return $this;

        if (isset($this->query_results->runtime_data)) {
            return $this;
        }

        /** class */
        $this->query_results->css_class = trim($this->query_results->runtime_data->css_class)
            . ' ' . trim($this->query_results->runtime_data->template_view_css_class)
            . ' ' . trim($this->query_results->runtime_data->current);

        if (trim($this->query_results->css_class) == '') {
        } else {
            $this->query_results->css_class = htmlspecialchars(
                trim($this->query_results->css_class),
                ENT_NOQUOTES,
                'UTF-8'
            );
        }

        /** id */
        $this->query_results->css_id = ' ' . trim($this->query_results->runtime_data->css_id)
            . ' ' . trim($this->query_results->runtime_data->template_view_css_id)
            . ' ' . trim($this->query_results->runtime_data->current);

        if (trim($this->query_results->css_id) == '') {
        } else {
            $this->query_results->css_id = htmlspecialchars(trim($this->query_results->css_id), ENT_NOQUOTES, 'UTF-8');
        }

        return $this;
    }
}