<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!+
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

?>
<!DOCTYPE html>
<html class="no-js">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="<?php echo $baseUrl . 'templates/' . $this->template; ?>/css/mobile.css"
          type="text/css" media="screen"/>
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.css"/>
    <?php //Load Mobile Extended Template Style Overrides
    $mobileCssFile = $mobileStyleOverride->getIncludeFile();
    if ($mobileCssFile) : ?>
        <link rel="stylesheet" href="<?php echo $baseUrl . $mobileCssFile; ?>" type="text/css" media="screen"/>
        <?php endif; ?>
    <script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
    <script src="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.js"></script>
    <script>
        (function($) { //enable using $ along side of other libraries
            $(document).ready(function() {
                $('html').removeClass("no-js");
            });
        })(jQuery) // releases $ to other libraries
    </script>
</head>

<body>
<div data-role="page" data-theme="<?php echo $mPageDataTheme; ?>">
    <div id="header" data-role="header" data-theme="<?php echo $mHeaderDataTheme; ?>">

        <h1><a href="<?php echo $baseUrl; ?>/"
               title="<?php echo $app->getCfg('sitename'); ?>"><?php echo $app->getCfg('sitename'); ?></a></h1>

        <?php if ($showDiagnostics) : ?>
        <ul id="diagnostics">
            <li>layout override</li>
            <li>column layout <?php echo $columnLayout; ?></li>
            <li>component <?php echo $currentComponent; ?></li>
            <?php if ($view) echo '<li>' . $view . ' view</li>'; ?>
            <?php if ($articleId) echo '<li>article ' . $articleId . '</li>'; ?>
            <?php if ($itemId) echo '<li>menu item ' . $itemId . '</li>'; ?>
            <?php if ($sectionId) echo '<li>section ' . $sectionId . '</li>'; ?>
            <?php if ($catId) echo '<li>category ' . $catId . '</li>'; ?>
            <?php if ($catId && ($inheritStyle || $inheritLayout)) {
            if ($parentCategory) {
                echo '<li>parent category ' . $parentCategory . '</li>';
            }
            $results = getAncestorCategories($catId);
            if ($results) {
                echo '<li>ancestor categories';
                if (count($results) > 0) {
                    foreach ($results as $item) {
                        echo ' ' . $item->id . ' ';
                    }
                }
                echo'</li>';
            }
        } ?>
        </ul>
        <?php endif; ?>
    </div>

    <?php if ($mNavPosition && ($this->countModules('nav'))) : ?>
    <div id="nav">
        <jdoc:include type="modules" name="nav" style="raw"/>
    </div><!-- end nav-->
    <?php endif; ?>

    <div id="content-container" data-role="content" data-theme="<?php echo $mContentDataTheme; ?>">
        <?php if ($this->getBuffer('message')) : ?>
        <jdoc:include type="message"/>
        <?php endif; ?>
        <jdoc:include type="component"/>
    </div>

    <?php if (!$mNavPosition && ($this->countModules('nav'))) : ?>
    <div id="nav">
        <jdoc:include type="modules" name="nav" style="raw"/>
    </div><!-- end nav-->
    <?php endif; ?>

    <div id="footer" data-role="footer" data-theme="<?php echo $mFooterDataTheme; ?>">
        <?php if ($this->countModules('footer')) : ?>
        <jdoc:include type="modules" name="footer" style="xhtml"/>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
