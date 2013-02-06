<?php
/**
 * Exception Service
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Exception;

defined('MOLAJO') or die;

/**
 * Exception Service
 *
 * In the FrontController
 *  - Initialise uses set_exception_handler to set the exception_handler method as the Exception Handler.
 *  - Initialise uses set_error_handler to set the error_handler method to handle PHP errors.
 *  - error_handler method throws an ErrorException, passing those errors into the exception_handler method
 *  - exception handler instantiates this class, passing in the Exception message, code and Exception
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
Class ExceptionService extends \Exception
{
    /**
     * Class construct
     *
     * @param   string      $message
     * @param   int         $code
     * @param   \Exception  $e
     *
     * @since   1.0
     */
    public function __construct($message, $code, \Exception $e)
    {
        parent::__construct($message, $code, $e);
    }

    /**
     * Format Custom Message
     *
     * @param   null  $title
     * @param   null  $message
     * @param   null  $code
     * @param   int   $display_file
     * @param   int   $display_line
     * @param   int   $display_stack_trace
     * @param   int   $terminate
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
            echo  $this->getPrevious()->getTraceAsString();
            echo '</pre>';
        }

        if ($terminate == 1) {
            echo  'Application will now terminate.';
        }

        $renderedOutput = ob_end_clean();

        echo $renderedOutput;

        if ($terminate == 1) {
            die;
        }
    }
}
