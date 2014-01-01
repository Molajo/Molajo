<?php
/**
 * Mock Data Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Mockdata;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Mock Data Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class MockdataPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Replaces {text}250,250,box{/text}
     *
     * {image}250,250,box{/image}
     * {blockquote}{cite:xYZ}*.*{/blockquote}
     * <iframe.+?src="(.+?)".+?<\/iframe>
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        return $this;

        $fields = $this->getFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {
                    $value = $this->search($fieldValue);

                    if ($value === false) {
                    } else {
                        $this->setField($field, $name, $value);
                    }
                }
            }
        }

        return $this;
    }
}
