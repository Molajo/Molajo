<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Application\MVC\Model\TableModel;

use Joomla\JFactory;

defined('MOLAJO') or die;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
Class ApplicationTest extends PHPUnit_Framework_TestCase
{

	/**
	 * JLoader is an abstract class of static functions and variables, so will test without instantiation
	 *
	 * @var    object
	 * @since  11.1
	 */
	protected $object;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function setUp()
	{

	}

	/**
	 * Tests the JLoader::discover method.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @covers  JLoader::discover
	 */
	public function testDiscover()
	{

	}


	/**
	 * Tests the JFactory::getACL method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  JFactory::getACL
	 */
	function testGetACL()
	{
		$this->assertInstanceOf(
			'JAccess',
			JFactory::getACL(),
			'Line: '.__LINE__
		);
	}

}
