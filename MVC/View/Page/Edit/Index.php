<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 *     <include:template name=Toolbar/>
 */
defined('MOLAJO') or die; ?>
<include:template name=Formbegin/>
<include:template name=Editfilters wrap=nav wrap_class=edit-filters role=nav/>
<section class="edit">
    <include:template name=Editstatus/>
    <include:template name=Editcheckin/>
    <include:request role=main/>
    <include:template name=Editcheckin/>
</section>
<include:template name=Formend/>
