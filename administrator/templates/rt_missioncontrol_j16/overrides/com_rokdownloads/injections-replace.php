<?php
if (JRequest::getString('controller')=='config') {

	$pq = phpQuery::newDocument($buffer);

	pq('table.adminlist ')->addClass('mc-list-table');
	pq('form[name=adminForm]')->wrapInner('<div class="mc-form-frame mc-padding" />');
	


	$buffer = $pq->getDocument()->htmlOuter();
} else {

	$pq = phpQuery::newDocument($buffer);
	pq('table:first')->wrapAll('<div class="mc-form-frame mc-padding" />');
	
	$buffer = $pq->getDocument()->htmlOuter();
}


