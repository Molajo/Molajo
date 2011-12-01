<?php
/**
 * @package     Molajo
 * @subpackage  Feed
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<table cellpadding="0" cellspacing="0"
       class="moduletable<?php echo htmlspecialchars($parameters->get('layout_class_suffix')); ?>">
<?php
// feed description
    if (!is_null($channel['title']) && $rsstitle) {
        ?>
        <tr>
            <td>
                <strong>
                    <a href="<?php echo htmlspecialchars(str_replace('&', '&amp;', $channel['link'])); ?>"
                       target="_blank">
                        <?php echo htmlspecialchars($channel['title']); ?></a>
                </strong>
            </td>
        </tr>
        <?php

    }

// feed description
    if ($rssdesc) {
        ?>
        <tr>
            <td>
                <?php echo $channel['description']; ?>
            </td>
        </tr>
        <?php

    }

// feed image
    if ($rssimage && $iUrl) {
        ?>
        <tr>
            <td align="center">
                <img src="<?php echo htmlspecialchars($iUrl); ?>" alt="<?php echo htmlspecialchars(@$iTitle); ?>"/>
            </td>
        </tr>
        <?php

    }

    $actualItems = count($feed);
    $setItems = $rssfeed;

    if ($setItems > $actualItems) {
        $totalItems = $actualItems;
    } else {
        $totalItems = $setItems;
    }
    ?>
    <td>
        <ul class="newsfeed<?php echo htmlspecialchars($layout_class_suffix); ?>">