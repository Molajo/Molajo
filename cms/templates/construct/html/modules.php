<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!+
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php function modChrome_div($module, &$params, &$attribs)
{

    $headerLevel = isset($attribs['level']) ? (int)$attribs['level'] : 5;
    $headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
    $moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
    if (!empty($module->content)) {
        ?>
    <div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> <?php if ($moduleClass) echo $moduleClass; ?>">
        <?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?>
                        class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h' . $headerLevel; ?>
        >
        <?php endif; ?>
        <?php echo $module->content; ?>
    </div>

    <?php

    }
}

?>

<?php function modChrome_aside($module, &$params, &$attribs)
{
    $headerLevel = isset($attribs['level']) ? (int)$attribs['level'] : 4;
    $headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
    $moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
    if (!empty($module->content)) {
        ?>
    <aside class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> <?php if ($moduleClass) echo $moduleClass; ?>">
        <?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?>
                        class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h' . $headerLevel; ?>
        >
        <?php endif; ?>
        <?php echo $module->content; ?>
    </aside>

    <?php

    }
}

?>

<?php function modChrome_figure($module, &$params, &$attribs)
{
    $headerLevel = isset($attribs['level']) ? (int)$attribs['level'] : 4;
    $headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
    $moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
    if (!empty($module->content)) {
        ?>
    <figure class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> <?php if ($moduleClass) echo $moduleClass; ?>">
        <?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?>
                        class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h' . $headerLevel; ?>
        >
        <?php endif; ?>
        <?php echo $module->content; ?>
    </figure>

    <?php

    }
}

?>

<?php function modChrome_footer($module, &$params, &$attribs)
{
    $headerLevel = isset($attribs['level']) ? (int)$attribs['level'] : 5;
    $headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
    $moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
    if (!empty($module->content)) {
        ?>
    <footer class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> <?php if ($moduleClass) echo $moduleClass; ?>">
        <?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?>
                        class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h' . $headerLevel; ?>
        >
        <?php endif; ?>
        <?php echo $module->content; ?>
    </footer>

    <?php

    }
}

?>

<?php function modChrome_header($module, &$params, &$attribs)
{
    $headerLevel = isset($attribs['level']) ? (int)$attribs['level'] : 3;
    $headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
    $moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
    if (!empty($module->content)) {
        ?>
    <header class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> <?php if ($moduleClass) echo $moduleClass; ?>">
        <?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?>
                        class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h' . $headerLevel; ?>
        >
        <?php endif; ?>
        <?php echo $module->content; ?>
    </header>

    <?php

    }
}

?>

<?php function modChrome_nav($module, &$params, &$attribs)
{
    $headerLevel = isset($attribs['level']) ? (int)$attribs['level'] : 3;
    $headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
    $moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
    if (!empty($module->content)) {
        ?>
    <nav class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> <?php if ($moduleClass) echo $moduleClass; ?>">
        <?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?>
                        class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h' . $headerLevel; ?>
        >
        <?php endif; ?>
        <?php echo $module->content; ?>
    </nav>

    <?php

    }
}

?>

<?php function modChrome_section($module, &$params, &$attribs)
{
    $headerLevel = isset($attribs['level']) ? (int)$attribs['level'] : 3;
    $headerClass = isset($attribs['header-class']) ? $attribs['header-class'] : 'je-header';
    $moduleClass = isset($attribs['module-class']) ? $attribs['module-class'] : null;
    if (!empty($module->content)) {
        ?>
    <section
            class="moduletable<?php echo $params->get('moduleclass_sfx'); ?> <?php if ($moduleClass) echo $moduleClass; ?>">
        <?php if ($module->showtitle) : ?>
				<h<?php echo $headerLevel; ?>
                        class="<?php echo $headerClass; ?>"><?php echo $module->title; ?><?php echo '</h' . $headerLevel; ?>
        >
        <?php endif; ?>
        <?php echo $module->content; ?>
    </section>

    <?php

    }
}

?>
