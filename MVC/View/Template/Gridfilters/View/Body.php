<?php
/**
 * Gridfilters Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die; ?>
<li>
    <include:template name=formselectlist wrap=none datalist=<?php echo $this->row->listname; ?>/>
</li>
