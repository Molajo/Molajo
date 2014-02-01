<?php
/**
 * Csrftoken Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Csrftoken;

use CommonApi\Event\CreateInterface;
use CommonApi\Event\DeleteInterface;
use CommonApi\Event\DisplayInterface;
use CommonApi\Event\UpdateInterface;
use Molajo\Plugin\AbstractPlugin;

/**
 * Csrftoken Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CsrftokenPlugin extends AbstractPlugin
{
    /**
     * After View Rendering, look for </form> Statement, if found, add CSRF Protection
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeResponse()
    {
        $rendered = $this->rendered_page;

        if ($rendered === null
            || trim($rendered) == ''
        ) {
            return $this;
        }

        $beginFormPattern = '/<form(.*)>/';

        preg_match_all($beginFormPattern, $rendered, $forms);

        if (count($forms[0]) == 0) {
            return $this;
        }

        $replaceThis = array();
        $withThis    = array();
        $i           = 0;

        $distinct = array_unique($forms[0]);

        foreach ($distinct as $match) {

            $formToken = serialize(rand(rand(50000, 500000), rand(500000, 50000000)));

            $withThis[] = $match
                . chr(10)
                . '<input name="info" type="hidden" value="">'
                . chr(10)
                . '<input type="hidden" name="' . $formToken . '" value="token">';

            $replaceThis[] = $match;
            $i ++;
        }

        $temp = str_replace($replaceThis, $withThis, $rendered);

        if ($rendered == $temp) {
            return $this;
        }

        $this->rendered_page = $temp;

        return $this;
    }

    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0
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
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        //reserved words - /edit
        return $this;
    }
}
