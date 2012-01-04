<?php
/**
 * @version        $Id: default_message.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * @since        1.5
 */

// no direct access
defined('_JEXEC') or die;

$state = $this->get('State');
$message1 = $state->get('message');
$message2 = $state->get('extension_message');
?>
<table class="adminform">
    <tbody>
    <?php if ($message1) : ?>
    <tr>
        <th><?php echo $message1 ?></th>
    </tr>
        <?php endif; ?>
    <?php if ($message2) : ?>
    <tr>
        <td><?php echo $message2; ?></td>
    </tr>
        <?php endif; ?>
    </tbody>
</table>