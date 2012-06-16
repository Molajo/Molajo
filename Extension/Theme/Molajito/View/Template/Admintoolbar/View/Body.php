<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$class = ' toolbar ' . strtolower($this->row->name);
$class = ' class="' . $class . '"';
$class = 'last-in-row';

$element = '';

if (strtolower($this->row->name) == 'apply') {
	$element = 'f';
} else if (strtolower($this->row->name) == 'save') {
	$element = '9';
} elseif (strtolower($this->row->name) == 'archive') {
	$element = '\\';
} elseif (strtolower($this->row->name) == 'unarchive') {
	$element = ']';
} elseif (strtolower($this->row->name) == 'cancel') {
	$element = ';';
} elseif (strtolower($this->row->name) == 'checkin') {
	$element = '7';
} elseif (strtolower($this->row->name) == 'copy'
		|| strtolower($this->row->name) == 'saveascopy') {
	$element = '\'';
} elseif (strtolower($this->row->name) == 'configure'
	|| strtolower($this->row->name) == 'options') {
	$element = 'a';
} elseif (strtolower($this->row->name) == 'create'
	|| strtolower($this->row->name) == 'new'
	|| strtolower($this->row->name) == 'saveandnew') {
	$element = 'z';
} elseif (strtolower($this->row->name) == 'delete') {
	$element = 'g';
} elseif (strtolower($this->row->name) == 'display') {
} elseif (strtolower($this->row->name) == 'edit') {
	$element = '[';
} elseif (strtolower($this->row->name) == 'feature'
	|| (strtolower($this->row->name) == 'unfeature')) {
	$element = 'c';
} elseif (strtolower($this->row->name) == 'move') {
	$element = 'u';
} elseif (strtolower($this->row->name) == 'orderdown') {
	$element = 'x';
} elseif (strtolower($this->row->name) == 'orderup') {
	$element = 'w';
} elseif (strtolower($this->row->name) == 'publish'
		|| strtolower($this->row->name) == 'unpublish') {
	$element = '1';
} elseif (strtolower($this->row->name) == 'reorder') {
	$element = ';';
} elseif (strtolower($this->row->name) == 'restore') {
	$element = 's';
} elseif (strtolower($this->row->name) == 'spam') {
	$element = '5';
} elseif (strtolower($this->row->name) == 'sticky'
		|| strtolower($this->row->name) == 'unsticky') {

} elseif (strtolower($this->row->name) == 'trash'
		|| strtolower($this->row->name) == 'untrash') {
	$element = 'y';
}

?>
<li class="toolbar"><a href="<?php echo $this->row->link; ?>"><span class="glyph general"><?php echo $element; ?></span></a>
</li>
