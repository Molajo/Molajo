<?php
/** 
 * @package     Minima
 * @author      Marco Barbosa
 * @copyright   Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/*
 * Module chrome for rendering the box in the dashboard
 */
function modChrome_widget($module, &$params, &$attribs)
{
    if ($module->content)
    {
        ?>
        <div id="widget-<?php echo $module->id ?>" class="box">
            <div class="box-top">
                <span><?php echo $module->title; ?></span>
                <nav>
                    <span class="box-icon"></span>                
                    <ul>
                        <li><a href="javascript:MinimaWidget.config('<?php echo $module->id ?>');">Settings</a></li>
                        <li><a href="#">Hide</a></li>
                        <li><a href="#">Close</a></li>
                    </ul>
                </nav>
            </div>
            <div class="box-content"><?php echo $module->content; ?></div>
        </div>
        <?php
    }
}
?>
