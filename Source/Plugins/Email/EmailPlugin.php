<?php
/**
 * Email Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Email;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Email Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class EmailPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * After-read processing
     *
     * Retrieves Author Information for Item
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('email');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name     = $field['name'];
                $new_name = $name . '_' . 'obfuscated';

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $newFieldValue = $this->obfuscateEmail($fieldValue);

                    if ($newFieldValue === false) {
                    } else {
                        $this->setField($field, $new_name, $newFieldValue);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Obfuscate Email
     *
     * @param   string $email_address
     *
     * @return  string
     * @since   1.0
     */
    protected function obfuscateEmail($email_address)
    {
        $obfuscate_email = "";

        for ($i = 0; $i < strlen($email_address); $i ++) {
            $obfuscate_email .= "&#" . ord($email_address[$i]) . ";";
        }

        return $obfuscate_email;
    }
}
