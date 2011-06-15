<?php
/**
 * @version		$Id: menu.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.base.tree');

/**
 * @package		Joomla.Administrator
 * @subpackage	mod_menu
 */
class RTAdminCssMenu extends JTree
{
	/**
	 * CSS string to add to document head
	 * @var string
	 */
	protected $_css = null;
	protected $_menudata = null;

	function __construct()
	{
		$this->_root = new JMenuNode('ROOT');
		$this->_current = & $this->_root;
		
		// menu data
		$menus['Articles'] = array('com_content','com_categories:extension=com_content','com_media');
		$menus['Menus'] = array('com_menus','com_trash:task=viewMenu');
		$menus['Users'] = array('com_users');
		$menus['Extend'] = array('com_installer','com_modules','com_redirect','com_messages','com_plugins','com_templates','com_languages','com_banners','com_categories:extension=com_banners','com_contact','com_categories:extension=com_contact','com_newsfeeds','com_categories:extension=com_newsfeeds','com_search','com_weblinks','com_categories:extension=com_weblinks');
		$menus['Config'] = array('com_config');
		$menus['Help'] = array('com_admin:task=help','com_admin:task=sysinfo');
		$menus['Tools'] = array('com_messages','com_massmail','com_checkin','com_cache');
		
		$this->_menudata = $menus;
	}


	function addSeparator()
	{
		$this->addChild(new JMenuNode(null, null, 'separator', false));
	}

