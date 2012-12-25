<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;

$action = Services::Registry()->get(PAGE_LITERAL, 'page_url');
?>

<h3>Effect 7: Accordion</h3>

<div id="accordion" class="accordion">
    <a href="<?php echo $action; ?>#first">This is first tab</a>

    <div id="first">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
            nisi ut aliquip ex ea commodo consequat.</p>
    </div>
    <a href="<?php echo $action; ?>#second">This is second tab</a>

    <div id="second">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
            et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
            aliquip ex ea commodo consequat.</p>
    </div>
    <a href="<?php echo $action; ?>#third">This is third tab</a>

    <div id="third">
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
            et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
            aliquip ex ea commodo consequat. </p>
    </div>
</div>
