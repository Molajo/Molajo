<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;

//echo '<pre>';
//var_dump($this->row);
//echo '</pre>';
//
// <include:this name=Author wrap_class=blue />
defined('MOLAJO') or die;

Services::Registry()->set('Trigger', 'content_heading', '<h2>' . $this->row->title. '</h2>');
Services::Registry()->set('Trigger', 'content_footer', '<h2>' . 'Posted August 22, 2011'. '</h2>');
?>
<article>
    <include:wrap name=Header wrap_class=blue value=content_heading/>
	<include:wrap name=Aside value=content_text_pullquote/>
    <?php echo $this->row->content_text; ?>
	<include:wrap name=Footer value=content_footer/>
</article>
