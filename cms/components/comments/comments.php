<?php
/**
 * @package     Molajo
 * @subpackage  Entry point
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$current_folder = basename(dirname(__FILE__));
require_once MOLAJO_MVC . '/entry.php';

class ArticlesController extends MolajoController
{
}

class ArticlesControllerEdit extends MolajoControllerEdit
{
}

class ArticlesControllerMultiple extends MolajoControllerMultiple
{
}

class ArticlesViewDisplay extends MolajoView
{
}

class ArticlesViewEdit extends MolajoViewEdit
{
}

class ArticlesTableArticle extends MolajoTableContent
{
}

class ArticlesModelDisplay extends MolajoModelDisplay
{
}

class ArticlesModelEdit extends MolajoModelEdit
{
}

class MolajoACLArticles extends MolajoACL
{
}

class ArticlesHelper
{
}
