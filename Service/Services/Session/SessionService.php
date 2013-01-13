<?php
/**
 * Session Service
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Session;

use Molajo\Service\Services;

use Symfony\Component\HttpFoundation\Session\Session;

defined('MOLAJO') or die;

/**
 * Session Service Plugin
 *
 * http://symfony.com/doc/master/resources/http_foundation/sessions.html
 *
 * http://api.symfony.com/2.0/Symfony/Resource/HttpFoundation/Session.html
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class SessionService
{
    /**
     * Session
     *
     * @var    object
     * @since  1.0
     */
    protected $session;

    /**
     * Authorised Extension Titles for User
     *
     * @var    array
     * @since  1.0
     */
    protected $authorised_extension_titles = array();

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(

    );


    /**
     *
     */
    public function initialise()
    {
        echo 'yes';
var_dump($_SESSION);
        die;
        session_start();
        $_SESSION['views'] = 1; // store session data
        echo "Pageviews = ". $_SESSION['views']; //retrieve data

        die;
        $session = new Session();
        $session->start();
        die;
        $this->session = new Session();
        $this->session->start();
        echo 'session';
        die;
        // set and get session attributes
        $this->session->set('userid', 1);

        return;

        echo '<pre>';
        var_dump($_SESSION);

        return;
        echo $session->get('Userid');

        // set flash messages
        $session->getFlashBag()->add('notice', 'Profile updated');

        // retrieve messages
        foreach ($session->getFlashBag()->get('notice', array()) as $message) {
            echo "<div class='flash-notice'>$message</div>";

        }

    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if ($this->session->get($key, null) === null) {
            $this->session->set($key, $default);
        }

        return $this->session->get($key);
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  void
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        $this->session->set($key, $value);

        return;
    }

    /**
     * setSessionStorageData
     *
     * @return NativeFileSessionStorage
     */
    public function setSessionStorageData()
    {
        $save_path = Services::Registry()->get(
            'Configuration',
            'system_cache_folder',
            SITE_BASE_PATH . '/cache'
        );
        $options = array();
        $options['cookie_lifetime'] = Services::Application()->get('lifetime', 15);
        $options['cookie_domain'] = $cookie_domain = Services::Registry()->get(
            'Configuration',
            'cookie_domain',
            ''
        );
        $options['cookie_path'] = $cookie_path = Services::Registry()->get(
            'Configuration',
            'cookie_path',
            ''
        );

        $sessionStorage = new NativeFileSessionStorage ($save_path, $options);

        return $sessionStorage;
    }

    /**
     * Loads the session configuration.
     *
     * @param array            $config    A session configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @param XmlFileLoader    $loader    An XmlFileLoader instance
     */
    private function registerSessionConfiguration(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('session.xml');

        // session
        $container->getDefinition('session_listener')->addArgument($config['auto_start']);
        $container->setParameter('session.default_locale', $config['default_locale']);

        // session storage
        $container->setAlias('session.storage', $config['storage_id']);
        $options = array();
        foreach (array('name', 'lifetime', 'path', 'domain', 'secure', 'httponly') as $key) {
            if (isset($config[$key])) {
                $options[$key] = $config[$key];
            }
        }
        $container->setParameter('session.storage.options', $options);

        $this->addClassesToCompile(
            array(
                'Symfony\\Bundle\\FrameworkBundle\\EventListener\\SessionListener',
                'Symfony\\Component\\HttpFoundation\\SessionStorage\\SessionStorageInterface',
                $container->getDefinition('session')->getClass(),
            )
        );

        if ($container->hasDefinition($config['storage_id'])) {
            $this->addClassesToCompile(
                array(
                    $container->findDefinition('session.storage')->getClass(),
                )
            );
        }
    }
}
