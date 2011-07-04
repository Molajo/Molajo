<?php
if ($view == 'menu' && ($layout == 'edit' or $layout == 'add')) {

    $pq = phpQuery::newDocument($buffer);
    pq('form[name=adminForm]')->wrapInner('<div class="mc-form-frame mc-padding" />');
    pq('div.width-40')->removeClass('width-40')->addClass('width-60');
    $buffer = $pq->getDocument()->htmlOuter();

} elseif ($view == 'item' && ($layout == 'edit' or $layout == 'add')) {

	$js_init = "window.addEvent('domready', function(){
	 	toggler = document.id('mc-menu-tabs')
	  	element = document.id('mc-menu')
	  	if(element) {
	  		document.switcher = new JSwitcher(toggler, element);
	  	}
	});";

	// add the tabber switcher js
	$this->document->addScript($this->templateUrl.'/js/MC.Switcher.js');
	$this->addInlineScript($js_init);

	$pq = phpQuery::newDocument($buffer);

	$framework = '<div id="mc-menu-key" class="adminform">
					<ul class="adminformlist" />
				  </div>
				  <ul id="mc-menu-tabs" class="mc-form-tabs">
				  	<li><a id="options" class="active">Menu Options</a></li>
					<li><a id="modules">Module Assignments &amp; MetaData</a></li>

				  </ul>
				  <div id="mc-menu" class="adminform">
				   	<div id="page-options">
				   		<div id="mc-details">
				   			<div class="mc-block">
				   				<h3>Menu Details</h3>
				   			</div>
				   		</div>
				   		<div id="mc-options">
				   			<div class="mc-block" />   		
				   		</div>
				   	</div>
				   	<div id="page-modules">
				   		<div id="mc-assignments">
				   			<div class="mc-block" />
				   		</div>
				   		<div id="mc-metadata">
				   			<div class="mc-block" />
				   		</div>
				   	</div>
				  </div>';
	
	
	pq('form[name=adminForm')->prepend($framework);
	pq('#mc-menu-key ul.adminformlist')->append(pq('.width-60 fieldset.adminform .adminformlist > li:lt(4)'));
	pq('#mc-details .mc-block')->append(pq('.width-60 fieldset.adminform ul.adminformlist'));
	pq('#mc-menu')->append(pq('form#item-form input[type="hidden"]'));
	pq('#mc-assignments .mc-block')->append(pq('.width-40 .panel:last'))->removeClass('panel');
	pq('#mc-metadata .mc-block')->append(pq('.width-40 .panel:last'))->removeClass('panel');
	pq('#mc-options .mc-block')->append(pq('.width-40 .panel'))->removeClass('panel');
	
	
//	pq('#mc-menu-key')->append(pq('form[name=adminForm] fieldset.adminform > ul.adminformlist'));
//	pq('#page-editor')->append(pq('form[name=adminForm] .width-60.fltlft > fieldset.adminform'));
//	pq('#mc-pubdata .mc-block')->append(pq('.width-40 .panel:eq(0) fieldset.panelform'));
//	pq('#mc-settings .mc-block')->append(pq('.width-40 .panel:eq(1) fieldset.panelform'));
//	pq('#mc-metadata .mc-block')->append(pq('.width-40 .panel:eq(2) fieldset.panelform'));
//	pq('#mc-permissions .mc-block')->append(pq('.width-100 .pane-sliders'));
//	pq('#jform_extension')->parent('li')->addClass('mc-hidden');
//	
	// remove unused bits
//	pq('#mc-menu-key span.faux-label')->parent('li')->remove();
//	pq('#page-editor legend')->remove();
//	pq('#page-editor label')->remove();
//	pq('#mc-permissions .pane-sliders:eq(0)')->remove();
//	pq('.rule-desc')->remove();
	pq('.width-60.fltlft')->remove();
	pq('.width-40.fltrt')->remove();
    pq('#jform_type-lbl')->next()->addClass('mc-menu-type');
    pq('.mc-menu-type')->next()->attr('rel',"{handler:'clone', target:'menu-types', size: {x: 550, y: 550}}");
    pq('#jform_link')->attr('size',40);
//	pq('.width-100.fltft')->remove();
	
	$buffer = $pq->getDocument()->htmlOuter();
	

} else {
	// needed bits
	$pq = phpQuery::newDocument($buffer);

            pq('form[name=adminForm] fieldset#filter-bar')->wrapInner('<div class="mc-filter-bar" />');

            pq('table.adminlist ')->addClass('mc-list-table');
            pq('table.adminlist a[href*="a.ordering"]')->addClass('mc-ordering-label')->parent('th')->addClass('mc-ordering-col'); 
            
            pq('fieldset.batch')->wrap('<div class="mc-form-frame mc-padding mc-second-block" />');	

            pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
            pq('form[name=adminForm] fieldset#filter-bar')->nextAll('table.mc-first-table')->addClass('mc-second-table')->removeClass('mc-first-table');
            pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
            pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

	
	$buffer = $pq->getDocument()->htmlOuter();



} 


