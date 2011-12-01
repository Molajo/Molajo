<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if (substr(JVERSION, 0, 3) >= '1.6') {
    //Joomla 1.6+

    JHtml::_('behavior.keepalive');
    JHtml::_('behavior.formvalidation');
    JHtml::_('behavior.tooltip');
    if (isset($this->error)) : ?>
    <div class="contact-error">
        <?php echo $this->error; ?>
    </div>
    <?php endif; ?>

<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate contact-form">
    <fieldset>
        <legend><?php echo JText::_('CONTACT_FORM_LABEL'); ?></legend>
        <dl>
            <dt><?php echo $this->form->getLabel('contact_name'); ?></dt>
            <dd><?php echo $this->form->getInput('contact_name'); ?></dd>
            <dt><?php echo $this->form->getLabel('contact_email'); ?></dt>
            <dd><?php echo $this->form->getInput('contact_email'); ?></dd>
            <dt><?php echo $this->form->getLabel('contact_subject'); ?></dt>
            <dd><?php echo $this->form->getInput('contact_subject'); ?></dd>
            <dt><?php echo $this->form->getLabel('contact_message'); ?></dt>
            <dd><?php echo $this->form->getInput('contact_message'); ?></dd>
            <?php     if ($this->params->get('show_email_copy')) { ?>
            <dt><?php echo $this->form->getLabel('contact_email_copy'); ?></dt>
            <dd><?php echo $this->form->getInput('contact_email_copy'); ?></dd>
            <?php } ?>
            <?php //Dynamically load any additional fields from plugins. ?>
            <?php foreach ($this->form->getFieldsets() as $fieldset): ?>
            <?php if ($fieldset->name != 'contact'): ?>
                <?php $fields = $this->form->getFieldset($fieldset->name); ?>
                <?php foreach ($fields as $field): ?>
                    <?php if ($field->hidden): ?>
                        <?php echo $field->input; ?>
                        <?php else: ?>
                        <dt>
                            <?php echo $field->label; ?>
                            <?php if (!$field->required && $field->type != "Spacer"): ?>
                            <span class="optional"><?php echo JText::_('CONTACT_OPTIONAL');?></span>
                            <?php endif; ?>
                        </dt>
                        <dd><?php echo $field->input;?></dd>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif ?>
            <?php endforeach;?>
            <dt></dt>
            <dd>
                <button class="button validate" type="submit"><?php echo JText::_('CONTACT_CONTACT_SEND'); ?></button>
                <input type="hidden" name="option" value="com_contact"/>
                <input type="hidden" name="task" value="contact.submit"/>
                <input type="hidden" name="return" value="<?php echo $this->return_page;?>"/>
                <input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>"/>
                <?php echo JHtml::_('form.token'); ?>
            </dd>
        </dl>
    </fieldset>
</form>

<?php

}
else {
    // Joomla 1.5
    ?>

<script type="text/javascript">
    function validateForm(frm) {
        var valid = document.formvalidator.isValid(frm);
        if (valid == false) {
            // do field validation
            if (frm.email.invalid) {
                alert("<?php echo JText::_('Please enter a valid e-mail address.', true);?>");
            } else if (frm.text.invalid) {
                alert("<?php echo JText::_('CONTACT_FORM_NC', true); ?>");
            }
            return false;
        } else {
            frm.submit();
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php'); ?>" class="form-validate contact-form" method="post" name="emailForm"
      id="emailForm">
    <fieldset class="contact-email">

        <label for="contact-formname">
            <?php echo JText::_('Enter your name'); ?>:
            <input type="text" name="name" id="contact-formname" size="30" class="inputbox" value=""/>
        </label>

        <label id="contact-emailmsg" for="contact-email">
            <?php echo JText::_('Email address'); ?>*:
            <input type="text" id="contact-email" name="email" size="30" value=""
                   class="inputbox required validate-email" maxlength="100"/>
        </label>

        <label for="contact-subject">
            <?php echo JText::_('Message subject'); ?>:
            <input type="text" name="subject" id="contact-subject" size="30" class="inputbox" value=""/>
        </label>

        <label id="contact-textmsg" for="contact-text" class="textarea">
            <?php echo JText::_('Enter your message'); ?>*:
            <textarea name="text" id="contact-text" class="inputbox required" rows="10" cols="50"></textarea>
        </label>

        <?php if ($this->contact->params->get('show_email_copy')): ?>

        <label for="contact-email-copy" class="copy">
            <?php echo JText::_('EMAIL_A_COPY'); ?>
            <input type="checkbox" name="email_copy" id="contact-email-copy" value="1"/>
        </label>

        <?php endif; ?>

        <button class="button validate" type="submit"><?php echo JText::_('Send'); ?></button>

    </fieldset>
    <input type="hidden" name="option" value="com_contact"/>
    <input type="hidden" name="view" value="contact"/>
    <input type="hidden" name="id" value="<?php echo (int)$this->contact->id; ?>"/>
    <input type="hidden" name="task" value="submit"/>
    <?php echo JHTML::_('form.token'); ?>
</form>

<?php }
