<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Smilies;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class SmiliesTrigger extends ContentTrigger
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
            self::$instance = new SmiliesTrigger();
        }

        return self::$instance;
    }

    /**
     * After-read processing
     *
     * Replaces text with emotion images
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->query_results->content_text)) {
        } else {
            return false;
        }

        $this->query_results->content_text =
            Services::Text()->smilies($this->query_results->content_text);

        return true;
    }
}
