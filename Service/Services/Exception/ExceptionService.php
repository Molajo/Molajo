<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Exception;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Exception
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class ExceptionService extends \Exception
{
    /**
     * Class construct
     *
     * @param   string      $message
     * @param   int         $code
     * @param   \Exception  $this
     *
     * Review for stack issue http://fabien.potencier.org/article/9/php-serialization-stack-traces-and-exceptions
     *
     * @return  void
     * @since   1.0
     */
    public function __construct($message, $code, \Exception $e)
    {
        parent::__construct($message, $code, $e);
    }

    /**
     * Format Custom Message
     *
     * @return  string
     * @since   1.0
     */
    public function formatMessage(
        $title = null,
        $message = null,
        $code = null,
        $display_file = 1,
        $display_line = 1,
        $display_stack_trace = 1,
        $terminate = 1
    ) {

        if ($title === null) {
            $title = 'Molajo Exception Information';
        }

        if ($message === null) {
            $message = $this->getMessage();
        }

        if ($code === null) {
            $code = $this->getCode();
        }

        $this->message = '';
        $this->message .= '<strong>' . $title . '</strong> ' . '<br />';
        $this->message .= '<strong>Date: </strong>' . date('M d, Y h:iA') . '<br />';
        $this->message .= '<strong>Message: </strong>' . $message . '<br />';

        if ($code === null) {
        } else {
            $this->message .= '<strong>Code: </strong>' . $this->getCode() . '<br />';
        }

        if ($display_file == 1) {
            $this->message .= '<strong>File: </strong>' . $this->getFile() . '<br />';
        }

        if ($display_line == 1) {
            $this->message .= '<strong>Line: </strong>' . $this->getLine() . '<br />';
        }

        ob_start();

        echo $this->message;

        if ($display_stack_trace == 1) {
            echo '<strong>Stack Trace: </strong><br />';
            echo '<pre>';
            echo $this->getTraceAsString();
            echo '</pre>';
        }

        if ($terminate == 1) {
            echo  'Application will now terminate.';
        }

        $renderedOutput = ob_get_contents();
        ob_end_clean();

        echo $renderedOutput;

        if ($terminate == 1) {
            die;
        }
    }
}
