<?php
/**
 * Cssclassandids Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Cssclassandids;

use Molajo\Plugins\ReadEventPlugin;
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

        if (isset($this->row->runtime_data)) {
            return $this;
        }

        /** class */
        $this->row->css_class = trim($this->row->runtime_data->css_class)
            . ' ' . trim($this->row->runtime_data->template_view_css_class)
            . ' ' . trim($this->row->runtime_data->current);

        if (trim($this->row->css_class) == '') {
        } else {
            $this->row->css_class = htmlspecialchars(
                trim($this->row->css_class),
                ENT_NOQUOTES,
                'UTF-8'
            );
        }

        /** id */
        $this->row->css_id = ' ' . trim($this->row->runtime_data->css_id)
            . ' ' . trim($this->row->runtime_data->template_view_css_id)
            . ' ' . trim($this->row->runtime_data->current);

        if (trim($this->row->css_id) == '') {
        } else {
            $this->row->css_id = htmlspecialchars(trim($this->row->css_id), ENT_NOQUOTES, 'UTF-8');
        }

        return $this;
    }
}
