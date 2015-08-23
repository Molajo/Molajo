<?php
/**
 * Email Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Email;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Email Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class EmailPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * After-read processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->existFields('email') === false) {
            return $this;
        }

        $this->processFieldsByType('handleEmail', $this->hold_fields);

        return $this;
    }

    /**
     * Handle Email
     *
     * @param   array $fields
     *
     * @return  string
     * @since   1.0.0
     */
    protected function handleEmail(array $field = array())
    {
        $new_name        = $field['name'] . '_' . 'obfuscated';
        $new_field_value = $this->obfuscateEmail($this->getFieldValue($field));
        $field['name']   = $new_name;
        $field['type']   = 'string';
        $field['value']  = $new_field_value;

        return $field;
    }

    /**
     * Obfuscate Email
     *
     * @param   string $email_address
     *
     * @return  string
     * @since   1.0.0
     */
    protected function obfuscateEmail($email_address)
    {
        $obfuscate_email = "";

        for ($i = 0; $i < strlen($email_address); $i++) {
            $obfuscate_email .= "&#" . ord($email_address[$i]) . ";";
        }

        return $obfuscate_email;
    }
}
