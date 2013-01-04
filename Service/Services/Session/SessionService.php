<?php
/**
 * Session Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Session;

use Molajo\Service\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeMemcachedSessionHandler;

defined('NIAMBIE') or die;

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
    public $session;


    public function initialise()
    {
        Services::Registry()->set('User', 1);

        return;
        $session = new Session();
        $session->start();

        // set and get session attributes
        $session->set('name', 'Amy Stephen');
        echo $session->get('name');

        // set flash messages
        $session->getFlashBag()->add('notice', 'Profile updated');

        // retrieve messages
        foreach ($session->getFlashBag()->get('notice', array()) as $message) {
            echo "<div class='flash-notice'>$message</div>";

        }

    }

    /**
     * getSession
     *
     */
    public function getSession()
    {
        $storage = new NativeSessionStorage(array(), new NativeMemcachedSessionHandler());
        $session = new Session($storage);
        //var_dump($session);
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
        $options['cookie_lifetime'] = Services::Registry()->get('Configuration', 'lifetime', 15);
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
