<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

/**
 * Tests for Molajo Class
 *
 * @package     Molajo
 * @subpackage  Testing
 * @since       1.0
 */
class Molajo
{

	/**
	 * Molajo::Application
	 *
	 * @var    object Application
	 * @since  1.0
	 */
	protected static $application = null;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function setUp()
	{

	}

	/**
	 * Tests the Molajo::Application method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 * @covers  Molajo::Application
	 */
	function testApplication()
	{
		$this->assertInstanceOf(
			'Applicationc',
			Molajo::Application(),
			'Line: '.__LINE__
		);

		$this->markTestIncomplete(
			'This test has not been implemented completely yet.'
		);
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	function tearDown()
	{

	}
}
