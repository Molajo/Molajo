<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Csrftoken;

use Molajo\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Csrftoken
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CsrftokenPlugin extends ContentPlugin
{

	/**
	 * After View Rendering, look for </form> Statement, if found, add CSRF Protection
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterViewRender()
	{
		$rendered = $this->get('rendered_output');

		if ($rendered === null
			|| trim($rendered) == ''
		) {
			return true;
		}

		$beginFormPattern = '/<form.+?name="(.+?)"\>/';

		preg_match_all($beginFormPattern, $rendered, $forms);

		if (count($forms[0]) == 0) {
			return true;
		}

		$replaceThis = array();
		$withThis = array();
		$i = 0;
		foreach ($forms[0] as $match) {
			$formDefinition = $match;

			$formName = $forms[1][$i];

			$formToken = md5(rand(rand(50000, 500000), rand(500000, 50000000)));

			$replaceThis[] = $formDefinition;
			$withThis[] = $formDefinition . chr(10)
				. '<input type="hidden" name="' . $formName . '[_csfr_token]" id="' . $formName . '__csfr_token" value="' . $formToken . '" />';
			$i++;
		}

		$temp = str_replace($replaceThis, $withThis, $rendered);

		if ($rendered == $temp) {
			return true;
		}

		$this->set('rendered_output', $temp);

		return true;
	}

	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		//unique
		return true;
	}

	/**
	 * Pre-update processing
	 *
	 * @param   $this->data
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeUpdate()
	{
		//reserved words - /edit
		return true;
	}
}
