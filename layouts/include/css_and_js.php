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
 * 3. Load CSS and JS
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
if ($this->state->get('layout.loadSiteCSS', true) === true) {
    /** standard site-wide css and js - media/site/css[js]/viewname.css[js] **/
    if (JFile::exists(JPATH_BASE.'/media/site/css/site.css')) {
        $this->document->addStyleSheet(JURI::base().'/site/css/site.css');
    }
    if ($this->document->direction == 'rtl') {
        if (JFile::exists(JPATH_BASE.'/media/site/css/site_rtl.css')) {
            $this->document->addStyleSheet(JURI::base().'/media/site/css/site_rtl.css');
        }
    }
}

if ($this->state->get('layout.loadSiteJS', true) === true) {
    if (JFile::exists(JPATH_BASE.'/media/site/js/site.js')) {
        $this->document->addScript(JURI::base().'/media/site/js/site.js');
    }
}

/** component specific css and js - media/site/css[js]/component_option.css[js] **/
if ($this->state->get('layout.loadComponentCSS', true) === true) {
    if (JFile::exists(JPATH_BASE.'/media/site/css/'.$this->state->get('request.option').'.css')) {
        $this->document->addStyleSheet(JURI::base().'/media/site/css/'.$this->state->get('request.option').'.css');
    }
}

if ($this->state->get('layout.loadComponentJS', true) === true) {
    if (JFile::exists(JPATH_BASE.'/media/site/js/'.$this->state->get('request.option').'.js')) {
        $this->document->addScript(JURI::base().'media/site/js/'.$this->state->get('request.option').'.js');
    }
}

/** Load Layout CSS (if exists in layout CSS folder) */
if ($this->state->get('layout.loadLayoutCSS', true) === true) {
    $files = JFolder::files($this->layoutFolder.'/css', '\.css', false, false);
    foreach ($files as $file) {
        if (substr(strtolower($file), 0, 4) == 'rtl_' && $this->document->direction == 'rtl') {
            $this->document->addStyleSheet($this->layoutFolder.'/css/'.$file);
        } else {
            $this->document->addStyleSheet($this->layoutFolder.'/css/'.$file);
        }
    }
}
    
/** Load Layout JS (if exists in layout JS folder) */
if ($this->state->get('layout.loadLayoutJS', true) === true) {
    $files = JFolder::files($this->layoutFolder.'/js', '\.js', false, false);
    foreach ($files as $file) {
        if (substr(strtolower($file), 0, 4) == 'rtl_' && $this->document->direction == 'rtl') {
            $this->document->addStyleSheet($this->layoutFolder.'/js/'.$file);
        } else {
            $this->document->addStyleSheet($this->layoutFolder.'/js/'.$file);
        }
    }
}