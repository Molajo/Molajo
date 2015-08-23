<?php
/**
 * Base for Extensions Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Extensions;

use Molajo\Plugins\SystemEvent;

/**
 * Base for Extensions Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Base extends SystemEvent
{
    /**
     * Extension Catalog Type Ids
     *
     * @var    array
     * @since  1.0.0
     */
    protected $extension_catalog_type_ids
        = array(
            5000  => 'Plugins',
            7000  => 'Themes',
            8000  => 'Pageviews',
            9000  => 'Templateviews',
            10000 => 'Wrapviews',
            12000 => 'Resources'
        );

    /**
     * Extension folders
     *
     * @var    array
     * @since  1.0.0
     */
    protected $extension_folders
        = array(
            5000  => array('/vendor/molajo/plugins/Source'),
            7000  => array(
                '/vendor/molajo/themes/Source/Foundation5',
                '/vendor/molajo/themes/Source/System'
            ),
            8000  => array(
                '/vendor/molajo/themes/Source/Foundation5/Views/Pages',
                '/vendor/molajo/themes/Source/System/Views/Pages'
            ),
            9000  => array(
                '/vendor/molajo/themes/Source/Foundation5/Views/Templates',
                '/vendor/molajo/themes/Source/System/Views/Templates'
            ),
            10000 => array(
                '/vendor/molajo/themes/Source/Foundation5/Views/Wraps',
                '/vendor/molajo/themes/Source/System/Views/Wraps'
            ),
            12000 => array('/Source/Resources')
        );

    /**
     * Installed Themes
     *
     * @var    array
     * @since  1.0.0
     */
    protected $installed_themes = array('Foundation5', 'System');

    /**
     * Current Theme
     *
     * @var    string
     * @since  1.0.0
     */
    protected $current_theme;

    /**
     * Parent Id
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $parent_id = 0;

    /**
     * Set Namespace
     *
     * @param   integer $catalog_type_id
     * @param   string  $extension_name
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setNamespace($catalog_type_id, $extension_name)
    {
        $theme = 'Molajo/Themes/' . $this->current_theme;

        if ($catalog_type_id === 5000) {
            $namespace = 'Molajo/Plugins/' . ucfirst(strtolower($extension_name));
        } elseif ($catalog_type_id === 7000) {
            $namespace = 'Molajo/Themes/' . ucfirst(strtolower($extension_name));
        } elseif ($catalog_type_id === 8000) {
            $namespace = $theme . '/Views/Pages/' . ucfirst(strtolower($extension_name));
        } elseif ($catalog_type_id === 9000) {
            $namespace = $theme . '/Views/Templates/' . ucfirst(strtolower($extension_name));
        } elseif ($catalog_type_id === 10000) {
            $namespace = $theme . '/Views/Wraps/' . ucfirst(strtolower($extension_name));
        } else {
            $namespace = 'Molajo/Resources/' . ucfirst(strtolower($extension_name));
        }

        return $namespace;
    }
}
