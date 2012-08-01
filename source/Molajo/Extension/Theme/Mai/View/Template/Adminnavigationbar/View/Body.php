<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Babs Gösgens. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$_baseUri = Services::Registry()->get('Triggerdata', 'full_page_url');

$_active = false;
foreach (Services::Registry()->get('Triggerdata','Adminbreadcrumbs') as $breadcrumb) {
	if($breadcrumb->url === $this->row->link) {
		$_active = true;
		break;
	}
}
?>
<dt<?php if($_active): ?> class="current"<?php endif ?>>
	<a href="<?php echo $this->row->link; ?>">
		<i class="glyph <?php echo $this->row->catalog_sef_request; ?>"></i><span><?php echo $this->row->link_text; ?></span>
	</a>
	<span><span></span></span>
</dt>
<dd>
	<?php if($this->row->link_text == 'Resources'): ?>
			<include:template name=Adminsectionmenu/>
	<?php else: ?>
					<ul class="level-two">
						<li>
							<a href="#blog"><span>Blog</span></a>
							<ul class="level-three" id="blog">
								<li><a href="#"><span>Articles</span></a><span><span></span></span></li>
								<li><a href="#"><span>Images</span></a></li>
							</ul>
						</li>
						<li>
							<a href="#faqs"><span>F.A.Q.'s</span></a>
							<ul class="level-three" id="faqs">
								<li><a href="#"><span>Articles</span></a><span><span></span></span></li>
								<li><a href="#"><span>Images</span></a></li>
							</ul>
						</li>
						<li>
							<a href="#contact"><span>Contact</span></a>
							<ul class="level-three" id="contact">
								<li><a href="#"><span>Articles</span></a><span><span></span></span></li>
								<li><a href="#"><span>Images</span></a></li>
							</ul>
						</li>
						<li>
							<a href="#downloads"><span>Downloads</span></a>
							<ul class="level-three" id="downloads">
								<li><a href="#"><span>Articles</span></a><span><span></span></span></li>
								<li><a href="#"><span>Images</span></a></li>
							</ul>
						</li>
					</ul>
	<?php endif ?>
</dd>
