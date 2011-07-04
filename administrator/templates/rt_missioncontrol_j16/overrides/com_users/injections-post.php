<?php

pq('form[name=adminForm] .fltrt .mc-panel-padding')->prepend(pq('fieldset#user-groups'));
pq('form[name=adminForm] #sliders ')->wrapAll('<fieldset class="adminform" />');