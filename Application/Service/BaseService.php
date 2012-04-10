<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Base
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class BaseService
{
    /**
     * Messages
     *
     * @var    Array
     * @since  1.0
     */
    protected $message;

    /**
     * Class constructor.
     *
     * @since  1.0
     */
    protected function __construct()
    {
        $this->message = array(
            300000 => 'Message not defined.'
        );
    }

    /**
     * Return message given message code
     *
     * @param   string  $code  Numeric value associated with message
     *
     * @return  mixed  Array or String
     *
     * @since   1.0
     */
    public function getMessage($code = 0)
    {
        if ($code == 0) {
            return $this->message;
        }

        if (isset($this->message[$code])) {
            return $this->message[$code];
        }

        return $this->DEFAULT_MESSAGE;
    }

    /**
     * Return code given message
     *
     * @param   string  $code  Numeric value associated with message
     *
     * @return  mixed  Array or String
     *
     * @since   1.0
     */
    public function getMessageCode($message = null)
    {
        $this->messageArray = self::get(0);

        $code = array_search($this->message, $this->messageArray);

        if ((int)$code == 0) {
            $code = self::DEFAULT_CODE;
        }

        return $code;
    }
}
