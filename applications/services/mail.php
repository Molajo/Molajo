<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Mail
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoMailService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

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
            self::$instance = new MolajoMailService();
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
     * getMailer
     *
     * Get a mailer object
     *
     * @static
     * @return Mailer|null
     * @since 1.0
     */
    public function get()
    {
        $send_mail = Molajo::Application()->get('send_mail');
        $smtpauth = Molajo::Application()->get('smtpauth');
        $smtpuser = Molajo::Application()->get('smtpuser');
        $smtppass = Molajo::Application()->get('smtppass');
        $smtphost = Molajo::Application()->get('smtphost');
        $smtpsecure = Molajo::Application()->get('smtpsecure');
        $smtpport = Molajo::Application()->get('smtpport');
        $mail_from = Molajo::Application()->get('mail');
        $from_name = Molajo::Application()->get('from_name');
        $mailer = Molajo::Application()->get('mailer');

        $mail = MolajoMail::getInstance();
        $mail->setSender(array($mail_from, $from_name));

        switch ($mailer)
        {
            case 'smtp' :
                $mail->useSMTP(
                    $smtpauth,
                    $smtphost,
                    $smtpuser,
                    $smtppass,
                    $smtpsecure,
                    $smtpport);
                break;

            case 'send_mail' :
                $mail->IsSendmail();
                break;

            default :
                $mail->IsMail();
                break;
        }

        return $mail;
    }

    /**
     * Cleans single line inputs.
     *
     * @param   string  $value    String to be cleaned.
     * @return  string  Cleaned string.
     */
    public static function cleanLine($value)
    {
        return trim(preg_replace('/(%0A|%0D|\n+|\r+)/i', '', $value));
    }

    /**
     * Cleans multi-line inputs.
     *
     * @param   string  $value    Multi-line string to be cleaned.
     * @return  string  Cleaned multi-line string.
     */
    public static function cleanText($value)
    {
        return trim(preg_replace('/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i', '', $value));
    }

    /**
     * Cleans any injected headers from the email body.
     *
     * @param   string  $body    email body string.
     * @return  string  Cleaned email body string.
     * @since   1.0
     */
    public static function cleanBody($body)
    {
        // Strip all email headers from a string
        return preg_replace("/((From:|To:|Cc:|Bcc:|Subject:|Content-type:) ([\S]+))/", "", $body);
    }

    /**
     * Cleans any injected headers from the subject string.
     *
     * @param   string  $subject    email subject string.
     * @return  string  Cleaned email subject string.
     * @since   1.0
     */
    public static function cleanSubject($subject)
    {
        return preg_replace("/((From:|To:|Cc:|Bcc:|Content-type:) ([\S]+))/", "", $subject);
    }

    /**
     * Verifies that an email address does not have any extra headers injected into it.
     *
     * @param   string  $address    email address.
     * @return  string  false    email address string or boolean false if injected headers are present.
     * @since   1.0
     */
    public static function cleanAddress($address)
    {
        if (preg_match("[\s;,]", $address)) {
            return false;
        }
        return $address;
    }

    /**
     * Verifies that the string is in a proper email address format.
     *
     * @param   string   $email    String to be verified.
     * @return  boolean  True if string has the correct format; false otherwise.
     * @since   1.0
     */
    public static function isEmailAddress($email)
    {
        // Split the email into a local and domain
        $atIndex = strrpos($email, "@");
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);

        // Check Length of domain
        $domainLen = strlen($domain);
        if ($domainLen < 1 || $domainLen > 255) {
            return false;
        }

        // Check the local address
        // We're a bit more conservative about what constitutes a "legal" address, that is, A-Za-z0-9!#$%&\'*+/=?^_`{|}~-
        // Also, the last character in local cannot be a period ('.')
        $allowed = 'A-Za-z0-9!#&*+=?_-';
        $regex = "/^[$allowed][\.$allowed]{0,63}$/";
        if (!preg_match($regex, $local) || substr($local, -1) == '.') {
            return false;
        }

        // No problem if the domain looks like an IP address, ish
        $regex = '/^[0-9\.]+$/';
        if (preg_match($regex, $domain)) {
            return true;
        }

        // Check Lengths
        $localLen = strlen($local);
        if ($localLen < 1 || $localLen > 64) {
            return false;
        }

        // Check the domain
        $domain_array = explode(".", rtrim($domain, '.'));
        $regex = '/^[A-Za-z0-9-]{0,63}$/';
        foreach ($domain_array as $domain) {

            // Must be something
            if (!$domain) {
                return false;
            }

            // Check for invalid characters
            if (!preg_match($regex, $domain)) {
                return false;
            }

            // Check for a dash at the beginning of the domain
            if (strpos($domain, '-') === 0) {
                return false;
            }

            // Check for a dash at the end of the domain
            $length = strlen($domain) - 1;
            if (strpos($domain, '-', $length) === $length) {
                return false;
            }
        }

        return true;
    }
}
