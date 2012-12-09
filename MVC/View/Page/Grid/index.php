<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 *     <include:template name=Toolbar/>
 */
defined('MOLAJO') or die; ?>
<include:template name=Formbegin/>
<include:template name=Gridfilters wrap=nav wrap_class=grid-filters role=nav/>
<section class="grid">
    <include:template name=Gridordering/>
    <include:template name=Gridstatus/>
    <include:template name=Gridpermissions/>
    <include:template name=Gridcategories/>
    <include:template name=Gridtags/>
    <include:template name=Gridfeature/>
    <include:template name=Gridsticky/>
    <include:template name=Gridcheckin/>
    <include:template name=Grid role=main/>
    <include:template name=Pagination wrap=nav wrap_class=pagination/>
</section>
<include:template name=Formend/>
