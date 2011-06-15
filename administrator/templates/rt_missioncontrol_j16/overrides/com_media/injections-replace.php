<?php

$pq = phpQuery::newDocument($buffer);

pq('fieldset#treeview')->parents('table')->wrap('<div class="mc-form-frame mc-padding" />');

$buffer = $pq->getDocument()->htmlOuter();


