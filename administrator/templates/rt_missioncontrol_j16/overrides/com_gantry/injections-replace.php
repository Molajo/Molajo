<?php
if ($layout == 'edit') {

	$pq = phpQuery::newDocument($buffer);
	
	pq('form[name=adminForm] fieldset.adminform')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');
	// pq('div.col:last')->addClass('mc-last-column');
	// pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
	// pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');
	// pq('table.mc-filter-table')->parent('div.mc-form-frame')->removeClass('mc-form-frame');
	//     pq('.mc-form-frame .fltlft, .mc-form-frame .fltrt')->wrapInner('<div class="mc-panel-padding" />');
	//     pq('.mc-form-frame .fltlft .mc-panel-padding > input')->parent()->removeClass('mc-panel-padding');
	// 
	// pq('#jform_title')->attr('size','25');
    
    // // set moo override if moo 1.3 template
    // $cid = JRequest::getVar('cid');
    // if (is_array($cid)) {
    //     $moo13= $this->basePath . DS . 'components' . DS . 'com_gantry' . DS . 'js' . DS . 'mootools.js';
    //     if (file_exists($moo13)) $moo_override = true;
    // }
	
	$buffer = $pq->getDocument()->htmlOuter();

}