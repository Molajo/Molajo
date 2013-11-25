<?php
/**
 * Email Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Email;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Email Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class EmailInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['service_namespace']        = 'Molajo\\Email\\Adapter';

        parent::__construct($options);
    }

    /**
     * Instantiate a new handler and inject it into the Adapter for the ServiceHandlerInterface
     * Retrieve a list of Interface dependencies and return the data ot the controller.
     *
     * @param   array $reflection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $this->reflection = $reflection;

        $options                            = array();
        $this->dependencies['Runtimedata']  = $options;
        $this->dependencies['Fieldhandler'] = $options;

        return $this->dependencies;
    }

    /**
     * Set Dependency values
     *
     * @param   array $dependency_instances (ignored in Service Item Adapter, based in from handler)
     *
     * @return  $this
     * @since   1.0
     */
    public function processFulfilledDependencies(array $dependency_instances = null)
    {
        parent::processFulfilledDependencies($dependency_instances);

        $this->dependencies['mailer_transport']       = null;
        $this->dependencies['site_name']              = null;
        $this->dependencies['smtpauth']               = null;
        $this->dependencies['smtphost']               = null;
        $this->dependencies['smtpuser']               = null;
        $this->dependencies['smtppass']               = null;
        $this->dependencies['smtpsecure']             = null;
        $this->dependencies['smtpport']               = null;
        $this->dependencies['sendmail_path']          = null;
        $this->dependencies['mailer_disable_sending'] = null;
        $this->dependencies['to']                     = null;
        $this->dependencies['from']                   = null;
        $this->dependencies['reply_to']               = null;
        $this->dependencies['cc']                     = null;
        $this->dependencies['bcc']                    = null;
        $this->dependencies['subject']                = null;
        $this->dependencies['body']                   = null;
        $this->dependencies['mailer_html_or_text']    = null;
        $this->dependencies['attachment']             = null;

        $this->dependencies['mailer_transport']
            = $this->dependencies['Runtimedata']->application->parameters->mailer_transport;

        $this->dependencies['site_name']
            = $this->dependencies['Runtimedata']->application->parameters->application_name;

        switch ($this->dependencies['Runtimedata']->application->parameters->mailer_transport) {

            case 'smtp':
                $this->dependencies['smtpauth']
                    = $this->dependencies['Runtimedata']->application->parameters->mailer_smtpauth;
                $this->dependencies['smtphost']
                    = $this->dependencies['Runtimedata']->application->parameters->mailer_smtphost;
                $this->dependencies['smtpuser']
                    = $this->dependencies['Runtimedata']->application->parameters->mailer_smtpuser;
                $this->dependencies['smtppass']
                    = $this->dependencies['Runtimedata']->application->parameters->mailer_smtppass;
                $this->dependencies['smtpsecure']
                    = $this->dependencies['Runtimedata']->application->parameters->mailer_smtpsecure;
                $this->dependencies['smtpport']
                    = $this->dependencies['Runtimedata']->application->parameters->mailer_smtpport;

                break;

            case 'sendmail':
                $this->dependencies['sendmail_path'] = $this->dependencies['Runtimedata']->application->parameters->mailer_send_mail;

                break;

            default:
                break;
        }

        $this->dependencies['to']
            = $this->dependencies['Runtimedata']->application->parameters->mailer_only_deliver_to;
        $this->dependencies['from']
            = $this->dependencies['Runtimedata']->application->parameters->mailer_mail_from;
        $this->dependencies['reply_to']
                                             = $this->dependencies['Runtimedata']->application->parameters->mailer_mail_reply_to;
        $this->dependencies['email_handler'] = 'phpMailer';
//= $this->dependencies['Runtimedata']->application->parameters->email_handler;
        $this->dependencies['mailer_disable_sending']
            = $this->dependencies['Runtimedata']->application->parameters->mailer_disable_sending;
        $this->dependencies['mailer_disable_sending']
            = 1;

        return $dependency_instances;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        try {
            $handler = $this->getHandler('PhpMailer');

            $this->service_instance = $this->getAdapter($handler);
        } catch (Exception $e) {

            throw new RuntimeException
            ('IoC: Injector Instance Failed for ' . $this->service_namespace
            . ' failed.' . $e->getMessage());
        }

//$this->testEmail();

        return $this;
    }

    /**
     * Get the Email specific Adapter Handler
     *
     * @param   string $adapter_handler
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getHandler($adapter_handler = '')
    {
        if ($adapter_handler === '') {
            $adapter_handler = 'PhpMailer';
        }

        $class = 'Molajo\\Email\\Handler\\' . $adapter_handler;

        try {
            return new $class(
                $this->dependencies['Fieldhandler'],
                $this->dependencies['mailer_transport'],
                $this->dependencies['site_name'],
                $this->dependencies['smtpauth'],
                $this->dependencies['smtphost'],
                $this->dependencies['smtpuser'],
                $this->dependencies['smtppass'],
                $this->dependencies['smtpsecure'],
                $this->dependencies['smtpport'],
                $this->dependencies['sendmail_path'],
                $this->dependencies['mailer_disable_sending'],
                $this->dependencies['to'],
                $this->dependencies['from'],
                $this->dependencies['reply_to'],
                $this->dependencies['cc'],
                $this->dependencies['bcc'],
                $this->dependencies['subject'],
                $this->dependencies['body'],
                $this->dependencies['mailer_html_or_text'],
                $this->dependencies['attachment']
            );
        } catch (Exception $e) {

            throw new RuntimeException
            ('Email: Could not instantiate Email Adapter Handler: ' . $adapter_handler);
        }
    }

    /**
     * Get Email Adapter, inject with specific Email Adapter Handler
     *
     * @param   object $handler
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function getAdapter($handler)
    {
        $class = $this->service_namespace;

        try {
            return new $class($handler);
        } catch (Exception $e) {

            throw new RuntimeException
            ('Email: Could not instantiate Adapter for Email Type: ' . $handler);
        }
    }

    /**
     *  Test after connection
     */
    protected function testEmail()
    {
        /** Test */
        $this->service_instance->set('to', 'AmyStephen@gmail.com,Amy Stephen');
        $this->service_instance->set('from', 'AmyStephen@gmail.com,Amy Stephen');
        $this->service_instance->set('reply_to', 'person@example.com,FName LName');
        $this->service_instance->set('cc', 'person@example.com,FName LName');
        $this->service_instance->set('bcc', 'person@example.com,FName LName');
        $this->service_instance->set('subject', 'Welcome to our Site');
        $this->service_instance->set('body', '<h2>Stuff goes here</h2>');
        $this->service_instance->set('mailer_html_or_text', 'html');

        $this->service_instance->send();
    }
}
