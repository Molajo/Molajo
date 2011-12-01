<?php
/**
 * @package     Molajo
 * @subpackage  Molajito
 * @copyright   Copyright (C) 2011 Cristina Solana, Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$page = 'default';
$bodyElement = '<body>';
if (MolajoFactory::getConfig()->get('html5', true) === true): ?>
<!DOCTYPE html>
<?php else : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MolajoFactory::getLanguage()->getDefault(); ?>" lang="<?php echo MolajoFactory::getLanguage()->getDefault(); ?>" dir="<?php echo $this->direction; ?>" >
<head>
    <doc:include type="head" />
</head>
<?php
echo $bodyElement;
include dirname(__FILE__) . '/pages/' . $page . '/index.php';
?>
</body>
</html>