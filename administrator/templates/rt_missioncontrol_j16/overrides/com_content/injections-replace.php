<?php
if ($layout == 'edit' or $layout == 'add') {

	$js_init = "window.addEvent('domready', function(){
	 	toggler = document.id('mc-article-tabs')
	  	element = document.id('mc-article')
	  	if(element) {
	  		document.switcher = new JSwitcher(toggler, element);
	  	}
	});";

	// add the tabber switcher js
	$this->document->addScript($this->templateUrl.'/js/MC.Switcher.js');
	$this->addInlineScript($js_init);

	$pq = phpQuery::newDocument($buffer);

	$framework = '<div id="mc-article-key" class="adminform" />
				  <ul id="mc-article-tabs" class="mc-form-tabs">
				  	<li><a id="editor" class="active">Article Editor</a></li>
					<li><a id="publishing">Publishing &amp; MetaData</a></li>
					<li><a id="advanced">Advanced &amp; Permissions</a></li>
				  </ul>
				  <div id="mc-article" class="adminform">
				   	<div id="page-editor" />
				   	<div id="page-publishing">
				   		<div id="mc-pubdata">
				   			<div class="mc-block">
				   				<h3>Publishing Options</h3>
				   			</div>
				   		</div>
				   		<div id="mc-metadata">
				   			<div class="mc-block">
				   				<h3>MetaData</h3>
				   			</div>
				   		</div>
				   	</div>
				   	<div id="page-advanced">
				   		<div id="mc-settings">
				   			<div class="mc-block">
				   				<h3>Advanced Options</h3>
				   			</div>
				   		</div>
				   		<div id="mc-permissions">
				   			<div class="mc-block">
				   				<h3>Article Permissions</h3>
				   			</div>
				   		</div>
				   	</div>
				  </div>';
	
	
	pq('form[name=adminForm')->prepend($framework);
	pq('#mc-article-key')->append(pq('form[name=adminForm] fieldset.adminform > ul.adminformlist'));
	pq('#page-editor')->append(pq('form[name=adminForm] .width-60.fltlft > fieldset.adminform'));
	pq('#mc-pubdata .mc-block')->append(pq('.width-40 .panel:eq(0) fieldset.panelform'));
	pq('#mc-settings .mc-block')->append(pq('.width-40 .panel:eq(1) fieldset.panelform'));
	pq('#mc-metadata .mc-block')->append(pq('.width-40 .panel:eq(2) fieldset.panelform'));
	pq('#mc-permissions .mc-block')->append(pq('.width-100 .pane-sliders'));

	
	// TODO
	if ($layout == 'edit') {
	
		// if roktracking found, add editor block
		if (file_exists($this->basePath.DS.'plugins'.DS.'system'.DS.'roktracking'.DS.'roktracking.php')) {
		
			$limit = 10;
			
			$cid = JRequest::getVar('id');
			if (is_array($cid)) $cid = $cid[0];
			
			$db =& MolajoFactory::getDBO();
			$query = 'select r.*, u.name, u.username, u.email,e.name as extension from #__rokadminaudit as r, #__users as u, #__extensions as e where r.user_id = u.id and e.element = r.option and r.cid = '.$cid.' and (r.task ="apply" or r.task="save") order by id desc limit '. intval($limit);
			$db->setQuery($query);

			$results = $db->loadObjectList();
			
			$editors = '<h3 class="title">Editors</h3>';
            if (!empty($results)) {
                $editors .= '<div class="mc-editors-list"><ul>';

                foreach ($results as $r) {
                    $editors .= '<li>'.$r->username.' ('.$r->timestamp.')</li>';
                }
                $editors .= '</ul></div>';
            } else {
                $editors .= 'no one has edited this article...';
            }

			
			pq('#mc-pubdata .mc-block')->append($editors);
		}
	}
	
	// tweaks
	pq('#mc-article-key ul.adminformlist > li:first-child > input')->addClass('mc-bigger-field');
	
	// remove unused bits
	pq('#mc-article-key span.faux-label')->parent('li')->remove();
	pq('#page-editor legend')->remove();
	pq('#jform_articletext-lbl')->remove();
	pq('#mc-permissions .pane-sliders:eq(0)')->remove();
	pq('.rule-desc')->remove();
	pq('.width-60.fltlft')->remove();
	pq('.width-40.fltrt')->remove();
	pq('.width-100.fltft')->remove();
	
	$buffer = $pq->getDocument()->htmlOuter();
	

} else {
	// needed bits
	$pq = phpQuery::newDocument($buffer);

            pq('form[name=adminForm] fieldset#filter-bar')->wrapInner('<div class="mc-filter-bar" />');

            pq('table.adminlist ')->addClass('mc-list-table');
            pq('table.adminlist a[href*="a.ordering"]')->addClass('mc-ordering-label')->parent('th')->addClass('mc-ordering-col'); 

            pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
            pq('form[name=adminForm] fieldset#filter-bar')->nextAll('table.mc-first-table')->addClass('mc-second-table')->removeClass('mc-first-table');
            pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
            pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

	
	$buffer = $pq->getDocument()->htmlOuter();



} 


