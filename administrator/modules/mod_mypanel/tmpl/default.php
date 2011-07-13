<?php
/**
 * @package     Minima
 * @subpackage  mod_mypanel
 * @author      Marco Barbosa
 * @copyright   Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$items = ModMypanelHelper::getItems();
$invisible = false;

$nPages = ceil( count($items) / 9);
//$nPages = ceil( (count($items)*3) / 9);

// hide arrows if items lower or equal 9
//if (count($items) <= 9) $invisible = true;
?>
<div id="panel">
    <!-- search field -->
    <!--<input type="text" id="search-term" placeholder="What are you looking for?" />-->
    <?php if (!$invisible) : ?>
    <!-- dots pagination -->
    <ul id="panel-pagination">
        <?php $pageCount=0;?>
        <?php for($i=0; $i < $nPages; $i++) : ?>
            <li <?php if($i == 0) echo "class=\"current\"" ?> id="panel-pagination-<?php echo $pageCount;?>">.</li>
        <?php
            $pageCount+=1;
        endfor;
        ?>
    </ul>
    <?php endif; ?>
    <!-- prev button -->
    <a href="#" id="prev" <?php if ($invisible) echo "class=\"invisible\""; ?>><span class="arrow"></span></a>
    <ul id="panel-list">
        <?php
            $class = ""; $count = 0;
            // standard components that we have the icons ready
            //$std = array("com_banners", "com_contact", "com_messages", "com_newsfeeds", "com_redirect", "com_search", "com_weblinks");
            $std = array("com_banners", "com_contact", "com_messages", "com_newsfeeds", "com_redirect", "com_search");
            foreach ($items as $item) :
                $desc = substr(JText::_(''.strtoupper($item->title).'_XML_DESCRIPTION'), 0, 100);
                $descExists = strpos($desc, '_XML_DESCRIPTION');                
                $title = JText::_(''.strtoupper($item->title));
                if ($descExists !== false) $desc = JText::_('TPL_MINIMA_NODESCRIPTION');
                // if it's a standard extension, add the class to use the sprite img instead
                if (in_array(strtolower($item->element), $std)) {
                    // getting the component image class
                    $arrClass = explode(":", $item->img);
                    $class = "icon-48-".$arrClass[1];
                } else {
                	// component dev already specifies image path, so grab 48 px icon vs. 16 px
                	$img = str_replace('16', '48', $item->img);
                    // fallback if img not found
                   if (!file_exists($img)) $class = "icon-48-generic";
                }
        ?>
        <?php   if (!empty($class)): ?>
                <li>
                    <a href="<?php echo $item->link; ?>" class="<?php echo $class; ?>"><?php echo $title; ?>
                        <span class="extension-desc"><?php echo $desc; ?></span>
                    </a>
                </li>
        <?php else: ?>
                <li class="ext">
                    <img src="<?php echo $img; ?>" width="48" height="48" alt="<?php echo $title; ?>" />
                    <a href="<?php echo $item->link; ?>" class="<?php echo $class; ?>"><?php echo $title; ?>
                        <span class="extension-desc"><?php echo $desc; ?></span>
                    </a>
                </li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
    <!-- next button -->
    <a href="#" id="next" <?php if ($invisible) echo "class=\"invisible\""; ?>><span class="arrow"></span></a>
</div>

<div class="clr"></div>
