<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Version;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Item Author
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class VersionPlugin extends Plugin
{

    /**
     * Pre-create processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        $field = $this->getField('version');
        $name = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
) {
            $newFieldValue = 1;
            $this->saveField($field, $name, $newFieldValue);
        }

        $field = $this->getField('version_of_id');
        $name = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
) {
            $newFieldValue = 0;
            $this->saveField($field, $name, $newFieldValue);
        }

        $field = $this->getField('status_prior_to_version');
        $name = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
) {
            $newFieldValue = 0;
            $this->saveField($field, $name, $newFieldValue);
        }

        return true;
    }

    /**
     * Pre-update processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        $field = $this->getField('version');
        $name = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
) {
            $newFieldValue = 1 + 1;
            $this->saveField($field, $name, $newFieldValue);
        }

        $field = $this->getField('status_prior_to_version');
        $name = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
) {
            $newFieldValue = $this->row->status;
            $this->saveField($field, $name, $newFieldValue);
        }

        return true;
    }

    /**
     * Post-update processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        return true;
    }

    /**
     * createVersion
     *
     * Automatic version management save and restore processes for resources
     *
     * @return boolean
     * @since   1.0
     */
    public function createVersion()
    {
        if ($this->get('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** create **/
        if ((int) $this->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->get('action') == 'delete'
            && $this->get('retain_versions_after_delete', 1) == 0
        ) {
            return true;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->get('id'));

        /** error processing **/
        if ($versionKey === false) {
            // redirect error
            return false;
        }

        /** Plugin_Event: onContentCreateVersion
         **/

        return true;
    }

    /**
     * maintainVersionCount
     *
     * Prune version history, if necessary
     *
     * @return boolean
     */
    public function maintainVersionCount()
    {
        if ($this->get('version_management', 1) == 1) {
        } else {
            return true;
        }

        /** no versions to delete for create **/
        if ((int) $this->get('id') == 0) {
            return true;
        }

        /** versions deleted with delete **/
        if ($this->get('action') == 'delete'
            && $this->get('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->get('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount($this->get('id'), $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            // redirect false
            return false;
        }

        /** Plugin_Event: onContentMaintainVersions
         **/

        return true;
    }
}
