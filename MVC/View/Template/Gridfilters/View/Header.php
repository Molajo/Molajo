<?php
use Molajo\Service\Services;

/**
 *
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('NIAMBIE') or die; ?>
<ol class="filters">
    <li>
        <strong><?php echo Services::Language()->translate('Search'); ?></strong>
    </li>
    <li>
        <input name="search" id="search" type="text" placeholder="<?php echo Services::Language()->translate('Search For'); ?>"/>
    </li>
    <li>
        <input type="submit" class="submit button small right" name="search-button" id="search-button" value="Search">
        <br />
    </li>
    <li>
        <strong><?php echo Services::Language()->translate('Filters'); ?></strong>
    </li>
