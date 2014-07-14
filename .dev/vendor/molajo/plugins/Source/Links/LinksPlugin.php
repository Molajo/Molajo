<?php
/**
 * Links Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Links;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Links Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class LinksPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Text Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $hold_fields;
    /**
     * Text Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $pattern = "/(((http[s]?:\/\/)|(www\/.))?(([a-z][-a-z0-9]+\/.)?[a-z][-a-z0-9]+\/.[a-z]+(\/.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";

    /**
     * Creates Linebreaks in Text Fields
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if ($this->countGetFields() === false) {
            return $this;
        }

        return $this->setLinks();
    }

    /**
     * After-read processing
     *
     * Retrieves Author Information for Item
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setLinks()
    {
        foreach ($this->hold_fields as $field) {

            $text_field = preg_replace($this->pattern, " <a href='$1'>$1</a>", $this->getFieldValue($field));
            $text_field = preg_replace("/href=\"www/", "href=\"http://www", $text_field);

            $this->setField($field, $field['name'], $text_field);
        }

        return $this;
    }
}
