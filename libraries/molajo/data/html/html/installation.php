<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class MolajoHtmlInstallation
{
    public static function stepbar()
    {
        $view = JRequest::getWord('view');
        switch ($view) {
            case '':
            case 'language':
                $on = 1;
                break;
            case 'preinstall':
                $on = 2;
                break;
            case 'license':
                $on = 3;
                break;
            case 'database':
                $on = 4;
                break;
            case 'filesystem':
                $on = 5;
                break;
            case 'site':
                $on = 6;
                break;
            case 'complete':
                $on = 7;
                break;
            case 'remove':
                $on = 7;
                break;
            default:
                $on = 1;
        }

        $html = '<h1>'.MolajoText::_('INSTL_STEPS_TITLE').'</h1>' .
                '<div class="step'.($on == 1 ? ' active'
                : '').'" id="language">'.MolajoText::_('INSTL_STEP_1_LABEL').'</div>' .
                '<div class="step'.($on == 2 ? ' active'
                : '').'" id="preinstall">'.MolajoText::_('INSTL_STEP_2_LABEL').'</div>' .
                '<div class="step'.($on == 3 ? ' active'
                : '').'" id="license">'.MolajoText::_('INSTL_STEP_3_LABEL').'</div>' .
                '<div class="step'.($on == 4 ? ' active'
                : '').'" id="database">'.MolajoText::_('INSTL_STEP_4_LABEL').'</div>' .
                '<div class="step'.($on == 5 ? ' active'
                : '').'" id="filesystem">'.MolajoText::_('INSTL_STEP_5_LABEL').'</div>' .
                '<div class="step'.($on == 6 ? ' active'
                : '').'" id="site">'.MolajoText::_('INSTL_STEP_6_LABEL').'</div>' .
                '<div class="step'.($on == 7 ? ' active'
                : '').'" id="complete">'.MolajoText::_('INSTL_STEP_7_LABEL').'</div>';
        return $html;
    }
}
