<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

echo '<pre>';
var_dump($this->query_results);
echo '</pre>';
die;
defined('MOLAJO') or die; ?>
<include:template name=Admintoolbar wrap=Section value=AdminToolbar/>
<include:template name=Adminsubmenu wrap=Section value=AdminSubmenu/>
<include:template name=Gridfilters wrap=Section value=GridFilters/>
<include:template name=Gridtable wrap=Section value=GridTable*/>
<include:template name=Gridpagination wrap=Section value=GridPagination/>
<include:template name=Gridbatch wrap=Section value=GridBatch/>
