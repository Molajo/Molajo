<?php
/**
 * @version     $id: view
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<form action="<?php echo MolajoRouteHelper::_('index.php?option=' . $this->request['option'] . '&view=' . $this->state->get('request.view')); ?>"
      method="post" name="adminForm" id="adminForm">