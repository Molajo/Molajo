<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php if (count($list) == 1) :
    $item = $list[0];
    modNewsFlashHelper::renderItem($item, $params, $access);
elseif (count($list) > 1) : ?>
<ul class="newsflash-vert<?php echo $params->get('moduleclass_sfx'); ?>">
    <?php foreach ($list as $item) : ?>
    <li>
        <?php modNewsFlashHelper::renderItem($item, $params, $access); ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif;