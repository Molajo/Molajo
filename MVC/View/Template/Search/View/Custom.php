<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;
$action = Services::Registry()->get(PAGE_LITERAL, 'page_url'); ?>
<label>Search</label>
<div class="row">
    <div class="five columns">
        <div class="row collapse">
            <div class="eight mobile-three columns">
                <input type="text"/>
            </div>
            <div class="four mobile-one columns">
                <a href="<?php echo $action; ?>" class="postfix button">Search</a>
            </div>
        </div>
    </div>
</div>
