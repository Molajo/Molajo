<?php
/**
 * @package     Molajo
 * @subpackage  Molajito
 * @copyright   Copyright (C) 2012 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<include:head />
<?php
$bodyElement = '<body>';
echo $bodyElement;
echo MolajoController::getApplication()->escapeOutput('<h1>example</h1>');
include $this->parameters->get('page');
?>
</body>
</html>