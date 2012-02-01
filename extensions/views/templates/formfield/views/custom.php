<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

defined('MOLAJO') or die; ?>
<include:formfield
    type=radio
    name=size
    values=(foo bar), (12 true)
/>

<form>
 <p><label>Customer name: <input></label></p>
 <fieldset>
  <legend> Pizza Size </legend>
  <p><label> <input type=radio name=size> Small </label></p>
  <p><label> <input type=radio name=size> Medium </label></p>
  <p><label> <input type=radio name=size> Large </label></p>
 </fieldset>
</form>

