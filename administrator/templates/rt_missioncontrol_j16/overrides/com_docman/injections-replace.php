<?php
global $mainframe;

// toolbar work
$toolbar =& $this->toolbar_output;
$pq = phpQuery::newDocument($toolbar);
pq('ul')->append(pq('td')->html());
pq('td')->remove();
pq('ul a')->wrap('<li />');
$toolbar = $pq->getDocument()->htmlOuter();


//force setting of DOCman title
$title = $mainframe->get('JComponentTitle');
$title = str_replace('DOCman ', '', $title);
$title = str_replace('">','">DOCman ', $title);
$mainframe->JComponentTitle = $title;

$pq = phpQuery::newDocument($buffer);

// add list table class for main list
pq('table.adminlist ')->addClass('mc-list-table');

// edit forms
pq('form[name=adminForm] fieldset.adminform:first')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');
pq('div.col:last')->addClass('mc-last-column');
pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');

pq('table.mc-filter-table')->parent('div.mc-form-frame')->removeClass('mc-form-frame');
pq('input#position.combobox')->wrapAll('<div class="mc-position-relative" />');

// generic first/last classes
pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
pq('form[name=adminForm] table.mc-first-table')->next('table')->addClass('mc-second-table');
pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

//docman specific stuff
pq('td[width="55%"]')->parents('table.adminform')->wrapAll('<div class="mc-form-frame" />');
pq('.jpane-slider >table.adminlist')->removeClass('mc-list-table');
pq('form > fieldset.adminform:first')->wrap('<div class="mc-form-frame mc-padding" />');	
pq(':first')->filter('table.adminform')->wrap('<div class="mc-form-frame mc-padding" />');
pq('<div class="clr" />')->appendTo('.mc-form-frame');
pq('dl#configPane')->parent('div')->removeClass('mc-form-frame');

$buffer = $pq->getDocument()->htmlOuter();