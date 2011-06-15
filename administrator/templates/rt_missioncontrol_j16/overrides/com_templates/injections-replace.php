<?php
// old
//pq('table.adminform tr:first-child th:last-child')->wrapInner('<div class="mc-button" />');

global $moo_override;

if ($layout == 'edit') {

	$pq = phpQuery::newDocument($buffer);
	
	pq('form[name=adminForm] fieldset.adminform')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');
	pq('div.col:last')->addClass('mc-last-column');
	pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
	pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');
	pq('table.mc-filter-table')->parent('div.mc-form-frame')->removeClass('mc-form-frame');
    pq('.mc-form-frame .fltlft, .mc-form-frame .fltrt')->wrapInner('<div class="mc-panel-padding" />');
    pq('.mc-form-frame .fltlft .mc-panel-padding > input')->parent()->removeClass('mc-panel-padding');

	pq('#jform_title')->attr('size','25');
	
	pq('.width-40 + .width-60')->addClass('wrap-around');
	pq('div.pane-sliders > div[style="display:none;"]:last-child')->parent()->addClass('no-borders');
    
    // // set moo override if moo 1.3 template
    // $cid = JRequest::getVar('cid');
    // if (is_array($cid)) {
    //     $moo13= $this->basePath . DS . 'components' . DS . 'com_gantry' . DS . 'js' . DS . 'mootools.js';
    //     if (file_exists($moo13)) $moo_override = true;
    // }
	
	$buffer = $pq->getDocument()->htmlOuter();

} elseif ($task == 'preview' || $task == 'edit_source' || $task == 'edit_css') {

	$pq = phpQuery::newDocument($buffer);

	pq('table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');

	$buffer = $pq->getDocument()->htmlOuter();

} else {

	//list view
	$pq = phpQuery::newDocument($buffer);

	// add filter-table class for filter
	pq('form[name=adminForm] td:contains("Filter") > input[type=text]')->parents('table')->addClass('mc-filter-table');
	pq('form[name=adminForm] td:contains("toggle state")')->parents('table')->addClass('mc-legend-table');
	pq('table.adminlist ')->addClass('mc-list-table');
	pq('table.adminlist')->prev('table')->addClass('mc-filter-table');
    pq('form[name=adminForm] fieldset#filter-bar')->wrapInner('<div class="mc-filter-bar" />');

	
	// generic first/last classes
	pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
    pq('form[name=adminForm] fieldset#filter-bar')->nextAll('table.mc-first-table')->addClass('mc-second-table')->removeClass('mc-first-table');
	pq('form[name=adminForm] table.mc-first-table')->next('table')->addClass('mc-second-table');
	pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
	pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

	
	$buffer = $pq->getDocument()->htmlOuter();
}