<?php
/**
 * Gravatar Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Gravatar;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Gravatar Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class GravatarPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Size
     *
     * Default  80
     * Range    1 - 2048
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $size = 80;

    /**
     * Maximum Rating
     *
     * Range: G, PG, R, X
     *
     * @var    string
     * @since  1.0.0
     */
    protected $maximum_rating = 'g';

    /**
     * Gravatar Type
     *
     * Options: 404, mm, identicon, monsterid, wavatar
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_image = false;

    /**
     * Use Secure URL
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $use_secure_url = false;

    /**
     * Executes after reading row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processGravatarPlugin();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if ($this->existFields('email') === false) {
            return $this;
        }

        if ((int)$this->runtime_data->application->parameters->display_gravatar === 0) {
            return false;
        }

        return true;
    }

    /**
     * Process the Gravatar Plugin
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processGravatarPlugin()
    {
        $this->setApplicationDefaults();

        $this->processFieldsByType('processEmailFields', $this->hold_fields);

        return $this;
    }

    /**
     * Create Gravatar Link
     *
     * @param   object $field
     *
     * @return  string
     * @since   1.0.0
     */
    protected function processEmailFields($field)
    {
        $name           = $field['name'];
        $field['name']  = $name . '_' . 'gravatar';
        $field['value'] = $this->createGravatarURL($this->getFieldValue($field));
        $field['type']  = 'url';

        return $field;
    }

    /**
     * Create Gravatar URL
     *
     * @param   string $email_address
     *
     * @return  string
     * @since   1.0.0
     */
    protected function createGravatarURL($email_address)
    {
        if ($this->use_secure_url === true) {
            $gravatar_url = 'https://www.gravatar.com/avatar/';
        } else {
            $gravatar_url = 'http://www.gravatar.com/avatar/';
        }

        $gravatar_url .= md5(strtolower(trim($email_address)));
        $gravatar_url .= "?d=" . urlencode(trim($this->gravatar_type));
        $gravatar_url .= "&s=" . (int)$this->size;
        $gravatar_url .= "&r=" . urlencode(trim($this->maximum_rating));

        return $gravatar_url;
    }

    /**
     * Set Gravatar Default values according to Application Values
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setApplicationDefaults()
    {
        $this->size           = (int)$this->runtime_data->application->parameters->gravatar_size;
        $this->gravatar_type  = $this->runtime_data->application->parameters->gravatar_image;
        $this->maximum_rating = $this->runtime_data->application->parameters->gravatar_rating;
        $this->use_secure_url = true;

        return $this;
    }
}
