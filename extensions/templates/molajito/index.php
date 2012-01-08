<?php
/**
 * @package     Molajo
 * @subpackage  Molajito
 * @copyright   Copyright (C) 2012 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$bodyElement = '<body>';
if ($this->parameters->get('html5', true) === true): ?>
<!DOCTYPE html>
    <?php else : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MolajoController::getApplication()->getLanguage()->getDefault(); ?>"
      lang="<?php echo MolajoController::getApplication()->getLanguage()->getDefault(); ?>" dir="<?php echo $this->direction; ?>">
<head>
    <include:head />
</head>
<?php
echo $bodyElement;
echo MolajoController::getApplication()->escapeOutput('<h1>example</h1>');
include $this->parameters->get('page');
?>
</body>
</html>