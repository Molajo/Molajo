<?php
pq('#image > [id*="width"]')->wrapAll('<div class="width-row" />');
pq('#image > [id*="height"]')->wrapAll('<div class="height-row" />');
