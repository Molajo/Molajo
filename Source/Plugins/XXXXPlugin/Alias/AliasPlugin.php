<?php
/**
 * Alias Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Alias;

use Molajo\Plugins\CreateEventPlugin;
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
        if (isset($this->row->title) && isset($this->row->alias)) {
        } else {
            return $this;
        }

        if ($this->row->alias == '') {
            $this->row->alias = $this->fieldhandler->filter(
                'alias',
                $this->row->alias,
                'alias',
                array()
            );
        } else {
            $this->row->alias = $this->fieldhandler->filter(
                'title',
                $this->row->title,
                'alias',
                array()
            );
        }

        //todo: check for existing
        //todo: check for reserved words

        return $this;
    }
}
