<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
?>
<ul class="pagination">
    

<nav class="pagination">
	<ul>
	    <li>Page <input type="number" min="1" max="9" /> of 9 pages</li>
		<li class="unavailable"><a href="<?php echo $this->row->prev_link; ?>">&laquo;</a></li>
	    <li>View <select><?php for($i=1; $i<=10; $i++): echo '<option value="'.($i*10).'">'.($i*10).'</option>'; endfor ?></select> per page</li>
	    <li>Total number of records: 84</li>
            