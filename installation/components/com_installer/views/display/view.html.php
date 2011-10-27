<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2011 Babs Gösgens. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display View
 *
 * @package	    Molajo
 * @subpackage	View
 * @since	    1.0
 */
class InstallerViewDisplay extends MolajoView
{

    /**
     * Tdddddd
     *
     * @var    ddd
     * @since  1.0
     */
    protected $system_checks = null;
    protected $form_fields = null;
    protected $form_edits = null;

    /**
     * display
     *
     * @return void
     */
	public function display($tpl = null)
	{
        /** check layout */

        if (JRequest::getCmd('layout', 'installer_step1') == 'installer_step1') {
            $this->system_checks = $this->get('SystemChecks');
            //LanguageList
            //UserLanguage
        }

        $this->form_fields = $this->get('FormFields');

        $this->$form_edits = $this->get('FormEdits');


        /** load unused fields into hidden form fields for display */

        parent::display($tpl);
    }

}
