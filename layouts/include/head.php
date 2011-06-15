<?php
/**
 * @version     $id: item.php
 * @package     Molajo
 * @subpackage  Standard Driver
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  1. Load Language Files
 */
include 'language.php';

/**
 *  2. Process Document Head
 */
if ($this->state->get('request.format') == 'html'
    && $this->state->get('request.initiating_extension_type') == 'component'
    && $this->state->get('request.application') == 'ADMINISTRATOR') {
    include 'document.php';
}

/**
 *  3. Prepare Toolbar
 */
if ($this->state->get('request.layout') == 'modal'
    || !$this->state->get('request.format') == 'html') {
} else {
    include 'toolbar.php';
}

/**
 *  4. Prepare Submenu
 */
if ($this->state->get('request.application') == 'ADMINISTRATOR') {
} else {
    include 'submenu.php';
}

/**
 * 5. Load CSS and JS
 *
 * Automatically includes the following files (if existing)
 *
 * 1. Standard site-wide CSS and JS in => media/site/css[js]/site.css[js]
 * 2. Component specific CSS and JS in => media/site/css[js]/component_option.css[js]
 * 3. Any CSS file in the CSS sub-folder => css/filenames.css
 * 4. Any JS file in the JS sub-folder => js/filenames.js
 *
 * Note: Right-to-left css files should begin with rtl_
 */
include 'css_and_js.php';