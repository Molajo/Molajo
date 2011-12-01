<?php
/**
 * @version     $id: syntaxhighlighter.php
 * @package     Molajo
 * @subpackage  Responses Component
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
if ($evenodd == 'even') {
    $evenodd = 'odd';
} else {
    $evenodd = 'even';
} ?>

<div class="clear"></div>
<pre class="brush: <?php echo htmlspecialchars($this->languageAlias) . $this->parameters; ?> <?php echo 'synhi' . $evenodd; ?>"
     id="<?php echo 'synhi' . $this->unique; ?>">
        <?php echo $this->code; ?>
    </pre>
<div class="clear"></div>
