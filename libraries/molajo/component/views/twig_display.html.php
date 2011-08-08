<?php
/**
 * @package     Molajo
 * @subpackage  Display View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Twig Display View
 *
 * @package	    Molajo
 * @subpackage	View
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
        $filehelper->requireClassFile(MOLAJO_PATH_ROOT.'/libraries/Twig/Autoloader.php', 'Twig_Autoloader');
        Twig_Autoloader::register();

        /** @var $loader  */
        $loader = new Twig_Loader_Filesystem(MOLAJO_LAYOUTS_EXTENSIONS);
        $this->twig = new Twig_Environment($loader, array(
          'cache' => MOLAJO_LAYOUTS_EXTENSIONS.'/cache',
        ));

        /** Display View */
        parent::display();
    }
}