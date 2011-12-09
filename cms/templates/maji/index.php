<?php
/**
 * @package     Molajo
 * @subpackage  Maji
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MOLAJO_BASE_FOLDER; ?>"
      lang="<?php echo MolajoFactory::getLanguage()->language; ?>"
      dir="<?php echo MolajoFactory::getLanguage()->direction; ?>">
<head>
    <doc:include type="head"/>
</head>
<body>
<?php
$here = dirname(__FILE__);
MolajoForm::addFormPath($here);
MolajoForm::addFieldPath($here);

//$form = JForm::getInstance('com_installer.manage', 'manage', array('load_data' => $loadData));
$form = MolajoForm::getInstance('com_dashboard.dashboard', 'dashboard', array('load_data' => true));
echo '<pre>';var_dump($form);'</pre>';

?>
</body>
</html>