<?php
/**
 * @package     Molajo
 * @subpackage  Molajito
 * @copyright   Copyright (C) 2011 Cristina Solana, Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
// $page = MolajoFactory::getDocument()->page;
// MolajoFactory::getDocument()->html5
$page = 'default';
$bodyElement = '<body>';
$docType = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
$docType = '<!DOCTYPE html>';
echo $docType;
?>
<html xmlns="http://www.w3.org/1999/xhtml"
      xml:lang="<?php echo $this->language; ?>"
      lang="<?php echo $this->language; ?>"
      dir="<?php echo $this->direction; ?>" >
<head>
    <doc:include type="head" />
</head>
<?php
echo $bodyElement;
include dirname(__FILE__).'/page/'.$page.'/index.php';
?>
</body>
</html>