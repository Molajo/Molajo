<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */

defined('MOLAJO') or die;

$pageURL = Services::Registry()->get('Plugindata', 'page_url');

$buttonTitle  = str_replace(' ', '&nbsp;', htmlentities('Edit', ENT_COMPAT, 'UTF-8'));
$buttonArray  = 'button_id:enable,button_tag:button,button_icon_prepend:icon-edit,button_title:' . $buttonTitle . ',' . 'button_type:secondary,' . ',' . 'button_link:' . $pageURL . '/edit';
$buttonArray1 = '{{' . trim($buttonArray) . '}}';

$buttonTitle = str_replace(' ', '&nbsp;', htmlentities('Delete', ENT_COMPAT, 'UTF-8'));
$linkURL = $pageURL;
$buttonArray = 'button_tag:button,button_icon_prepend:icon-trash,button_title:' . $buttonTitle . ',' . 'button_type:alert,' . 'button_link:' . $pageURL;
$buttonArray2 = '{{' . trim($buttonArray) . '}}';
?>
<include:ui name=button-group button_group_shape=radius class=listbox button_group_array=<?php echo trim($buttonArray1) . trim($buttonArray2); ?>/>

<p id="modified"></p>
