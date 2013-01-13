<?php
/**
 * Grid Page View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */

defined('MOLAJO') or die;
?>
<include:template name=Toolbar/>
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
