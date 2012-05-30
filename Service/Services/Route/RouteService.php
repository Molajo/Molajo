<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Route;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * Route Service
 *
 * @package    Molajo
 * @subpackage Route
 * @since      1.0
 */
Class RouteService
{
    /**
     * $instance
     *
     * @var     object
     * @since   1.0
     */
    protected static $instance = null;

    /**
     * @return Object
     *
     * @since   1.0
     */
    public static function getInstance()
    {
        if (self::$instance) {
        } else {
            self::$instance = new RouteService();
        }

        return self::$instance;
    }

    /**
     * Using the PAGE_REQUEST constant:
     *
     *  - retrieve the catalog record
     *  - set registry values needed to fulfill the page request
     *
     * @return mixed
     *
     * @since 1.0
     */
    public function process()
    {
        /** Route Registry */
        Services::Registry()->createRegistry('Parameters');
        Services::Registry()->createRegistry('Metadata');

        Services::Registry()->set('Parameters', 'status_found', '');
        Services::Registry()->set('Parameters', 'status_authorised', '');
        Services::Registry()->set('Parameters', 'redirect_to_id', 0);

        /** Overrides */
        if ((int) Services::Registry()->get('Override', 'catalog_id', false) == true) {
            Services::Registry()->set('Parameters', 'request_catalog_id', 0);

        } else {
            Services::Registry()->set('Parameters', 'request_catalog_id',
                (int) Services::Registry()->get('Override', 'catalog_id'));
        }

        if (Services::Registry()->get('Override', 'url_request', false) == false) {
            $path = PAGE_REQUEST;

        } else {
            $path = Services::Registry()->get('Override', 'url_request');
        }

        /** Check for duplicate content URL for Home (and redirect, if found) */
        $continue = $this->checkHome($path);

        if ($continue == false) {
            Services::Debug()->set('Route checkHome() Redirect to Real Home');

            return false;}

        /** See if Application is in Offline Mode */
        if (Services::Registry()->get('Configuration', 'offline', 0) == 1) {
            Services::Error()->set(503);
            Services::Debug()->set('Application::Route() Direct to Offline Mode');

            return true;
        }

        /** Remove Parameters from path and save for later use */
        $continue = $this->getNonRouteParameters();

        if ($continue == false) {
            Services::Debug()->set('Route getNonRouteParameters() Failed');

            return false;
        }

        /**  Get Route Information: Catalog  */
        $continue = Helpers::Catalog()->getRoute();

        /** 404 */
        if (Services::Registry()->get('Parameters', 'status_found') === false) {
            echo 404;
            Services::Error()->set(404);
            Services::Debug()->set('Application::Route() 404');

            return false;
        }

        /** URL Change Redirect from Catalog */
        if ((int) Services::Registry()->get('Parameters', 'redirect_to_id', 0) == 0) {
        } else {
            Services::Response()->redirect(
                Helper::Catalog()->getURL(
                    Services::Registry()->get('Parameters', 'redirect_to_id', 0)
                ), 301
            );
            Services::Debug()->set('Application::Route() Redirect');

            return false;
        }

        /** Redirect to Logon */
        if (Services::Registry()->get('Configuration', 'application_logon_requirement', 0) > 0
            && Services::Registry()->get('User', 'guest', true) === true
            && Services::Registry()->get('Parameters', 'request_catalog_id')
                <> Services::Registry()->get('Configuration', 'application_logon_requirement', 0)
        ) {
            Services::Response()->redirect(
                Services::Registry()->get('Configuration', 'application_logon_requirement', 0)
                , 303
            );
            Services::Debug()->set('Application::Route() Redirect to Logon');

            return false;
        }

        $this->getRouteParameters();

        /**   Return to Application Object */

        return $this;
    }

    /**
     * Determine if URL is duplicate content for home (and issue redirect, if necessary)
     *
     * @param string $path Stripped of Host, Folder, and Application
     *                         ex. index.php?option=login or access/groups
     *
     * @return boolean
     * @since  1.0
     */
    protected function checkHome($path = '')
    {
        if (strlen($path) == 0) {
            return true;

        } else {

            /** duplicate content: URLs without the .html */
            if ((int) Services::Registry()->get('Configuration', 'url_sef_suffix', 1) == 1
                && substr($path, -11) == '/index.html') {
                $path = substr($path, 0, (strlen($path) - 11));
            }

            if ((int) Services::Registry()->get('Configuration', 'url_sef_suffix', 1) == 1
                && substr($path, -5) == '.html') {
                $path = substr($path, 0, (strlen($path) - 5));
            }
        }

        /** populate value used in query  */
        Services::Registry()->set('Parameters', 'request_url_query', $path);

        /** home: duplicate content - redirect */
        if (Services::Registry()->get('Parameters', 'request_url_query', '') == 'index.php'
            || Services::Registry()->get('Parameters', 'request_url_query', '') == 'index.php/'
            || Services::Registry()->get('Parameters', 'request_url_query', '') == 'index.php?'
            || Services::Registry()->get('Parameters', 'request_url_query', '') == '/index.php/'
        ) {
            Services::Redirect()->set('', 301);

            return false;
        }

        /** Home */
        if (Services::Registry()->get('Parameters', 'request_url_query', '') == ''
            && (int) Services::Registry()->get('Parameters', 'request_catalog_id', 0) == 0) {

            Services::Registry()->set('Parameters', 'request_catalog_id',
                Services::Registry()->get('Configuration', 'application_home_catalog_id', 0));
            Services::Registry()->set('Parameters', 'catalog_home', true);
        }

        return true;
    }

    /**
     * Retrieve non route parameter values and remove from path
     *
     * Note: $path has already been stripped of Host, Folder, and Application
     *
     *   ex. index.php?option=article&tag=XYZ&prev=6
     *      ex. access/groups/tag/XYZ/prev/6
     *
     * todo: remove tag/value if SEF URL
     *
     * @since 1.0
     */
    protected function getNonRouteParameters()
    {
        $action = 'display';

        $path = Services::Registry()->get('Parameters', 'request_url_query');

        if ($path == '') {
            Services::Registry()->set('Parameters', 'request_non_route_parameters', array());
            Services::Registry()->set('Parameters', 'request_action', 'display');
            Services::Registry()->set('Parameters', 'request_catalog_id',
                Services::Registry()->get('Configuration', 'application_home_catalog_id', 0));

            return true;
        }

        /** Retrieve ID */
        $value = (int) Services::Request()->get('request')->get('id');
        Services::Registry()->set('Parameters', 'request_catalog_id', $value);

        /** save non-routable parameter pairs in array */
        $use = array();

        /** XML with system defined nonroutable pairs */
        $list = Services::Configuration()->getFile('nonroutable', 'Application');

        foreach ($list->parameter as $item) {

            $key = (string) $item['name'];

            $filter = (string) $item['filter'];
            if ($filter === null) {
                $filter = 'char';
            }

            $value = Services::Request()->get('request')->get($key);

            if ($value === null) {
            } else {

                /** Action */
                if ($key == 'action') {
                    $action = $value;
                }

                /** remove non-route parameters - as it is - from the route path */
                $remove = $key . '=' . $value;

                $path = substr($path, 0, strpos($path, $remove))
                    . substr($path, strpos($path, $remove) + 1 + strlen($remove), 999);

                /** filter input */
                $value = $this->filterInput($key, $value, $filter, 1, null);

                if ($value === false) {
                } else {
                    $use[$key] = $value;
                }
            }
        }

        /** Remove trailing ? or & */
        if (trim($path) == '') {
        } else {
            if (strrpos($path, '&') == (strlen($path) - 1)
                || strrpos($path, '?') == (strlen($path) - 1)
            ) {
                $path = substr($path, 0, strlen($path) - 1);
            }
        }

        /** Update Path and store Non-routable parameters for Extension Use */
        Services::Registry()->set('Parameters', 'request_url_query', $path);
        Services::Registry()->set('Parameters', 'request_non_route_parameters', $use);
        Services::Registry()->set('Parameters', 'request_action', $action);

        /** add Edit and Add later

        2. add /add and /edit
        3. deal with nonroutable sef
         *
        if (strripos($pageRequest, '/edit') == (strlen($pageRequest) - 5)) {
        } elseif (strripos($pageRequest, '/add') == (strlen($pageRequest) - 4)) {
        Services::Registry()->set('Parameters', 'request_action', 'add');
         */

        /**
        look up the URL in the catalog first to determine if it's internal
        if (trim($return) == '') {
        Services::Registry()->set('Parameters', 'redirect_on_success', '');

        } elseif (JUri::isInternal(base64_decode($return))) {
        Services::Registry()->set('Parameters', 'redirect_on_success', base64_decode($return));

        } else {
        Services::Registry()->set('Parameters', 'redirect_on_success', '');
        }
         */

        return true;
    }

    /**
     * filterInput
     *
     * @param string $name        Name of input field
     * @param string $field_value Value of input field
     * @param string $dataType    Datatype of input field
     * @param int    $null        0 or 1 - is null allowed
     * @param string $default     Default value, optional
     *
     * @return mixed
     * @since   1.0
     *
     * @throws /Exception
     */
    protected function filterInput($name, $value, $dataType, $null, $default)
    {
        try {
            $value = Services::Filter()->filter($value, $dataType, $null, $default);

        } catch (\Exception $e) {
            //echo $e->getMessage() . ' ' . $name;
            return false;
        }

        return $value;
    }

    /**
     * getRouteParameters
     *
     * Retrieve the Menu Item, Content, Extension and Primary Category Parameters for Route
     *
     * Determine the Theme and Page Values
     *
     * @return null
     * @since   1.0
     *
     * @throws /Exception
     */
    protected function getRouteParameters()
    {
        /**  Menu Item  */
        if (Services::Registry()->get('Parameters', 'catalog_type_id') == CATALOG_TYPE_MENU_ITEM_COMPONENT) {
            $response = Helpers::Content()->getMenuitem();
            if ($response === false) {
                Services::Error()->set(500, 'Menu Item not found');
            }
        }

        /**  Content */
        $response = Helpers::Content()->getRoute();
        if ($response === false) {
            Services::Error()->set(500, 'Content Item not found');
        }

        /**  Category  */
        if ((int) Services::Registry()->get('Parameters', 'catalog_category_id') == 0) {
        } else {
            Helpers::Content()->getRouteCategory();
        }

        /**  Extension */
        $response = Helpers::Extension()->getExtension(
            Services::Registry()->get('Parameters', 'extension_instance_id'),
            ucfirst(strtolower(Services::Registry()->get('Parameters', 'content_catalog_type_title'))),
            'List'
        );
        if ($response === false) {
            Services::Error()->set(500, 'Extension not found');
        }

        /**  Merge in matching Configuration data  */
        Services::Registry()->merge('Configuration', 'Parameters', true);

        Helpers::Extension()->setThemePageView();

        Helpers::Extension()->setTemplateWrapModel();

        Services::Registry()->delete('Parameters', 'item*');
        Services::Registry()->delete('Parameters', 'list*');
        Services::Registry()->delete('Parameters', 'form*');

        Services::Registry()->sort('Parameters');
        Services::Registry()->sort('Metadata');

        /**
        Services::Registry()->get('Parameters', '*');
        Services::Registry()->get('Metadata', '*');
        die;
        */

        return;
    }
}
