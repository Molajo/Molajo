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
     * @since   1.0.0
     */
    public function onBeforeCreate()
    {
        if ($this->checkIfAliasExists() === false) {
            return $this;
        }

        return $this->createAlias();
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeUpdate()
    {
        if ($this->checkIfAliasExists() === false) {
            return $this;
        }

        if ($this->checkIfAliasValueSet() === true) {
            return $this;
        }

        return $this->createAlias();
    }

    /**
     * After update processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterUpdate()
    {
        // TODO: Implement onAfterUpdate() method.
    }

    /**
     * Pre-update processing
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkIfAliasExists()
    {
        if (isset($this->row->title) && isset($this->row->alias)) {
            return true;
        }

        return false;
    }

    /**
     * Pre-update processing
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkIfAliasValueSet()
    {
        if ($this->row->alias === ''
            || $this->row->alias === null
        ) {
            return true;
        }

        return false;
    }

    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function createAlias()
    {
        $this->row->alias = $this->filter('alias', $this->row->alias, 'alias', array());

        //todo: check for existing
        //todo: check for reserved words

        return $this;
    }
}
