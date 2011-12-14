<?php
/**
 * @version     $id: default.php
 * @package     Molajo
 * @subpackage  Responses Component
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** done in layout in case designer does not want: remove first and last p tags to ensure cite within blockquote **/
if (substr($this->excerpt, 0, 3) == '<p>') {
    $this->excerpt = substr($this->excerpt, 3, strlen($this->excerpt) - 3);
}
if (substr($this->excerpt, strlen($this->excerpt) - 4, 4) == '</p>') {
    $this->excerpt = substr($this->excerpt, 0, strlen($this->excerpt) - 4);
}

$document =& MolajoFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'media/css/quotes.css');
if ($evenodd == 'even') {
    $evenodd = 'odd';
} else {
    $evenodd = 'even';
} ?>

<blockquote id="<?php echo 'bq' . $this->unique; ?>" class="<?php echo 'bq' . $evenodd; ?>">
    <p>
        <?php echo $this->excerpt; ?>
        <?php if (!$this->cite == '') { ?>
        <cite id="<?php echo 'cite' . $this->unique; ?>" class="<?php echo 'cite' . $evenodd; ?>">
            <?php echo $this->cite; ?>
        </cite>
        <?php } ?>
    </p>
</blockquote>
