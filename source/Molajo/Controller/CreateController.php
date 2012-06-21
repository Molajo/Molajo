<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Controller;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Create
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class CreateController extends ModelController
{
    public function create()
    {
		/** tokens */


		/**
		 * Create - where is the data?
		 * 1. it can be passed in from a class, like the installer
		 * 2. it can be available on a form, like an editor
		 * getting it into the controller is the only difference, processing same
		 */

		/** verify ACL */
		$results = $this->checkPermissions();
		if ($results === false) {
			//error
			//return false (not yet)
		}

		/** filter input, validate values and foreign keys */
        $valid = $this->filterValidateInput();
        if ($valid === true) {
            $valid = $this->model->store();
        }

		/** Foreign Key Validation */

		/** Set Model Values */

        /** redirect */
        if ($valid === true) {
			if ($this->get('redirect_on_success', '') == '') {

			} else {
				Services::Redirect()->url
					= Services::Url()->getURL($this->get('redirect_on_success'));
				Services::Redirect()->code == 303;
			}

		} else {
			if ($this->get('redirect_on_success', '') == '') {

			} else {
				Services::Redirect()->url
					= Services::Url()->getURL($this->get('redirect_on_failure'));
				Services::Redirect()->code == 303;
			}
		}

		return $valid;
	}

	/**
	 * checkPermissions for Create
	 *
	 * @return bool
	 * @since  1.0
	 */
	protected function checkPermissions()
	{

		/**
		 * creating - need a catalog id - what are we creating?
		 * test 1: is there a primary category in the input?
		 * test 2: use the catalog_id for the component
		 * --> when creating an extension, the catalog type id for that extension is used
		 */

		/** Retrieve Extensions Catalog ID  */
		$m = new ModelController();

		/** Verify ACL for User to Create Extensions */
		$connect = $m->connect('Table', 'Extensions');
		if ($connect === false) {
			return false;
		}

		$m->set('name_key_value', 'Extensions');

		$results = $m->getData('item');
		if ($results === false) {
			//error
			return false;
		}

		$results = Services::Authorisation()->authoriseTask('Create', $results->catalog_id);
		if ($results === false) {
			//error
			//return false (not yet)
		}

		return true;
	}

    /**
     * filterValidateInput
     *
     * Runs custom validation methods
     *
     * @return object
     * @since   1.0
     */
    protected function filterValidateInput()
    {
		/** Model information */
		$table_registry_name = ucfirst(strtolower($this->get('model_type', '')))
			. ucfirst(strtolower($this->get('model_name', '')));

		$valid = true;

		return true;

        /** filters and defaults */
        $userHTMLFilter = Services::Authorisation()->setHTMLFilter();

        $valid = true;

        if (isset($v->filters->filter)) {

            foreach ($v->filters->filter as $f) {

                $name = (string) $f['name'];
                $dataType = (string) $f['filter'];
                $null = (string) $f['null'];
                $default = (string) $f['default'];

                if (isset($this->model->row->$name)) {
                    $value = $this->model->row->$name;
                } else {
                    $value = null;
                }

                if ($dataType == null) {
                    // no filter defined
                } elseif ($dataType == 'html' && $userHTMLFilter === false) {
                    // user does not require HTML filtering

                } else {

                    try {
                        $value = Services::Filter()->filter($value, $dataType, $null, $default);

                    } catch (Exception $e) {

                        $valid = false;
                        Services::Message()->set(
                            $message = Services::Language()->translate($e->getMessage()) . ' ' . $name,
                            $type = MESSAGE_TYPE_ERROR
                        );

                        Services::Debug()->set('CreateController::filterValidateInput Filter Failed' . ' ' . $message);
                    }
                }
            }
        }

        Services::Debug()->set('CreateController::filterValidateInput Filter::Success: ' . $valid);

        /** Helper Functions */
        if (isset($v->helpers->helper)) {

            foreach ($v->helpers->helper as $h) {

                $name = (string) $h['name'];

                try {
                    $this->validateHelperFunction($name);

                } catch (Exception $e) {

                    $valid = false;
                    Services::Message()->set(
                        $message = Services::Language()->translate($e->getMessage()) . ' ' . $name,
                        $type = MESSAGE_TYPE_ERROR
                    );

                    Services::Debug()->set('CreateController::filterValidateInput Helper Failed' . ' ' . $message);
                }
            }
        }

        Services::Debug()->set('CreateController::filterValidateInput Helper::Success: ' . $valid);

        /** Foreign Keys */
        if (isset($v->fks->fk)) {

            foreach ($v->fks->fk as $f) {

                $name = (string) $f['name'];
                $source_id = (string) $f['source_id'];
                $source_model = (string) $f['source_model'];
                $required = (string) $f['required'];
                $message = (string) $f['message'];

                try {
                    $this->validateForeignKey($name, $source_id, $source_model, $required, $message);

                } catch (Exception $e) {

                    $valid = false;
                    Services::Message()->set(
                        $message = Services::Language()->translate($e->getMessage()) . ' ' . $name,
                        $type = MESSAGE_TYPE_ERROR
                    );

                    Services::Debug()->set('CreateController::filterValidateInput FKs Failed' . ' ' . $message);
                }
            }
        }

        Services::Debug()->set('CreateController::Validate FK::Success: ' . $valid);

        return $valid;
    }

    /**
     * validateForeignKey
     *
     * @param $name
     * @param $source_id
     * @param $source_table
     * @param $required
     * @param $message
     *
     * @return boolean
     * @since   1.0
     */
    protected function validateForeignKey($name, $source_id, $source_model, $required, $message)
    {

        Services::Debug()->set(
            'CreateController::validateForeignKey Field: ' . $name
            . ' Value: ' . $this->model->row->$name
            . ' Source: ' . $source_id
            . ' Model: ' . $source_model
            . ' Required: ' . $required
        );

        if ($this->model->row->$name == 0 && $required == 0) {
            return true;
        }

        if (isset($this->model->row->$name)) {

            $m = new $source_model ();
            $m->query->where($m->db->qn('id') . ' = ' . $m->db->q($this->model->row->$name));
            $value = $m->loadResult();

            if (empty($value)) {
            } else {
                return true;
            }
        } else {
            if ($required == 0) {
                return true;
            }
        }

        throw new \Exception('VALIDATE_FOREIGN_KEY');
    }

    /**
     * storeRelated
     *
     * Method to store a row in the related table
     *
     * @return boolean True on success.
     *
     * @return bool
     * @since   1.0
     */
    private function storeRelated()
    {
        $catalog = new EntryModel('Catalog');

        $catalog->catalog_type_id = $this->model->table_name->catalog_type_id;

        $this->catalog_id = $catalog->save();

        $catalog->load();

        if ($catalog->getError()) {
            $this->setError($catalog->getError());

            return false;
        }

        return true;
    }
}
