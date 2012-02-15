<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Content
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoContentModel extends MolajoDisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table = Services::Configuration()->get('dbprefix').'_content';
        $this->primary_key = 'id';

        return parent::__construct($id);
    }

    /**
     * getItems
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $this->setCriteria();
        $this->_query();
    }

    public function setCriteria ()
    {
        /** Set State for Selection Criteria */
        $asset_type_id = $this->task_request->get('source_asset_type_id');
        $this->set('crud', 'r');
    }

    protected function _query()
    {
        /** query  */
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();
        $asset_type_id = $this->task_request->get('source_asset_type_id');

        $query->select('a.' . $db->namequote('id'));
        $query->select('a.' . $db->namequote('extension_instance_id'));
        $query->select('a.' . $db->namequote('asset_type_id'));
        $query->select('a.' . $db->namequote('title'));
        $query->select('a.' . $db->namequote('subtitle'));
        $query->select('a.' . $db->namequote('path'));
        $query->select('a.' . $db->namequote('alias'));
        $query->select('a.' . $db->namequote('content_text'));
        $query->select('a.' . $db->namequote('protected'));
        $query->select('a.' . $db->namequote('featured'));
        $query->select('a.' . $db->namequote('stickied'));
        $query->select('a.' . $db->namequote('status'));
        $query->select('a.' . $db->namequote('start_publishing_datetime'));
        $query->select('a.' . $db->namequote('stop_publishing_datetime'));
        $query->select('a.' . $db->namequote('version'));
        $query->select('a.' . $db->namequote('version_of_id'));
        $query->select('a.' . $db->namequote('status_prior_to_version'));
        $query->select('a.' . $db->namequote('created_datetime'));
        $query->select('a.' . $db->namequote('created_by'));
        $query->select('a.' . $db->namequote('modified_datetime'));
        $query->select('a.' . $db->namequote('modified_by'));
        $query->select('a.' . $db->namequote('checked_out_datetime'));
        $query->select('a.' . $db->namequote('checked_out_by'));

        $query->select('a.' . $db->namequote('root'));
        $query->select('a.' . $db->namequote('parent_id'));
        $query->select('a.' . $db->namequote('lft'));
        $query->select('a.' . $db->namequote('rgt'));
        $query->select('a.' . $db->namequote('lvl'));
        $query->select('a.' . $db->namequote('home'));

        $query->select('a.' . $db->namequote('custom_fields'));
        $query->select('a.' . $db->namequote('parameters'));
        $query->select('a.' . $db->namequote('metadata'));
        $query->select('a.' . $db->namequote('language'));
        $query->select('a.' . $db->namequote('translation_of_id'));
        $query->select('a.' . $db->namequote('ordering'));

        $query->from(Services::Configuration()->get('dbprefix').'content'.' as a ');

        /** Status and Dates */
        $query->where('a.' . $db->namequote('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED);

        $query->where('(a.start_publishing_datetime = ' .
                $db->quote($nullDate) .
                ' OR a.start_publishing_datetime <= ' .
                $db->quote($now) . ')'
        );
        $query->where('(a.stop_publishing_datetime = ' .
                $db->quote($nullDate) .
                ' OR a.stop_publishing_datetime >= ' .
                $db->quote($now) . ')'
        );

        /** ordering */
        $query->order('a.start_publishing_datetime DESC');

        /** Assets Join and View Access Check */
        $query->where('b_assets.asset_type_id = ' .
            $db->quote($asset_type_id));

        MolajoAccessService::setQueryViewAccess(
            $query,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'b_assets',
                'select' => true
            )
        );

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {

        } else {
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
                    $db->getErrorNum() . ' ' .
                    $db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoContentModel::_getItems',
                $debug_object = $db
            );
            return $this->request->set('status_found', false);
        }

        if (count($rows) == 0) {
            return array();
        }

        return $rows;
    }

    /**
     * getItems
     *
     * @return    array
     *
     * @since    1.0
     */
    public function getItems2()
    {


        $this->items = array();

        $tempObject = new JObject();

        $tempObject->set('title', 'Test Title');
        $tempObject->set('content_text', 'Test Paragraph.');
        $tempObject->set('start_publishing_datetime', '2012-02-13');
        $tempObject->set('author', 'Amy Stephen');

        $this->items[] = $tempObject;

        $tempObject = new JObject();

        $tempObject->set('title', '2nd Article Test Title');
        $tempObject->set('content_text', '2nd Test Paragraph.');
        $tempObject->set('start_publishing_datetime', '2012-02-13');
        $tempObject->set('author', 'Doug McGinnis');

        $this->items[] = $tempObject;

        return $this->items;
    }
}
