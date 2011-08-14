<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<span class="<?php echo $this->rowset[0]->inboxClass; ?>">
    <?php echo $this->rowset[0]->link; ?>
        <?php echo JText::sprintf('LAYOUT_EXTENION_INBOX_UNREAD_MESSAGES', $this->rowset[0]->unread);?>
    <?php echo $this->rowset[0]->linkend; ?>
</span>