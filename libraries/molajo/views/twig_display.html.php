<?php
/**
 * @version     $id: MolajoViewTwig_display
 * @package     Molajo
 * @subpackage  Twig Display View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * View to Display Display Layouts with Twig
 *
 * @package	    Molajo
 * @subpackage	Twig Display View
 * @since	    1.0
 */
class MolajoViewTwig_display extends MolajoViewDisplay
{
    /**
     * @var $twig object
     */
    protected $twig;

    /**
     * display
     *
     * Display method for Twig Layouts
     */
    public function display($tpl = null)
    {
        /** Twig Autoload */
        $filehelper = new MolajoFileHelper();
        $filehelper->requireClassFile(JPATH_ROOT.'/libraries/Twig/Autoloader.php', 'Twig_Autoloader');
        Twig_Autoloader::register();

        /** @var $loader  */
        $loader = new Twig_Loader_Filesystem(MOLAJO_LAYOUTS);
        $this->twig = new Twig_Environment($loader, array(
          'cache' => MOLAJO_LAYOUTS.'/cache',
        ));

        /** Display View */
        parent::display();
    }
}