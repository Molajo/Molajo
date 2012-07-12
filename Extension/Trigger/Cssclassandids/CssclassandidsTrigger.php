<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Cssclassandids;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CssclassandidsTrigger extends ContentTrigger
{

    /**
     * Before the Query results are injected into the View
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeViewRender()
    {
        $count = count($this->data);

        if ((int) $count == 0
            || $this->data == false
            || $this->data == null
        ) {
            return true;
        }

        /** Add CSS info to each row */
        $first = true;
        foreach ($this->data as $item) {

            if ($first) {

                /** @var $css_class */
                $class = '';

                if (is_object($item)) {
                    if (isset($item->css_class)) {
                        $class = $item->css_class;
                    }
                } else {
                    if (isset($item['css_class'])) {
                        $class = $item['css_class'];
                    }
                }

                $class .= ' ' . $this->get('view_css_class', '');

                if (trim($class) == '') {
                    $class = '';
                } else {
                    $class = ' class="' . htmlspecialchars(trim($class), ENT_NOQUOTES, 'UTF-8') . '"';
                }

                /** @var $css_id */
                $id = '';

                if (is_object($item)) {
                    if (isset($item->css_id)) {
                        $class = $item->css_id;
                    }
                } else {
                    if (isset($item['css_id'])) {
                        $class = $item['css_id'];
                    }
                }

                $id .= ' ' . $this->get('view_css_id', '');
                if (trim($id) == '') {
                    $id = trim($id);
                } else {
                    $id = ' id="' . htmlspecialchars(trim($id), ENT_NOQUOTES, 'UTF-8') . '"';
                }
            }

            /** Store CSS class and id in each row */
            if (is_object($item)) {
                $item->css_class = $class;
            } else {
                $item['css_class'] = $class;
            }

            if (is_object($item)) {
                $item->css_id = $id;
            } else {
                $item['css_id'] = $id;
            }
        }

        return true;
    }
}
