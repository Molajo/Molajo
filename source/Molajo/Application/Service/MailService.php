<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Mail
 *
 * Edits, filters input, sends email
 *
 * Example usage:
 *
 * $results = Services::Mail()
 *  ->set('send_to_email', 'person@example.com')
 *  ->set('from_email', 'admin@example.com')
 *  ->set('subject', 'Welcome to our Site')
 *  ->set('body', $bodyofemail)
 *  ->send($message);
 *
 * Valid parameters:
 * send_to_email - array of email addresses for recipients
 * cc_email - array of email_addresses to blind copy
 * bcc_email - array of email_addresses to blind copy
 * from_email - email_address to use as the from address
 * from_name - name to use as the from name
 * reply_to_email - email_address to which this email is a response
 * reply_to_name - name to use as the reply to
 * subject - title of email
 * body - email contents
 * mode - html or default (plain text)
 * attachment - file name of attachment
 *
 * @package   Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class MailService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Configuration
     *
     * @var    object
     * @since  1.0
     */
    protected $configuration;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MailService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {

    }

    /**
     * get
     *
     * Returns a set property or it's default for the mail object
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed   The value of the configuration.
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->configuration->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the mail object
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed   Previous value of the property
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->configuration->set($key, $value);
    }

    /**
     * send
     *
     * Checks permissions, validates data elements, and sends email
     *
     * @return  mixed
     * @since   1.0
     */
    public function send()
    {
        if (Services::Registry()->get('Configuration\\disable_sending', 1) == 1) {
            return true;
        }

        $this->configuration = Services::Registry()->initialise();

        $results = $this->permission();
        if ($results === true) {
            return $results;
        }

        $only_deliver_to = Services::Registry()->get('Configuration\\only_deliver_to', '');
        if (trim($only_deliver_to) == '') {
        } else {
            $this->set('cc_email', array());
            $this->set('bcc_email', array());
            $this->set('reply_to_email', '');
            $this->set('send_to_email', $only_deliver_to);
        }

        $results = $this->edit_and_filter_input();
        if ($results === true) {
        } else {
            return false;
        }

        /** Get instance */
        $mailClass = $this->get('mail_class', 'JMail');
        $mail = $mailClass::getInstance();

        /** Set type of email */
        switch (Services::Registry()->get('Configuration\\mailer')) {
            case 'smtp':
                $mail->useSMTP(
                    Services::Registry()->get('Configuration\\smtpauth'),
                    Services::Registry()->get('Configuration\\smtphost'),
                    Services::Registry()->get('Configuration\\smtpuser'),
                    Services::Registry()->get('Configuration\\smtppass'),
                    Services::Registry()->get('Configuration\\smtpsecure'),
                    Services::Registry()->get('Configuration\\smtpport')
                );
                break;

            case 'sendmail':
                $mail->IsSendmail();
                break;

            default:
                $mail->IsMail();
                break;
        }

        $mail->SetFrom(
            $this->get('from_email'),
            $this->get('from_name'),
            0
        );

        $results = $mail->sendMail(
            $this->get('from_email'),
            $this->get('from_name'),
            $this->get('send_to_email'),
            $this->get('subject'),
            $this->get('body'),
            $this->get('mode'),
            $this->get('cc_email'),
            $this->get('bcc_email'),
            $this->get('attachment'),
            $this->get('reply_to_email'),
            $this->get('reply_to_name')
        );

        return $results;
    }

    /**
     * permission
     *
     * Verify user and extension have permission to send email
     *
     * @return bool
     * @since  1.0
     */
    protected function permission()
    {
        $permission = true;

        /** Component (authorises any user) */

        /** User */

        /** authorization event */
        //todo: what is the asset id of a service?
        //$results = Services::Access()->authoriseTask('email', $asset_id);

        return $permission;
    }

    /**
     * edit_and_filter_input
     *
     * Verify all data required is available and filter input for security
     *
     * @return bool|int
     */
    protected function edit_and_filter_input()
    {
        $error = '';

        /** Permission */
        $results = $this->permission();
        if ($results == false) {
            return 304;
        }

        /** From Email Address */
        $name = 'from_email';
        $value = $this->get('from_email');
        $dataType = 'email';
        $results = $this->edit_and_filter_input($name, $value, $dataType);
        if ($results === false) {
            return false;
        }
        $this->set('from_email', $results);

        /** From Name */
        $name = 'from_name';
        $value = $this->get('from_name', Services::Registry()->get('Configuration\\site_name'));
        $dataType = 'char';
        $results = $this->edit_and_filter_input($name, $value, $dataType);
        if ($results === false) {
            return false;
        }
        $this->set('from_name', $results);

        /** Send to Email Address */
        $name = 'send_to_email';
        $values = $this->get('send_to_email');
        $dataType = 'email';
        if (is_array($value)) {
        } else {
            $values = array($value);
        }
        $validated = array();
        if (count($values) > 0) {
            foreach ($values as $value) {
                $results = $this->edit_and_filter_input($name, $value, $dataType);
                if ($results === false) {
                    return false;
                } else {
                    $validated[] = $results;
                }
            }
        }
        $this->set('send_to_email', $validated);

        /** Subject */
        $name = 'subject';
        $value = $this->get('subject', Services::Registry()->get('Configuration\\site_name'));
        $dataType = 'char';
        $results = $this->edit_and_filter_input($name, $value, $dataType);
        if ($results === false) {
            return false;
        }
        $this->set('subject', $results);

        /** Body */
        $name = 'body';
        $value = $this->get('body');
        if ($this->get('mode', 'text') == 'html') {
            $dataType = 'html';
        } else {
            $dataType = 'text';
        }
        $results = $this->edit_and_filter_input($name, $value, $dataType);
        if ($results === false) {
            return false;
        }
        $this->set('body', $results);

        /** Copy Email Address */
        $name = 'cc_email';
        $values = $this->get('cc_email');
        $dataType = 'email';
        if (is_array($value)) {
        } else {
            $values = array($value);
        }
        $validated = array();
        if (count($values) > 0) {
            foreach ($values as $value) {
                $results = $this->edit_and_filter_input($name, $value, $dataType);
                if ($results === false) {
                    return false;
                } else {
                    $validated[] = $results;
                }
            }
        }
        $this->set('cc_email', $validated);

        /** Blind Copy Email Address */
        $name = 'bcc_email';
        $values = $this->get('bcc_email');
        $dataType = 'email';
        if (is_array($value)) {
        } else {
            $values = array($value);
        }
        $validated = array();
        if (count($values) > 0) {
            foreach ($values as $value) {
                $results = $this->edit_and_filter_input($name, $value, $dataType);
                if ($results === false) {
                    return false;
                } else {
                    $validated[] = $results;
                }
            }
        }
        $this->set('bcc_email', $validated);

        /** Attachment */
        $name = 'attachment';
        $values = $this->get('attachment');
        $dataType = 'file';
        if (is_array($value)) {
        } else {
            $values = array($value);
        }
        $validated = array();
        if (count($values) > 0) {
            foreach ($values as $value) {
                $results = $this->edit_and_filter_input($name, $value, $dataType);
                if ($results === false) {
                    return false;
                } else {
                    $validated[] = $results;
                }
            }
        }
        $this->set('attachment', $validated);

        /** Reply to Email Address */
        $name = 'reply_to_email';
        $value = $this->get('reply_to_email');
        $dataType = 'email';
        $results = $this->edit_and_filter_input($name, $value, $dataType);
        if ($results === false) {
            return false;
        }
        $this->set('reply_to_email', $results);

        /** Reply to Name */
        $name = 'reply_to_name';
        $value = $this->get('reply_to_name');
        $dataType = 'char';
        $results = $this->edit_and_filter_input($name, $value, $dataType);
        if ($results === false) {
            return false;
        }
        $this->set('reply_to_name', $results);

        return true;
    }

    /**
     * call_security_filter
     *
     * @param   string  $name         Name of input field
     * @param   string  $field_value  Value of input field
     * @param   string  $dataType     Datatype of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     */
    protected function call_security_filter(
        $name, $value, $dataType, $null = null, $default = null)
    {
        try {
            $value = Services::Security()->filter(
                $value, $dataType, $null, $default);

        } catch (Exception $e) {
            $value = false;
            Services::Message()->set(
                $message = Services::Language()->translate($e->getMessage()) . ' ' . $name,
                $type = MESSAGE_TYPE_ERROR
            );
            if (Services::Registry()->get('Configuration\\debug', 0) == 1) {
                Services::Debug()->set('Services::mail Filter Failed' . ' ' . $message);
            }
        }

        return $value;
    }
}
