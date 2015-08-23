<?php
/**
 * Navbar Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Navbar;

use CommonApi\Event\DisplayEventInterface;
use stdClass;

/**
 * Navbar Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class NavbarPlugin extends Menu implements DisplayEventInterface
{
    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processPlugin();

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
        if (strtolower($this->controller['parameters']->token->name) === 'navbar') {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Get Navigation Bar Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        $this->getMenu();

        return $this;
    }
}
