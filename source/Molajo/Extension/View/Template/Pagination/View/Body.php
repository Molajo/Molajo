<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
defined('MOLAJO') or die;

$class = 'button ' . strtolower($this->row->name);
$class = ' class="' . $class . '"';
?>
<li><a href="<?php echo $this->row->link; ?>"<?php echo $class; ?>><span><?php echo $this->row->name; ?></span></a></li>

<li><a href="#"><span class="audible">%TYPE% Page</span>1</a></li>
<li><a href="#" rel="prev"><span class="prev">Previous<span class="audible">: %TYPE% Page</span></span>2</a></li>
<li><p><span class="audible">You're currently reading %TYPE% page </span>3</p></li>
<li><a href="#" rel="next"><span class="next">Next<span class="audible">: %TYPE% Page</span></span>4</a></li>
<li><a href="#"><span class="audible">%TYPE% Page </span>5</a></li>
<p id="paginglabel" class="audible">Example Pagination</p>
<ul role="navigation" aria-labelledby="paginglabel">
    <li><a href="#page1"><span class="audible">Example Page</span>1</a></li>
    <li><a href="#page2" rel="prev"><span class="prev">Previous<span class="audible">: Example Page</span></span>2</a>
    </li>

    <li><p><span class="audible">You're currently reading Example page </span>3</p></li>
    <li><a href="#page4" rel="next"><span class="next">Next<span class="audible">: Example Page</span></span>4</a></li>
    <li><a href="#page5"><span class="audible">Example Page </span>5</a></li>
</ul>
</div>
