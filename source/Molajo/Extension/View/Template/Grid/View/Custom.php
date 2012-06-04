<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

echo '<pre>';
var_dump($this->query_results);
echo '</pre>';
defined('MOLAJO') or die; ?>
<include:template name=Admintoolbar wrap=Section value=admintoolbar*/>
<include:template name=Adminsubmenu wrap=Section value=adminsubmenu*/>
<include:template name=Gridfilters wrap=Section value=gridfilters*/>
<include:template name=Gridtable wrap=Section value=gridtable*/>
<include:template name=Gridpagination wrap=Section value=gridpagination*/>
<include:template name=Gridbatch wrap=Section value=gridbatch*/>
