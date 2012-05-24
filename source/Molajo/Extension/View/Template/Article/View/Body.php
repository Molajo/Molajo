<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * <include:module name=pullquote template=pullquote wrap=aside/>
 */
defined('MOLAJO') or die; ?>
<article>
	<include:wrap name=Header wrap_class=blue {<h2><?php echo $this->row->title; ?></h2>} />
	<include:wrap name=Aside {This is aside text lorem ipsum dolor sit amet consectetur adipisicing} />
	<?php echo $this->row->content_text; ?>
	<include:wrap name=Footer {<h2>Posted August 22, 2011</h2>} />
</article>
