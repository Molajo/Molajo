<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2012 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
var_dump($this->setup);
?>
<div class="inner">
    <form action="<?php echo JUri::current() ?>" method="post">

        <input type="hidden" name="language" value="<?php echo $this->setup['language'] ?>">
        <input type="hidden" name="sitename" value="<?php echo $this->setup['sitename'] ?>">
        <input type="hidden" name="name" value="<?php echo $this->setup['name'] ?>">
        <input type="hidden" name="admin_email" value="<?php echo $this->setup['admin_email'] ?>">
        <input type="hidden" name="admin_password" value="<?php echo $this->setup['admin_password'] ?>">
        <input type="hidden" name="db_host" value="<?php echo $this->setup['hostname'] ?>">
        <input type="hidden" name="db_scheme" value="<?php echo $this->setup['db_scheme'] ?>">
        <input type="hidden" name="db_username" value="<?php echo $this->setup['db_username'] ?>">
        <input type="hidden" name="db_password" value="<?php echo $this->setup['db_password'] ?>">
        <input type="hidden" name="db_prefix" value="<?php echo $this->setup['db_prefix'] ?>">
        <input type="hidden" name="db_type" value="<?php echo $this->setup['db_type'] ?>">
        <input type="hidden" name="remove_tables" value="<?php echo $this->setup['remove_tables'] ?>">
        <input type="hidden" name="sample_data" value="<?php echo $this->setup['sample_data'] ?>">

        <h2><?php echo MolajoTextHelper::_('Welcome to the Molajo Installer'); ?></h2>

        <p><?php echo MolajoTextHelper::_('Before we get started, please ensure you have your database connection information handy, as you\'ll need it to complete the installation process.
        Contact your hosting provider if you do not know your database connection information.') ?></p>
        <ul id="system-check">
            <li id="language" class="valid">
                <span><?php echo MolajoTextHelper::sprintf('We have detected that your language is set to <strong class="%s">%s</strong>', $this->setup['language'], $this->languages[$this->setup['language']]) ?>
                    <a href="#" id="select-language"><span>Change Language</span></a>
                    <select name="language">
                        <?php foreach ($this->languages AS $code => $language): ?>
                        <option value="<?php echo $code ?>"<?php echo $this->setup['language'] == $code ? ' selected'
                                : ''; ?>><?php echo MolajoTextHelper::_($language) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <!--ul style="display:none;">
                        <li class="en-us"><a href="#">US English</a></li>
                        <li class="en-uk"><a href="#">UK English</a></li>
                        <li class="de-de"><a href="#">German</a></li>
                        <li class="it-it"><a href="#">Italian</a></li>
                        <li class="po-br"><a href="#">Brazilian Portuguese</a></li>
                    </ul-->
                </span>
            </li>
        </ul>
        <div id="actions">
            <!--a href="<?php echo JURI::base(); ?>index.php?option=installer&view=display&layout=step2" class="btn-primary"><strong>Ready?</strong> Lets get started! &raquo;</a
            -->
            <button type="submit" class="btn-primary" name="layout"
                    value="step2"><?php echo MolajoTextHelper::_('Ready? Lets get started! &raquo;') ?></button>
        </div>
    </form>
</div>
