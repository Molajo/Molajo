<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 *
 * <include:module name=pullquote template=pullquote wrap=aside/>
 */
//echo '<pre>';
//var_dump($this->row);
//echo '</pre>';
// <include:wrap name=Footer {<h2>Posted August 22, 2011</h2>} />
defined('MOLAJO') or die; ?>
<article>
    <include:wrap name=Header wrap_class=blue />
	<include:wrap name=Aside model=Pullquote />
    <?php echo $this->row->content_text; ?>

</article>
<include:this name=Author wrap_class=blue />
