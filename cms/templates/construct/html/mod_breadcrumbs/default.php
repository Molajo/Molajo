<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

// Joomla! 1.5, 1.6+

?>
<nav class="breadcrumbs<?php echo $moduleclass_sfx; ?>">
    <?php if ($params->get('showHere', 1)) {
    echo '<h5 class="showHere">' . JText::_('BREADCRUMBS_HERE') . '</h5>';
}
    ?>
    <ol>
        <?php for ($i = 0; $i < $count; $i++) :

        // If not the last item in the breadcrumbs add the separator
        if ($i < $count - 1) {
            if (!empty($list[$i]->link)) {
                echo '<li><a href="' . $list[$i]->link . '" class="pathway">' . $list[$i]->name . '</a></li>';
            } else {
                echo '<li>';
                echo $list[$i]->name;
                echo '</li>';
            }
            if ($i < $count - 2) {
                echo ' ' . $separator . ' ';
            }
        } else if ($params->get('showLast', 1)) { // when $i == $count -1 and 'showLast' is true
            if ($i > 0) {
                echo ' ' . $separator . ' ';
            }
            echo '<li>';
            echo $list[$i]->name;
            echo '</li>';
        }
    endfor; ?>
    </ol>
</nav>