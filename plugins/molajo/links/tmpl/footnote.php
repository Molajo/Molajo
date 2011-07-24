<?php
/**
 * @version     $id: footnote.php
 * @package     Molajo
 * @subpackage  Footnotes
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
if ($evenodd == 'even') { $evenodd = 'odd'; } else { $evenodd = 'even'; }; 
echo $this->fulllink; ?><sup><span id="<?php echo 'footnote'.$this->id; ?>" class="footnote"><?php if (trim($this->linktext) == trim($this->link)) { echo $this->link; } else { echo $this->linktext.' - '.$this->link;         } ?>
    </span>
</sup>