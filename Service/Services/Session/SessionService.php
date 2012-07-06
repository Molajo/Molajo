<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Session;

use Molajo\Service\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeMemcachedSessionHandler;

defined('MOLAJO') or die;

/**
 * Session
 *
 * http://symfony.com/doc/master/components/http_foundation/sessions.html
 *
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Session.html
 *
 * @package   Molajo
 * @subpackage  Services
 * @since           1.0
 */
Class SessionService
{
    /**
     * Response instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Session
     *
     * @var    object
     * @since  1.0
     */
    public $session;

    /**
     * getInstance
     *
     * @static
     * @return object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new SessionService();
        }

        return self::$instance;
    }

    protected function __construct()
    {
        $session = new Session();
        $session->start();

        // set and get session attributes
        $session->set('name', 'Drak');
        $session->get('name');

        // set flash messages
        $session->getFlashBag()->add('notice', 'Profile updated');

        // retrieve messages
        /*
        foreach ($session->getFlashBag()->get('notice', array()) as $message) {
        echo "<div class='flash-notice'>$message</div>";
            die;
        }
        */
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
        $save_path = Services::Registry()->get('Configuration', 'cache_path', SITE_BASE_PATH . '/cache');
        $options = array();
        $options['cookie_lifetime'] = Services::Registry()->get('Configuration', 'lifetime', 15);
        $options['cookie_domain'] = $cookie_domain = Services::Registry()->get('Configuration', 'cookie_domain', '');
        $options['cookie_path'] = $cookie_path = Services::Registry()->get('Configuration', 'cookie_path', '');

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

		$this->addClassesToCompile(array(
			'Symfony\\Bundle\\FrameworkBundle\\EventListener\\SessionListener',
			'Symfony\\Component\\HttpFoundation\\SessionStorage\\SessionStorageInterface',
			$container->getDefinition('session')->getClass(),
		));

		if ($container->hasDefinition($config['storage_id'])) {
			$this->addClassesToCompile(array(
				$container->findDefinition('session.storage')->getClass(),
			));
		}
	}
}
