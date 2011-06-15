<?php
if ($view == 'help') {
	// needed bits
	$pq = phpQuery::newDocument($buffer);
	
	pq('form[name=adminForm]')->wrapAll('<div class="mc-form-frame" />');
	pq('ul.helpmenu')->wrapAll('<div class="mc-toolbar" id="toolbar" />');
	
	// pq('table.adminlist ')->addClass('mc-list-table');
	// pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
	// pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');
	// 
	// 
	// // custom bits
	// pq('div[id$=cellhelp')->wrapAll('<div class="mc-form-frame mc-padding mc-search-data" />');
	// pq('form > div.mc-form-frame:first table.adminform')->removeClass('mc-first-table')->removeClass('adminform')->addClass('mc-search-form');
	// pq('form > div.mc-form-frame:first')->removeClass('mc-form-frame')->removeClass('mc-padding');

	
	$buffer = $pq->getDocument()->htmlOuter();
	
	
	
} elseif ($view == 'profile') {
	
	$pq = phpQuery::newDocument($buffer);

	pq('form[name=adminForm] fieldset.adminform:first')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');

	pq('div.col:last')->addClass('mc-last-column');
	pq('.mc-form-frame .fltlft, .mc-form-frame .fltrt')->wrapInner('<div class="mc-panel-padding" />');
	pq('.mc-form-frame .fltlft .mc-panel-padding > input')->parent()->removeClass('mc-panel-padding');
	//pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
	//pq('form[name=adminForm] .mc-form-frame')->addClass('mc-padding');
	pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');
	pq('<div class="clr"></div>')->appendTo('form[name=adminForm] .mc-form-frame');

$buffer = $pq->getDocument()->htmlOuter();
	
} else {
	$buffer = str_replace('adminlist','adminlist2',$buffer);
	$buffer = str_replace('width="650"','width="75%"',$buffer);
	$buffer = str_replace('width="300"','width="250"',$buffer);
	$pq = phpQuery::newDocument($buffer);
	
	pq('fieldset.adminform')->wrap('<div class="mc-form-frame mc-padding" />');
	
	$buffer = $pq->getDocument()->htmlOuter();



}