<?php
/**
 * Site Controller Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Site;

use Exception;
use stdClass;
use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Site Controller Service Provider
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class SiteServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Constructor
     *
     * @param   $options
     *
     * @since   1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace'] = 'Molajo\\Controller\\Site';
        $options['service_name']      = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Identify Class Dependencies for Constructor Injection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        /**
         * To make certain all dependencies are filled before Site runs and continues
         * scheduling from the Resources schedule
         */
        $options                        = array();
        $this->dependencies             = array();
        $this->dependencies['Resource'] = $options;
        $this->dependencies['Request']  = $options;

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  void
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $host     = $this->dependencies['Request']->host;
        $base_url = $this->dependencies['Request']->base_url;
        $path     = $this->dependencies['Request']->path;

        $reference_data = $this->dependencies['Resource']->get('xml:///Molajo//Application//Defines.xml');

        $sites = $this->sites();

        try {
            $class = $this->service_namespace;

            $this->service_instance = new $class(
                $host,
                $base_url,
                $path,
                $reference_data,
                $sites
            );
        } catch (Exception $e) {

            throw new RuntimeException
            ('IoC instantiateService Failed: ' . $this->service_namespace . '  ' . $e->getMessage());
        }

        return;
    }

    /**
     * Installed Sites
     *
     * @return  $this
     * @since   1.0
     */
    public function sites()
    {
        $sitexml = $this->dependencies['Resource']->get('xml:///Molajo//Application//Sites.xml');

        if (count($sitexml) > 0) {
        } else {
            return $this;
        }

        $sites = array();

        foreach ($sitexml as $item) {
            $site                   = new stdClass();
            $site->id               = (string)$item['id'];
            $site->name             = (string)$item['name'];
            $site->site_base_url    = (string)$item['base'];
            $site->site_base_folder = (string)$item['folder'];
            $sites[]                = $site;
        }

        return $sites;
    }
}
