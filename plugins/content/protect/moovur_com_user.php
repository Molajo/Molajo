<?php
/**
 * @version		$Id: moovur_com_user.php 551 2009-01-25 01:22:05Z ircmaxell $
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
class plgMoovurMoovur_Com_User extends MolajoPlugin
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
		if('com_user' != JRequest::getCMD('option')) return;
		$captcha = false;
		$return = '';
		switch(JRequest::getCMD('task')) {
			case 'requestreset':
				if($this->params->get('test_request_reset', false) || $this->params->get('display_request_reset', false)) {
					$captcha = true;
					$return = 'index.php?option=com_user&view=reset';
				}
				break;
			case 'confirmreset':
				if($this->params->get('test_confirm_reset', false) || $this->params->get('display_confirm_reset', false)) {
					$captcha = true;
					$return = 'index.php?option=com_user&view=reset&layout=confirm';
				}
				break;
			case 'login':
				if($this->params->get('test_login', false) || $this->params->get('display_login', false)) {
					$captcha = true;
					$return = 'index.php?option=com_user&view=login';
				}
				break;
			case 'remindusername':
				if($this->params->get('test_remind', false) || $this->params->get('display_remind', false)) {
					$captcha = true;
					$return = 'index.php?option=com_user&view=remind';
				}
				break;
			case 'register_save':
				if($this->params->get('test_registration', false) || $this->params->get('display_registration', false)) {
					$captcha = true;
					$return = 'index.php?option=com_user&task=register';
				}
				break;
		}

		if($captcha) {
			Moovur::checkCaptcha(JRoute::_($return));
		}
	}

	/*
	 * Perform the actual form validation. Based upon the action requered we
	 * will insert the Captcha.
	 */
	public function moovurValidateForm(&$form) {
		if(isset($form->vars['option']) && $form->vars['option'] == 'com_user') {
			if(isset($form->vars['task'])) {
				switch($form->vars['task']) {	
					case 'login':
						if($this->params->get('display_login', false)) {
							$form->full_html = preg_replace('#<input type="submit" name="Submit"#', Moovur::getCaptcha('com_user.login').'<input type="submit" name="Submit"', $form->full_html);
						}
						break;
					case 'register_save':
						if($this->params->get('display_registration', false)) {
							$form->full_html = preg_replace('#<button class="button validate" type="submit"#', Moovur::getCaptcha('com_user.register_save').'<button class="button validate" type="submit"', $form->full_html);
						}
						break;
					case 'remindusername':
						if($this->params->get('display_remind', false)) {
							$form->full_html = preg_replace('#<button type="submit"#', Moovur::getCaptcha('com_user.remindusername').'<button type="submit"', $form->full_html);
						}
						break;
					case 'requestreset':
						if($this->params->get('display_request_reset', false)) {
							$form->full_html = preg_replace('#<button type="submit"#', Moovur::getCaptcha('com_user.requestreset').'<button type="submit"', $form->full_html);
						}
						break;
					case 'confirmreset':
						if($this->params->get('display_confirm_reset', false)) {
							$form->full_html = preg_replace('#<button type="submit"#', Moovur::getCaptcha('com_user.confirmreset').'<button type="submit"', $form->full_html);
						}
						break;						
				}
			}
		}
	}
}
