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
    protected $form_fields   = null;
    protected $form_edits    = null;

    /**
     * display
     *
     * @return void
     */
	public function display($tpl = null)
	{
        $helper = $this->loadHelper('installer');

        /** check layout */
        $layout = JRequest::getCmd('next_step', 'step1');
        
        if ($layout == 'step1') {
        }
        else if($layout == 'step2') {

        }
        else if($layout == 'step3') {

        }
        else if($layout == 'step4') {
        }

        $this->form_fields = $this->get('FormFields');

        $this->form_edits = $this->get('FormEdits');

        // We want to enable single page (or however many steps) so we need to assign these to any layout
        $this->assign('setup',     $this->getModel()->getSetup());
        $this->assign('languages', $this->getModel()->getLanguageList());

        /** load unused fields into hidden form fields for display */

        parent::display($layout);
    }

}
