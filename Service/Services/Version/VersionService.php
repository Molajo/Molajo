<?php
/**
 * Version Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Version;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Version Service
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class VersionService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected $instance;

    /**
     * Current Niambie Version
     */
    const VERSION = '1.0-DEV';

    /**
     * Constructor
     *
     * @return void
     * @since  1.0
     */
    public function __construct()
    {
        if (defined('MOLAJOVERSION')) {
        } else {
            define('MOLAJOVERSION', $this->VERSION);
        }

        return;
    }

    /**
     * Compares a Molajo version with the current one.
     *
     * @param   string  $version
     *
     * @return  int     Indication of Comparison Results:
     *                  -1 Older
     *                  0 Same
     *                  1 Version passed in is newer
     * @since   1.0
     */
    public function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower($this->VERSION));
        $version        = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}
