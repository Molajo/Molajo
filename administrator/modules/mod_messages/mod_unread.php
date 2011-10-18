<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'admin_inbox');
$wrap = $params->def('wrap', 'none');

require_once dirname(__FILE__).'/helper.php';

$this->rowset[0]->inboxClass = $unread ? 'unread-messages' : 'no-unread-messages';
$this->rowset[0]->unread = ModUnreadHelper::getCount();
if ($this->rowset[0]->unread > 0) {
	$this->rowset[0]->link = JRequest::getInt('hidemainmenu') ? null : MolajoRoute::_('index.php?option=com_messages');
    $this->rowset[0]->link = '< href="'.$this->rowset[0]->link.'">';
    $this->rowset[0]->linkend = '</a>';
} else {
    $this->rowset[0]->link = '';
    $this->rowset[0]->linkend = '';
}
