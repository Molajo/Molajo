<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<div class="newsflash<?php echo $params->get('moduleclass_sfx') ?>">

    <?php
        srand((double)microtime() * 1000000);
    $flashnum = rand(0, $items - 1);
    $item = $list[$flashnum];
    modNewsFlashHelper::renderItem($item, $params, $access);
    ?>

</div>
