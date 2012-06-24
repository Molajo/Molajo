<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Create;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Controller\CreateController;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Extension Instances
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CreateTrigger extends ContentTrigger
{

	/**
	 * Post-create processing for new extension
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterCreate()
	{

		if ($this->query_results->catalog_type_id >= CATALOG_TYPE_EXTENSION_BEGIN
			AND $this->query_results->catalog_type_id <= CATALOG_TYPE_EXTENSION_END
		) {
		} else {
			return true;
		}

		$results = Services::Text()->getPlaceHolderText(4, 20, 'html', 1);

		$results = Services::Text()->addImage(200, 300, 'cat');
		echo $results;
		die;
//$results = Services::Install()->extension('Test3', 'Templates', $source_path = null, $destination_path = null);
		die;
		//
		//echo $results;
		//die;

		/** Extension Instance ID */
		$id = $this->query_results->id;
		$catalog_type_id = $this->query_results->catalog_type_id;


		if ($this->parameters->create_extension == true) {
			//copy files
		}

		if ($this->parameters->create_sample_content == true) {
			//create content
		}

		//add admin menu items
		//gridtype

		if ((int) $id == 0) {
			return false;
		}


		return true;
	}

}
