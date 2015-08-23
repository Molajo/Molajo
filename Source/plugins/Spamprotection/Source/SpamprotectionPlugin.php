<?php
/**
 * Spamprotection Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Spamprotection;

use CommonApi\Event\SystemEventInterface;
use Molajo\Plugins\SystemEvent;

/**
 * Spamprotection Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class SpamprotectionPlugin extends SystemEvent implements SystemEventInterface
{
    /**
     * After View Rendering, look for </form> Statement, if found, add CSRF Protection
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeResponse()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this->processSpamprotection();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if ($this->rendered_page === null
            || trim($this->rendered_page) === ''
        ) {
            return false;
        }

        return true;
    }

    /**
     * Process Csrf Token
     *
     * @return  SpamprotectionPlugin
     * @since   1.0.0
     */
    protected function processSpamprotection()
    {
        $beginFormPattern = '/<form(.*)>/';

        preg_match_all($beginFormPattern, $this->rendered_page, $forms);

        if (count($forms[0]) === 0) {
            return $this;
        }

        $this->rendered_page = $this->processForms($forms);

        return $this;
    }

    /**
     * Process Csrf Token
     *
     * @return  string
     * @since   1.0.0
     */
    protected function processForms($forms)
    {
        $form_token = $this->user->getSession('form_token');

        $replace_this = array();
        $with_this    = array();
        $i            = 0;

        $distinct = array_unique($forms[0]);

        foreach ($distinct as $match) {
            $with_this[]    = $this->processForm($match, $form_token);
            $replace_this[] = $match;
            $i++;
        }

        return str_replace($replace_this, $with_this, $this->rendered_page);
    }

    /**
     * Process Form
     *
     * @param   string $match
     * @param   string $form_token
     *
     * @return  string
     * @since   1.0.0
     */
    protected function processForm($match, $form_token)
    {
        return $match
        . chr(10)
        // http://www.modernblue.com/web-design-blog/fighting-spam-with-css/
        . '<input type="text" name="info" style="display: none;" autofill="off" />';
    }

    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeCreate()
    {
        //unique
        return $this;
    }

    /**
     * Pre-update processing
     *
     * @param   $this ->row
     * @param   $model
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeUpdate()
    {
        //reserved words - /edit
        return $this;
    }
}
