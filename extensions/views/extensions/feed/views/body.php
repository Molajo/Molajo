<?php
/**
 * @package     Molajo
 * @subpackage  Feed
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<tr>
<li>
<?php
if (!is_null($this->row->get_link())) {
    ?>
    <a href="<?php echo htmlspecialchars($this->row->get_link()); ?>" target="_child">
        <?php echo htmlspecialchars($this->row->get_title()); ?></a>
    <?php

}

// feed description
    if ($rssfeeddesc) {
        // feed description
        $text = $filter->clean(html_entity_decode($this->row->get_description(), ENT_COMPAT, 'UTF-8'));
        $text = str_replace('&apos;', "'", $text);

        // word limit check
        if ($words) {
            $texts = explode(' ', $text);
            $count = count($texts);
            if ($count > $words) {
                $text = '';
                for ($i = 0; $i < $words; $i++)
                {
                    $text .= ' ' . $texts[$i];
                }
                $text .= '...';
            }
        }
        ?>
        <div style="text-align: <?php echo $rssrtl ? 'right' : 'left'; ?> !important">
            <?php echo $text; ?>
        </div>
        <?php

    }
    ?>
</li>
