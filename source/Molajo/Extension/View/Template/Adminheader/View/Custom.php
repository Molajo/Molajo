<?php
use Molajo\Service\Services;
/**
 * @package   	Molajo
 * @copyright 	2012 Amy Stephen. All rights reserved.
 * @license   	GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<div class="banner">
	<h1 id="logo"><span class="logo"><?php echo Services::Registry()->get('Configuration', 'site_title', 'Molajo'); ?></span></h1>
	<p class="intro">Intro. <?php echo Services::Registry()->get('Parameters', 'criteria_title'); ?> stuff..</p>
</div>
