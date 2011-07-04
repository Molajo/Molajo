<?php
/**
 * @version     $id: pullquote.php
 * @package     Molajo
 * @subpackage  Responses Component
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base().'media/molajo/css/quotes.css' );
if ($evenodd == 'even') { $evenodd = 'odd'; } else { $evenodd = 'even'; } ?>

<span id="<?php echo 'pq'.$this->unique; ?>" class="pullquote <?php echo 'pq'.$evenodd; ?>"><?php echo $this->excerpt; ?></span>

