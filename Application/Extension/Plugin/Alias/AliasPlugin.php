<?php
/**
 * Alias Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Alias;

use Molajo\Plugin\CreateEventPlugin;
use CommonApi\Event\CreateInterface;
use CommonApi\Event\UpdateInterface;

/**
 * Alias Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class AliasPlugin extends CreateEventPlugin implements CreateInterface, UpdateInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        return $this->createAlias();
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return $this->createAlias();
    }

    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0
     */
    protected function createAlias()
    {
        if (isset($this->query_results->title) && isset($this->query_results->alias)) {
        } else {
            return $this;
        }

        if ($this->query_results->alias == '') {
            $this->query_results->alias = $this->fieldhandler->filter(
                'alias',
                $this->query_results->alias,
                'alias',
                array()
            );
        } else {
            $this->query_results->alias = $this->fieldhandler->filter(
                'title',
                $this->query_results->title,
                'alias',
                array()
            );
        }

        //todo: check for existing
        //todo: check for reserved words

        return $this;
    }
}
