<?php

$pq = phpQuery::newDocument($buffer);

//wrap icons on 'community'
if ($view == 'community' or $view == '') {
    pq('')->wrapInner('<div class="mc-form-frame" />');
} elseif ($view == 'configuration') {
    $inputfields = pq('form[name=adminForm]')->find(':input[size]');
    foreach ($inputfields as $field) {
        $size = intval(pq($field)->attr('size'));
        if ($size > 35) {
            pq($field)->attr('size',35);
        }
    }
    pq('')->wrapInner('<div class="mc-form-frame" />');
} else {
    pq('form[name=adminForm] fieldset.adminform:first')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');
    pq('form[name=adminForm] fieldset#filter-bar')->wrapInner('<div class="mc-filter-bar" />');

    pq('table.adminlist ')->addClass('mc-list-table');
    pq('table.adminlist a[href*="a.ordering"]')->addClass('mc-ordering-label')->parent('th')->addClass('mc-ordering-col');

    pq('fieldset.batch')->wrap('<div class="mc-form-frame mc-padding mc-second-block" />');
    pq('#component-form')->addClass('adminform');


    // edit forms
    pq('form[name=adminForm] fieldset.adminform:first')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');

    pq('div.col:last')->addClass('mc-last-column');
    pq('.mc-form-frame .fltlft, .mc-form-frame .fltrt')->wrapInner('<div class="mc-panel-padding" />');
    pq('.mc-form-frame .fltlft .mc-panel-padding > input')->parent()->removeClass('mc-panel-padding');

    pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');
    pq('<div class="clr"></div>')->appendTo('form[name=adminForm] .mc-form-frame');
    pq('input#position.combobox')->wrapAll('<div class="mc-position-relative" />');


    // generic first/last classes
    pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
    pq('form[name=adminForm] fieldset#filter-bar')->nextAll('table.mc-first-table')->addClass('mc-second-table')->removeClass('mc-first-table');
    pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
    pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

}

$buffer = $pq->getDocument()->htmlOuter();