<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

// Joomla 1.5 only

?>

<section class="edit weblink<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">

    <?php if ($this->params->get('show_page_title', 1)) : ?>
    <h2>
        <?php echo $this->escape($this->params->get('page_title')) ?>
    </h2>
    <?php endif; ?>
    <script type="text/javascript">
        //<![CDATA[
        function submitbutton(pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'cancel') {
                submitform(pressbutton);
                return;
            }

            // do field validation
            if (document.getElementById('jformtitle').value == "") {
                alert("<?php echo JText::_('Weblink item must have a title', true); ?>");
            } else if (document.getElementById('jformcatid').value < 1) {
                alert("<?php echo JText::_('You must select a category.', true); ?>");
            } else if (document.getElementById('jformurl').value == "") {
                alert("<?php echo JText::_('You must have a url.', true); ?>");
            } else {
                submitform(pressbutton);
            }
        }
        //]]>
    </script>

    <form action="<?php echo $this->action ?>" method="post" name="adminForm" class="editor" id="adminForm">
        <fieldset class="publishing">
            <legend><?php echo JText::_('Submit A Web Link');?></legend>

            <label for="jformtitle">
                <?php echo JText::_('Name'); ?>:
                <input class="inputbox" type="text" id="jformtitle" name="jform[title]" size="50" maxlength="250"
                       value="<?php echo $this->escape($this->weblink->title);?>">
            </label>

            <label for="jformcatid">
                <?php echo JText::_('Category'); ?>:
                <?php echo $this->lists['catid']; ?>
            </label>

            <label for="jformurl">
                <?php echo JText::_('URL'); ?>:
                <input class="inputbox" type="url" id="jformurl" name="jform[url]"
                       value="<?php echo $this->escape($this->weblink->url); ?>" size="50" maxlength="250">
            </label>

            <label for="jformdescription">
                <?php echo JText::_('Description'); ?>:
                <textarea class="inputbox" cols="30" rows="6" id="jformdescription" name="jform[description]"
                          style="width:300px">
                    <?php echo htmlspecialchars($this->weblink->description, ENT_QUOTES);?>
                </textarea>
            </label>
        </fieldset>

        <fieldset>
            <legend><?php echo JText::_('Published');?></legend>
            <label for="jformpublished">
                <?php echo JText::_('Published'); ?>:
                <?php echo $this->lists['published']; ?>
            </label>

            <label for="jformordering">
                <?php echo JText::_('Ordering'); ?>:
                <?php echo $this->lists['ordering']; ?>
            </label>

        </fieldset>

        <button type="button" onclick="submitbutton('save')">
            <?php echo JText::_('Save') ?>
        </button>
        <button type="button" onclick="submitbutton('cancel')">
            <?php echo JText::_('Cancel') ?>
        </button>

        <input type="hidden" name="jform[id]" value="<?php echo (int)$this->weblink->id; ?>">
        <input type="hidden" name="jform[ordering]" value="<?php echo (int)$this->weblink->ordering; ?>">
        <input type="hidden" name="jform[approved]" value="<?php echo $this->weblink->approved; ?>">
        <input type="hidden" name="option" value="com_weblinks">
        <input type="hidden" name="controller" value="weblink">
        <input type="hidden" name="task" value="">
        <?php echo JHTML::_('form.token'); ?>
    </form>
</section>