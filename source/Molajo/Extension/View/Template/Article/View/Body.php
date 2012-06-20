<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
?>
<section class="row">
	<nav class="twelve columns">
		<h2><?php echo $this->row->title; ?></h2>
	</nav>
</section>
<section class="row">
	<section class="nine columns">
		<?php echo $this->row->content_text; ?>
		<include:template name=Author wrap=Section value=author/>
	</section>
	<nav class="three columns">
		<include:wrap name=Aside value=content_text_pullquote/>
	</nav>
</section>
