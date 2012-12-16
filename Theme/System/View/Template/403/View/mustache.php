<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die; ?>
{{# items }}
<header>
    <h2>{{title}}</h2>

    <h3>{{hello}}</h3>
</header>

<img src="{{gravatar}}" alt="{{name}}" class="alignright"/>
{{{intro}}}
{{{fulltext}}}
<footer>
    {{start_publishing_datetime}}
</footer>
{{/ items }}
{{{dashboard}}}
{{{placeholder}}}
