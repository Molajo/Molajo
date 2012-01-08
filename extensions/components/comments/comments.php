<?php
/**
 * @package     Molajo
 * @subpackage  Entry point
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$current_folder = basename(dirname(__FILE__));
require_once MOLAJO_APPLICATIONS_MVC . '/entry.php';

class CommentsController extends MolajoControllerDisplay
{
}
class CommentsControllerEdit extends MolajoControllerEdit
{
}
class CommentsControllerMultiple extends MolajoControllerMultiple
{
}
class CommentsTableComment extends MolajoTableContent
{
}
class CommentsModelDisplay extends MolajoModelDisplay
{
}
class CommentsModelEdit extends MolajoModelEdit
{
}
class CommentsMolajoACL extends MolajoACL
{
}
class CommentsHelper
{
}
