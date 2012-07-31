<?php
/**
 * @package     Molajo
 * @copyright   2012 Amy Stephen. All rights reserved.
 * @license     GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
?>
<div class="row">
    <div class="twelve columns">
        <div class="row">
            <div class="six columns">
                <label>Title</label>
                <input type="text" placeholder="Title"/>
            </div>
            <div class="six columns">
                <label>Author</label>
                <input type="text" placeholder="Author"/>
            </div>
        </div>
        <include:template name=Editeditor/>
        <div class="row">
            <div class="six columns">
                <label>Tags</label>
                <input type="text" placeholder="Title"/>
            </div>
            <div class="six columns">
                <label>Categories</label>
                <input type="text" placeholder="Title"/>
            </div>
        </div>
    </div>
</div>
