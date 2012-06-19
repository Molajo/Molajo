<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$current_year = Services::Date()->getDate()->format('Y');
$first_year = $this->row->copyright_first_year;

if ($first_year == null || $first_year == '') {
	$ccDateSpan = $current_year;

} elseif ($first_year == $current_year) {
	$ccDateSpan = $first_year;

} else {
	$ccDateSpan = $first_year . '-' . $current_year;
}
?>
<p>
	<?php echo '&#169;' . ' ' . $ccDateSpan . ' ' . $this->row->copyright_holder; ?>
    <a href="<?php echo $this->row->link; ?>">
        <?php echo $this->row->linked_text; ?> v.<?php echo MOLAJOVERSION; ?></a>
    <?php echo ' ' . $this->row->remaining_text; ?>.
</p>
