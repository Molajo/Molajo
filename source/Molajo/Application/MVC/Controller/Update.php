<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Application\MVC\Controller;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Update
 *
 * @package   Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class UpdateController extends Controller
{

    /**
     *  save
     */
    public function save()
    {
        /** Test */
        $this->id = 113;
        $valid = true;

        /** load data into model */
        $valid = $this->load();
        /**
        $this->id = 0;
        $this->model->id = 0;
        $this->model->row->id = 0;
         */
        $this->model->row->title = 'Puff it up.';
        /** filter input, validate values and foreign keys */
        if ($valid === true) {
            $valid = $this->filter_and_validate($this->model->row);
        }

        /** insert or update model data */
        if ($valid === true) {
            //$valid = $this->model->store();
        }

        /** redirect */
        if ($valid === true) {
            if ($this->model->row->id == 0
                || $this->model->row->status == 0
            ) {
                Services::Response()
                    ->redirect(
                    $this->task_request->get('redirect_on_success'),
                    301);
            } else {
                Services::Response()
                    ->redirect(
                    AssetHelper::getURL(
                        $this->task_request->get('request_asset_id')),
                    301
                );
            }
        } else {
            $link = $this->task_request->get('redirect_on_failure');
            if ((int)$this->id == 0) {
                $link .= '&task=add';
            } else {
                $link .= '&task=edit';
            }
            Services::Response()
                ->redirect($link, 301);
        }

        /**
        echo $action;
        die;


        $hash = Services::Security()->getHash(APPLICATION.get_class($this));

        $session = Services::Session()->create($hash);
        var_dump($session);
        echo 'back in app';
        die;*/
    }

    /**
     * load
     *
     * load existing data from model
     *
     * @return boolean
     * @since  1.0
     */
    public function load()
    {
        $valid = true;

        /** new or update? */
        $this->model->query->where($this->model->db->qn('id')
            . ' = ' . (int)$this->id);

        $results = $this->model->load();

        if (empty($results)) {
            $this->model->row = new \stdClass;
            $this->model->row->id = 0;
            $this->model->row->title = 'One long summer';
            $this->model->row->protected = 0;
            $this->model->row->asset_type_id = 10000;
            $this->model->row->checked_out_by = 0;
            $this->model->row->created_datetime = '2012-02-14';
            $this->model->row->created_by = 42;
            $this->model->row->extension_instance_id = 2;
            $this->model->row->modified_by = 42;

            return $valid;

        } else {

            try {
                $this->model->query = $this->model->db->getQuery(true);
                $this->model->id = $this->id;
                $this->model->query->where('id = ' . (int)$this->id);
                $this->model->row = $this->model->loadObject();

            } catch (Exception $e) {
                $valid = false;

                Services::Debug()->set('UpdateController::load Failed');
                Services::Debug()->set('Model: ' . $this->model->name . ' ID: ' . $this->id);

                Services::Message()
                    ->set(
                    $message =
                        Services::Language()->translate($e->getMessage()),
                    $type = MESSAGE_TYPE_ERROR
                );
            }
        }

        return $valid;
    }

    /**
     * filter_and_validate
     *
     * Runs custom validation methods
     *
     * @return  object
     * @since   1.0
     */
    protected function filter_and_validate()
    {
        $valid = true;

        $v = simplexml_load_file(
            APPLICATIONS_MVC
                . '/Model/Table/'
                . substr($this->model->table_name, 3, 99)
                . '.xml'
        );
        if (count($v) == 0) {
            return true;
        }

        /** filters and defaults */
        $userHTMLFilter = Services::Access()->setHTMLFilter();

        $valid = true;
        if (isset($v->filters->filter)) {
            foreach ($v->filters->filter as $f) {

                $name = (string)$f['name'];
                $dataType = (string)$f['filter'];
                $null = (string)$f['null'];
                $default = (string)$f['default'];

                if (isset($this->model->row->$name)) {
                    $value = $this->model->row->$name;
                } else {
                    $value = null;
                }

                if ($dataType == null) {
                    // no filter defined
                } else if ($dataType == 'html'
                    && $userHTMLFilter === false
                ) {
                    // user does not require HTML filtering

                } else {

                    try {
                        $value = Services::Security()->filter(
                            $value, $dataType, $null, $default);

                    } catch (Exception $e) {
                        $valid = false;
                        Services::Message()->set(
                            $message = Services::Language()->translate($e->getMessage()) . ' ' . $name,
                            $type = MESSAGE_TYPE_ERROR
                        );

                        Services::Debug()->set('UpdateController::filter_and_validate Filter Failed' . ' ' . $message);

                    }
                }
            }
        }

        Services::Debug()->set('UpdateController::filter_and_validate Filter::Success: ' . $valid);

        /** Helper Functions */
        if (isset($v->helpers->helper)) {
            foreach ($v->helpers->helper as $h) {

                $name = (string)$h['name'];

                try {
                    $this->validateHelperFunction($name);

                } catch (Exception $e) {
                    $valid = false;
                    Services::Message()->set(
                        $message = Services::Language()->translate($e->getMessage()) . ' ' . $name,
                        $type = MESSAGE_TYPE_ERROR
                    );

                    Services::Debug()->set('UpdateController::filter_and_validate Helper Failed' . ' ' . $message);

                }
            }
        }

        Services::Debug()->set('UpdateController::filter_and_validate Helper::Success: ' . $valid);

        /** Foreign Keys */
        if (isset($v->fks->fk)) {
            foreach ($v->fks->fk as $f) {

                $name = (string)$f['name'];
                $source_id = (string)$f['source_id'];
                $source_model = (string)$f['source_model'];
                $required = (string)$f['required'];
                $message = (string)$f['message'];

                try {
                    $this->validateForeignKey($name, $source_id,
                        $source_model, $required, $message);

                } catch (Exception $e) {
                    $valid = false;
                    Services::Message()->set(
                        $message = Services::Language()->translate($e->getMessage()) . ' ' . $name,
                        $type = MESSAGE_TYPE_ERROR
                    );

                    Services::Debug()->set('UpdateController::filter_and_validate FKs Failed' . ' ' . $message);
                }
            }
        }

        Services::Debug()->set('UpdateController::Validate FK::Success: ' . $valid);

        return $valid;
    }

    /**
     * validateHelperFunction
     *
     * @param $method
     *
     * @return  boolean
     * @since   1.0
     */
    protected function validateHelperFunction($method)
    {
        $helperClass = 'Molajo'
            . ucfirst(substr($this->model->table_name, 3, 999))
            . 'ModelHelper';

        if (class_exists($helperClass)) {
        } else {
            $helperClass = 'ModelHelper';
        }

        if (method_exists($helperClass, $method)) {
        } else {
            throw new \Exception('VALIDATE_HELPER_FUNCTION_NOT_FOUND');
        }

        $h = new $helperClass();

        $h->row = $this->model->row;

        $return = $h->$method();
        //get your helper class data back
        if ($return === false) {
            throw new \Exception('VALIDATE_HELPER_FUNCTION');
        }
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
     * @return  boolean
     * @since   1.0
     */
    protected function validateForeignKey($name, $source_id, $source_model,
                                          $required, $message)
    {

        Services::Debug()->set('UpdateController::validateForeignKey Field: ' . $name . ' Value: ' . $this->model->row->$name . ' Source: ' . $source_id . ' Model: ' . $source_model . ' Required: ' . $required);

        if ($this->model->row->$name == 0
            && $required == 0
        ) {
            return true;
        }

        if (isset($this->model->row->$name)) {

            $m = new $source_model ();

            $m->query->where($m->db->qn('id')
                . ' = ' . $m->db->q($this->model->row->$name));

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
     * @return  boolean  True on success.
     *
     * @return bool
     * @since   1.0
     */
    private function storeRelated()
    {
        $asset = new AssetModel();

        $asset->asset_type_id = $this->model->table_name->asset_type_id;

        $this->asset_id = $asset->save();

        $asset->load();
        if ($asset->getError()) {
            $this->setError($asset->getError());
            return false;
        }

        //
        // View Access
        //
        //		$grouping = Model::getInstance('Grouping');

        //       if ((int) $this->access == 0) {
        //            $asset->content_table = $this->model->table_name;
        //            $this->asset_id = $asset->save();
        //        } else {
        //            $asset->load();
        //        }

        //        if ($asset->getError()) {
        //            $this->setError($asset->getError());
        //            return false;
        //       }

        //        if ((int) $this->asset_id == 0) {
        //			$this->query = $this->db->getQuery(true);
        //			$this->query->update($this->db->qn($this->model->table_name));
        //			$this->query->set('asset_id = '.(int) $this->asset_id);
        //			$this->query->where($this->db->qn($k).' = '.(int) $this->$k);
        //			$this->db->setQuery($this->query->__toString());

        //			if ($this->db->query()) {
        //            } else {
        //				$e = new MolajoException(Services::Language()->sprintf('DB_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->db->getErrorMsg()));
        //				$this->setError($e);
        //				return false;
        //			}
        //        }
        return true;
    }
}
