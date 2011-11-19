<?php defined('_JEXEC') or die;
/**
 * @version		$Id: default_articles.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Joomla 1.6 only

?>

<?php if ($this->params->get('show_articles')) : ?>
    <section class="contact-articles">
	    <ol>
		    <?php foreach ($this->item->articles as $article) :	?>
			    <li>
			    <?php $link = JRoute::_('index.php?option=com_content&view=article&id='.$article->id); ?>
			    <?php echo '<a href="'.$link.'">' ?>
				    <?php echo $article->text = htmlspecialchars($article->title, ENT_COMPAT, 'UTF-8'); ?>
				    </a>
			    </li>
		    <?php endforeach; ?>
	    </ol>
    </section>
<?php endif;
