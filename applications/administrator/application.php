<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Administrator
 *
 * Interacts with the Application Class for the Site Application
 *
 * @package		Molajo
 * @subpackage	Application
 * @since       1.0
 */
class MolajoAdministratorApplication extends MolajoApplication
{
    /**
     * __construct
     *
	 * @since  1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
    }

    /**
     * initialise
     *
	 * @since  1.0
	 */
	public function initialise($config = array())
	{
 		parent::initialise($config);
    }

	/**
     * route
     *
     * @since 1.0
     */
	public function route()
	{
		parent::route();
	}

	/**
     * getRouter
     *
	 * @since	1.0
	 */
	static public function getRouter($name = null, array $options = array())
	{
		parent::getRouter();
	}

	/**
     * dispatch
     *
	 * @since	1.0
	 */
	public function dispatch($component = null)
	{
		parent::dispatch ($component);
	}

	/**
     * render
     *
	 * Display the application.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function render()
	{
       parent::render();
	}

	/**
	 * getTemplate
     *
     * Get the template
	 *
	 * @return	string	The template name
	 * @since	1.0
	 */
	public function getTemplate($params = false)
	{
		static $template;

		if (isset($template)) {
        } else {
			$admin_style = MolajoFactory::getUser()->getParam('admin_style');

			// Load the template name from the database
			$db = MolajoFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('a.title as template, b.params as params');
			$query->from('#__templates as a');
			$query->from('#__template_styles as b');
			$query->where('a.application_id = '. (int) MOLAJO_APPLICATION_ID);
			$query->where('a.id = b.template_id');

			if ($admin_style) {
				$query->where('id = '.(int) $admin_style);
			} else{
				$query->where('`default` = 1');
			}

			$db->setQuery($query->__toString());
			$template = $db->loadObject();

			$template->template = JFilterInput::getInstance()->clean($template->template, 'cmd');
			$template->params = new JRegistry($template->params);

			if (file_exists(MOLAJO_EXTENSION_TEMPLATES.'/'.$template->template.'/'.'index.php')) {
            } else {
				$template->params = new JRegistry();
				$template->template = MOLAJO_APPLICATION_DEFAULT_TEMPLATE;
			}
		}
		if ($params) {
			return $template;
		}

		return $template->template;
	}
}