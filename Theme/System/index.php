<?php
/**
 * System Theme
 *
 * Theme Index.php file that is included in the DisplayController, providing the source for
 * parsing for <include:type/> statements.
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die; ?>
<include:head/>
<include:page name=<?php echo $this->row->page_name; ?>/>
<include:defer/>
