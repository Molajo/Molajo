<?php
/**
 * @version		$Id: moovur_com_contact.php 955 2009-02-05 20:49:02Z ircmaxell $
 * @package		Moovum
 * @subpackage	Moovur
 * @copyright	Copyright 2009 Moovum. All rights reserved.
 * @license		GNU General Public License Version 2
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Moovur ComUser plugin
 *
 * @package		Moovur Plugins 
 * @subpackage	com_user
 */
class plgMoovurMoovur_Com_Contact extends MolajoPlugin
{
	/*
	 * Constructor
	 */
	public function __construct(&$subject, $options = array()) {
		parent::__construct($subject, $options);
	}

	/*
	 * Check response for user plugin. Within this method we include all
	 * possible actions and handle them if active.
	 */
	public function moovurCheckResponse() {
		if('com_contact' != JRequest::getCMD('option')) return;
		if('submit' == JRequest::getCMD('task')) {
			if($this->params->get('check_contact')) {
				$obj = new stdclass;
				$obj->text = JRequest::getVar('text');
				$obj->author_name = JRequest::getVar('name');
				$obj->author_email = JRequest::getVar('email');
				$obj->title = JRequest::getVar('subject');
				Moovur::checkContent($obj, 'com_contact', 'submit');
			} elseif($this->params->get('test_contact') || $this->params->get('display_contact')) {
				$return = 'index.php?option=com_contact&view=contact&id='.JRequest::getInt('id');
				Moovur::checkCaptcha('com_contact.submit', JRoute::_($return));
			}
		}
	}

	/*
	 * Perform the actual form validation. Based upon the action requered we
	 * will insert the Captcha.
	 */
	public function moovurValidateForm(&$form) {
		if(isset($form->vars['option']) && $form->vars['option'] == 'com_contact') {
			if(isset($form->vars['task'])) {
				if($form->vars['task'] == 'submit') {
					if($this->params->get('display_contact', false) && !$this->params->get('check_contact', false) ) {
						$form->full_html = preg_replace('#<button class="button validate" type="submit#', Moovur::getCaptcha('com_contact.submit').'<button class="button validate" type="submit', $form->full_html);
					}
				}
			}
		}
	}

}