	function renderMenu($id = 'menu', $class = '')
	{
		$depth = 1;

		if (!empty($id)) {
			$id='id="'.$id.'"';
		}

		if (!empty($class)) {
			$class='class="'.$class.'"';
		}

		/*
		 * Recurse through children if they exist
		 */
		while ($this->_current->hasChildren())
		{
			echo "<ul ".$id." ".$class.">\n";
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth);
			}
			echo "</ul>\n";
		}

		if ($this->_css) {
			// Add style to document head
			$doc = JFactory::getDocument();
			$doc->addStyleDeclaration($this->_css);
		}
	}

	function renderLevel($depth)
	{
		/*
		 * Build the CSS class suffix
		 */
		$class = 'li-'.RTAdminCSSMenu::cleanName($this->_current->title); 
		 
		if(strpos($this->_current->class,'separator')!==false) {
			$class .= ' separator';
		}

		if(strpos($this->_current->class,'disabled')!==false) {
			$class .= ' disabled';
		}
		
		if ($this->_current->hasChildren()) {
			$class .= ' parent';
			$this->_current->class.= ' daddy';
			
		}
		
		
		if ($depth == 1) {
			$class .= ' root';
			if (RTAdminCSSMenu::_isActive($this->_current->title))
				$class .= ' active';
		}
		
		$this->_current->class.= ' item';

		//increment depth 
		$depth++;
		
		/*
		 * Print the item
		 */
		echo '<li class="'.$class.'">';
		

		/*
		 * Print a link if it exists
		 */
		if ($this->_current->link != null && $this->_current->target != null) {
			echo "<a class=\"".$this->_current->class."\" href=\"".$this->_current->link."\" target=\"".$this->_current->target."\" >".$this->_current->title."</a>";
		} elseif ($this->_current->link != null && $this->_current->target == null) {
			echo "<a class=\"".$this->_current->class."\" href=\"".$this->_current->link."\">".$this->_current->title."</a>";
		} elseif ($this->_current->title != null) {
			echo "<span class=\"".$this->_current->class."\"><span>".$this->_current->title."</span></span>\n";
		} else {
			echo "<span></span>";
		}

		/*
		 * Recurse through children if they exist
		 */
		while ($this->_current->hasChildren())
		{
			if ($this->_current->class) {
				echo '<ul class="level'.$depth.' parent-'.RTAdminCSSMenu::cleanName($this->_current->title).'">'."\n";
			} else {
				echo '<ul>'."\n";
			}
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth);
			}
			echo "</ul>\n";
		}
		echo "</li>\n";
	}

	/**
	 * Method to get the CSS class name for an icon identifier or create one if
	 * a custom image path is passed as the identifier
	 *
	 * @access	public
	 * @param	string	$identifier	Icon identification string
	 * @return	string	CSS class name
	 * @since	1.5
	 */
	function getIconClass($identifier)
	{
		static $classes;

		// Initialise the known classes array if it does not exist
		if (!is_array($classes)) {
			$classes = array();
		}

		/*
		 * If we don't already know about the class... build it and mark it
		 * known so we don't have to build it again
		 */
		if (!isset($classes[$identifier])) {
			if (substr($identifier, 0, 6) == 'class:') {
				// We were passed a class name
				$class = substr($identifier, 6);
				$classes[$identifier] = "icon-16-$class";
			} else {
				if ($identifier == null) {
					return null;
				}
				// Build the CSS class for the icon
				$class = preg_replace('#\.[^.]*$#', '', basename($identifier));
				$class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);

				$this->_css  .= "\n.icon-16-$class {\n" .
						"\tbackground: url($identifier) no-repeat;\n" .
						"}\n";

				$classes[$identifier] = "icon-16-$class";
			}
		}
		return $classes[$identifier];
	}
	
	function _isActive($toplevel) {
	
		$option = JRequest::getString('option');
	
		$menus = $this->_menudata;
		
	
		
		switch ($toplevel) {

		
			case 'Dashboard':
				if ($option == 'com_cpanel') return true;
				break;
				
			case 'Articles':
				if (RTAdminCSSMenu::_isOption($menus['Articles'])) return true;
				break;
				
			case 'Menus':
				if (RTAdminCSSMenu::_isOption($menus['Menus'])) return true;
				break;
				
			case 'Users':
				if (RTAdminCSSMenu::_isOption($menus['Users'])) return true;
				break;
				
			case 'Extend':
				if (RTAdminCSSMenu::_isOption($menus['Extend'])) return true;
				break;
				
			case 'Configure':
				if (RTAdminCSSMenu::_isOption($menus['Config'])) return true;
				break;
		
			case 'Help':
				if (RTAdminCSSMenu::_isOption($menus['Help'])) return true;
				break;	
		
		}
		return false;
		
	}
	
	function _isOption($opts_array) {
	
		global $option;
		
		foreach ($opts_array as $opts) {
			$bits = explode(':',$opts);
			if (sizeof($bits) == 2) {
				$query = explode('=',$bits[1]);
				if ($option == $bits[0] && JRequest::getString($query[0]) == $query[1]) return true;
			} else {
				if ($option == $bits[0]) return true;
			}
		}
		return false;
	}
	
	function cleanName($name) {
		$name = strip_tags($name);
		$name = str_replace('*','',$name);
		$name = strtolower(strip_tags(str_replace(' ','-',$name)));
		$name = str_replace('/','-',$name);
		return $name;
	}
}

/**
 * @package		Joomla.Administrator
 * @subpackage	mod_menu
 */
class JMenuNode extends JNode
{
	/**
	 * Node Title
	 */
	public $title = null;

	/**
	 * Node Id
	 */
	public $id = null;

	/**
	 * Node Link
	 */
	public $link = null;

	/**
	 * Link Target
	 */
	public $target = null;

	/**
	 * CSS Class for node
	 */
	public $class = null;

	/**
	 * Active Node?
	 */
	public $active = false;

	public function __construct($title, $link = null, $class = null, $active = false, $target = null, $titleicon = null)
	{
		$this->title	= $titleicon ? $title.$titleicon : $title;
		$this->link		= JFilterOutput::ampReplace($link);
		$this->class	= $class;
		$this->active	= $active;
		$this->id		= str_replace(" ","-",$title);
		$this->target	= $target;
	}
}
