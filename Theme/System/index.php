<?php
/**
 * Theme Index.php file that is included in the DisplayController, providing the source for
 * parsing for <include:type/> statements.
 *
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 * <include:profiler/>
 */
defined('NIAMBIE') or die; ?>
<include:head/>
<include:page name=<?php echo $this->row->page_name; ?>/>
<include:defer/>
