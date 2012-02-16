<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Display
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoDisplayModel extends MolajoModel
{
    /**
     * $request
     *
     * @var    array
     * @since  1.0
     */
    public $request = null;

    /**
     * $parameters
     *
     * @var    string
     * @since  1.0
     */
    public $parameters = null;

    /**
     * $query
     *
     * @var    string
     * @since  1.0
     */
    protected $query = null;

    protected $_queryStatement = null;

    /**
     * $dispatcher (use application object)
     *
     * @var    string
     * @since  1.0
     */
    protected $dispatcher = null;

    /**
     * $filterFieldName (REMOVE)
     *
     * @var        string
     * @since    1.0
     */
    protected $filterFieldName = null;

    /**
     * Valid filter fields or ordering.
     *
     * @var    array
     * @since  1.0
     */
    protected $filter_fields = array();

    /**
     * Table Fields for verifying select list and ordering items
     *
     * @var    array
     * @since  1.0
     */
    protected $tableFieldList = array();

    /**
     * Model Object for Molajo configuration DELETE
     *
     * @var    array
     * @since  1.0
     */
    protected $molajoConfig = array();

    /**
     * Molajo Field Class
     *
     * @var        array
     * @since    1.0
     */
    protected $fieldClass = array();

    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct(JConfig $config = null)
    {
        $this->_name = get_class($this);
        parent::__construct($config);
    }

    /**
     * populateState (change)
     *
     * Method to auto-populate the model state.
     *
     * @return    void
     * @since    1.0
     */
    protected function populateState()
    {
        $this->context = $this->_name;

        $this->parameters = $this->mvc['parameters'];

        //        $this->filterFieldName = $this->mvc['filterFieldName'];

        $this->fieldClass = new MolajoField();

        //        $this->dispatcher = Services::Dispatcher();

        //        MolajoPluginHelper::importPlugin('query');
        //        MolajoPluginHelper::importPlugin($this->mvc['plugin_type']);

        if ($this->mvc['id'] == 0) {
            $this->populateStateMultiple();
        } else {
            $this->populateItemState();
        }

        //        $this->dispatcher->trigger('queryGetState', array(&$this->status, &$this->parameters));
    }

    /**
     * populateStateMultiple
     *
     * Method to auto-populate the model status.
     *
     * @return    void
     * @since    1.0
     */
    public function populateStateMultiple()
    {
        /** search **/
        $this->processFilter('search');

        /** filters **/
        $loadFilterArray = array();

        /** always do status filter **/
        $loadFilterArray[] = 'status';
        $this->processFilter('status');

        /** force title filter for restore list **/
        if ($this->state->get('filter.status') == MOLAJO_STATUS_VERSION) {
            $loadFilterArray[] = 'title';
            $this->processFilter('title');
        }

        /** selected filters **/
        //for ($i = 1; $i < 10000; $i++) {

        //    $filterName = $this->parameters->def($this->filterFieldName . $i);

        /** end of filter processing **/
        //            if ($filterName == null) {
        //                break;
        //            }

        /** status already processed **/
        //            if ($filterName == 'status') {

        /** configuration option not selected **/
        //            } else if ($filterName == '0') {

        /** no filter was selected for configuration option **/
        //            } else if (in_array($filterName, $loadFilterArray)) {

        /** process selected filter **/
        //            } else {
        //                $loadFilterArray[] = $filterName;
        //                $this->processFilter($filterName);
        //            }
        //        }

        /** list limit **/
        $limit = (int)Services::User()->getUserStateFromRequest(
            'global.list.limit',
            'limit',
            Services::Configuration()->get('list_limit')
        );

        $this->setState('list.limit', (int)$limit);

        /** list start **/
        $value = Services::User()->getUserStateFromRequest(
            $this->context . '.limitstart',
            'limitstart',
            0
        );
        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
        $this->setState('list.start', (int)$limitstart);

        /** ordering by field **/
        $ordering = 'a.title';
        $value = Services::User()->getUserStateFromRequest(
            $this->context . '.ordercol',
            'filter_order',
            $ordering
        );
        if (strpos($value, 'a.')) {
            $searchValue = substr($value, (strpos($value, 'a.') + 1), strlen($value) - strpos($value, 'a.'));
        } else {
            $searchValue = $value;
        }
        if (in_array($searchValue, $this->tableFieldList)) {
            $ordering = $value;
        } else {
            $ordering = 'a.title';
        }
        Services::User()->setUserState(
            $this->context . '.ordercol',
            $ordering
        );

        $this->setState('list.ordering', $value);

        if (in_array($value, $this->tableFieldList)) {
            $ordering = $value;
        } else {
            $ordering = 'a.title';
        }

        /** ordering direction **/
        $direction = 'ASC';
        $value = Services::User()->getUserStateFromRequest(
            $this->context . '.orderdirn',
            'filter_order_Dir',
            $direction
        );
        if (in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
        } else {
            $value = $direction;
            Services::User()->setUserState(
                $this->context . '.orderdirn',
                $value
            );
        }
        $this->setState('list.direction', $value);

        return;
    }

    /**
     * populateItemState
     *
     * Method to populate status values needed for Item View
     *
     * @return    void
     * @since    1.0
     */
    public function populateItemState()
    {
        $loadFilterArray[] = 'id';
        $this->processFilter('id');
    }

    /**
     * processFilter
     *
     * Retrieves filter value
     *
     * @param string $filterName
     * @return boolean
     */
    protected function processFilter($filterName)
    {
        /** class name **/
        $nameClassName = 'MolajoField' . ucfirst($filterName);

        /** class file **/
        $this->fieldClass->getClass($filterName);

        /** class instantiation **/
        if (class_exists($nameClassName)) {
            $molajoSpecificFieldClass = new $nameClassName();
        } else {
            Services::Message()
                ->set(Services::Language()->_('MOLAJO_INVALID_FIELD_CLASS') . ' ' . $nameClassName, 'error');
            return false;
        }

        /** retrieve filtered, validated value **/
        $filterValue = $molajoSpecificFieldClass->getValue();

        /** set status **/
        $this->setState('filter.' . $filterName, $filterValue);

        return true;
    }

    /**
     * getData
     *
     * - set filters
     *      - triggers queryGetState event, passing in the full filter set
     *
     * - create query
     *      - triggers queryBeforeQuery event, passing in the Query object
     *
     * - run query
     *      - triggers queryAfterQuery event, passing in the query resultset
     *
     * - loops through the recordset
     *
     *      - triggers queryBeforeItem event, passing in a single recordset item
     *
     *      - creates 'added value' fields, like author, permanent URL, etc.
     *
     *      - remove items, due to post-query examination
     *
     *      - triggers queryAfterItem event, passing in the current item
     *          'with added value' fields and less those removed
     *
     * - loop complete
     *      - triggers queryComplete event, passing in the resultset
     *
     *      - Returns resultset to the Controller
     *
     * @return    mixed    An array of objects on success, false on failure.
     * @since    1.0
     */
    public function getData()
    {
        $names = $this->getModel()->getProperties();

        $this->tableFieldList = array();
        foreach ($names as $name => $value) {
            $this->tableFieldList[] = $name;
        }

        /** create query **/
        $store = $this->getStoreId();
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        $query = $this->getListQuery();

        /** run query **/
        $this->db->setQuery(
            $query,
            $this->getStart(),
            $this->getState('list.limit')
        );
        $items = $this->db->loadObjectList();

        /** error handling */
        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** pass query results to event */
        //        $this->dispatcher->trigger(
        //            'queryAfterQuery',
        //                array(&$this->status,
        //                    &$items,
        //                    &$this->parameters)
        //       );

        /** publish dates (if the user is not able to see unpublished - and the dates prevent publishing) **/
        $nullDate = $this->db->quote($this->db->getNullDate());
        $nowDate = $this->db->quote(
            $this->alias = Services::Date()
                ->toSql()
        );

        /** retrieve names of json fields for this type of content **/
        $jsonFields = $this->molajoConfig->getOptionList(MOLAJO_EXTENSION_OPTION_ID_JSON_FIELDS);

        /** ACL **/
        $aclClass = ucfirst($this->mvc['option']) . 'Acl';

        /** process rowset */
        $rowCount = 0;
        if (count($items) > 0) {

            for ($i = 0; $i < count($items); $i++) {

                $keep = true;
                $items[$i]->canCheckin = false;
                $items[$i]->checked_out = false;
                //                $this->dispatcher->trigger('queryBeforeItem', array(&$this->status, &$items[$i], &$this->parameters, &$keep));

                /** category is archived, so item should be too **/
                if ($items[$i]->minimum_status_category < $items[$i]->status && $items[$i]->status > MOLAJO_STATUS_VERSION) {
                    $items[$i]->status = $items[$i]->minimum_status_category;
                    /** recheck the new status against query filter **/
                    if ($this->getState('filter.status') > $items[$i]->status) {
                        $keep = false;
                    }
                }

                /** category is unpublished, spammed, or trashed, so item should be too **/
                if ($items[$i]->archived_category == 1 && $items[$i]->status < MOLAJO_STATUS_ARCHIVED) {
                    $items[$i]->status = MOLAJO_STATUS_ARCHIVED;
                    /** recheck the new status against query filter **/
                    if ($this->getState('filter.status') == MOLAJO_STATUS_ARCHIVED
                        || $this->getState('filter.status') == '*'
                    ) {
                    } else {
                        $keep = false;
                    }
                }

                /** split into intro text and full text */
                $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
                $tagPos = preg_match($pattern, $items[$i]->content_text);

                if ($tagPos == 0) {
                    $introtext = $items[$i]->content_text;
                    $fulltext = '';
                } else {
                    list($introtext, $fulltext) = preg_split($pattern, $items[$i]->content_text, 2);
                }

                $items[$i]->introtext = $introtext;
                $items[$i]->fulltext = $fulltext;

                /** some content plugins expect column named text */
                if ($this->parameters->get('view_show_intro', '1') == '1') {
                    $items[$i]->text = $items[$i]->introtext . ' ' . $items[$i]->fulltext;
                } else if ($items[$i]->fulltext) {
                    $items[$i]->text = $items[$i]->fulltext;
                } else {
                    $items[$i]->text = $items[$i]->introtext;
                }

                /** text snippet */
                $items[$i]->snippet = substr($items[$i]->text, 0, $this->parameters->get('view_text_snippet_length', 200));

                if ($items[$i]->created_by_alias == '') {
                    $items[$i]->display_author_name = $items[$i]->author_name;
                } else {
                    $items[$i]->display_author_name = $items[$i]->created_by_alias;
                }

                $items[$i]->slug = $items[$i]->alias ? ($items[$i]->id . ':' . $items[$i]->alias) : $items[$i]->id;
                $items[$i]->catslug = $items[$i]->category_alias
                    ? ($items[$i]->category_id . ':' . $items[$i]->category_alias) : $items[$i]->category_id;
                //                $items[$i]->parent_slug	= $items[$i]->category_alias ? ($items[$i]->parent_id.':'.$items[$i]->parent_alias) : $items[$i]->parent_id;

                $items[$i]->url = '';
                $items[$i]->readmore_link = '';
                // TODO: Change based on shownoauth
                //                $items[$i]->readmore_link = MolajoRouteHelper::_(ContentHelperRoute::getArticleRoute($items[$i]->slug, $items[$i]->catslug));

                /** trigger events */
                //                Services::Dispatcher->notifys();

                if (isset($items[$i]->created)) {
                    $items[$i]->created_date = date($items[$i]->created);
                    $items[$i]->created_ccyymmdd = Services::Date()->convertCCYYMMDD($items[$i]->created);
                    $items[$i]->created_n_days_ago = Services::Date()->differenceDays(date('Y-m-d'), $items[$i]->created_ccyymmdd);
                    $items[$i]->created_ccyymmdd = str_replace('-', '', $items[$i]->created_ccyymmdd);
                    $items[$i]->created_pretty_date = Services::Date()->prettydate($items[$i]->created);
                } else {
                    $items[$i]->created_n_days_ago = '';
                    $items[$i]->created_ccyymmdd = '';
                    $items[$i]->created_pretty_date = '';
                }

                if (isset($items[$i]->modified)) {
                    $items[$i]->modified_ccyymmdd = Services::Date()->convertCCYYMMDD($items[$i]->modified);
                    $items[$i]->modified_n_days_ago = Services::Date()->differenceDays(date('Y-m-d'), $items[$i]->modified_ccyymmdd);
                    $items[$i]->modified_ccyymmdd = str_replace('-', '', $items[$i]->modified_ccyymmdd);
                    $items[$i]->modified_pretty_date = Services::Date()->prettydate($items[$i]->modified);
                } else {
                    $items[$i]->modified_n_days_ago = '';
                    $items[$i]->modified_ccyymmdd = '';
                    $items[$i]->modified_pretty_date = '';
                }

                if (isset($items[$i]->start_publishing_datetime)) {
                    $items[$i]->published_ccyymmdd = Services::Date()->convertCCYYMMDD($items[$i]->start_publishing_datetime);
                    $items[$i]->published_n_days_ago = Services::Date()->differenceDays(date('Y-m-d'), $items[$i]->published_ccyymmdd);
                    $items[$i]->published_ccyymmdd = str_replace('-', '', $items[$i]->published_ccyymmdd);
                    $items[$i]->published_pretty_date = Services::Date()->prettydate($items[$i]->start_publishing_datetime);
                } else {
                    $items[$i]->published_n_days_ago = '';
                    $items[$i]->published_ccyymmdd = '';
                    $items[$i]->published_pretty_date = '';
                }

                /** Perform JSON to array conversion... **/
                foreach ($jsonFields as $name) {
                    $attribute = $name->value;
                    if (property_exists($items[$i], $attribute)) {
                        $registry = new Registry;
                        $registry->loadJSON($items[$i]->$attribute);
                        $items[$i]->$attribute = $registry->toArray();
                    }
                }

                /** acl-append item-specific task permissions **/
                $acl = new $aclClass();
                $results = $acl->getUserItemPermissions($this->mvc['option'],
                    $this->mvc['view'],
                    $items[$i]);
                if ($results === false) {
                    $keep = false;
                }

                //                $this->dispatcher->trigger('queryAfterItem', array(&$this->status, &$items[$i], &$this->parameters, &$keep));

                /** process content plugins */
                //                $this->dispatcher->trigger('contentPrepare', array($this->context, &$items[$i], &$this->parameters, $this->getState('list.start')));
                $items[$i]->event = new stdClass();

                //                $results = $this->dispatcher->trigger(
                //                    'contentBeforeDisplay',
                //                    array($this->context,
                //                        &$items[$i],
                //                        &$this->parameters,
                //                        $this->getState('list.start')
                //                   )
                //                );
                $items[$i]->event->beforeDisplayContent = trim(implode("\n", $results));

                //                $results = $this->dispatcher->trigger('contentAfterDisplay', array($this->context, &$items[$i], &$this->parameters, $this->getState('list.start')));
                $items[$i]->event->afterDisplayContent = trim(implode("\n", $results));

                /** remove item overridden by category and no longer valid for criteria **/
                if ($keep === true) {
                    $items[$i]->rowCount = $rowCount++;
                } else {
                    unset($items[$i]);
                }
            }
        }

        /** final event for queryset */
        //        $this->dispatcher->trigger('queryComplete', array(&$this->status, &$items, &$this->parameters));

        /** place query results in cache **/
        $this->cache[$store] = $items;

        /** return from cache **/
        return $this->cache[$store];
    }

    /**
     * getListQuery
     *
     * Build query for retrieving a list of items subject to the model status.
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    function getListQuery()
    {
        $this->query = $this->db->getQuery(true);

        /** Process each field that is 1) required 2) selected for display and 3) selected as a filter **/
        $nameArray = array();

        /** load all available columns into select list **/
        foreach ($this->tableFieldList as $name) {
            $this->setQueryInformation($name, false);
        }

        /** process search filter */
        $this->setQueryInformation('search', false);

        /** primary table **/
        $this->query->from('#' . $this->mvc['component_table'] . ' AS a');
        $this->_queryStatement = $this->query;

        /** parent category **/
        //        $this->query->select('c.id AS category_id, c.title AS category_title, c.path AS category_route, c.alias AS category_alias');
        //        $this->query->join('LEFT', '#__categories AS c ON c.id = a.category_id');

        /** sins of the parent checking **/

        /** spammed or trashed or unpublished ancestor = same for descendents **/
        //        $this->query->select(' minimumState.published AS minimum_status_category');
        //        $subQuery = ' SELECT parent.id, MIN(parent.published) AS published ';
        //        $subQuery .= ' FROM #__categories AS cat ';
        //        $subQuery .= ' JOIN #__categories AS parent ON cat.lft BETWEEN parent.lft AND parent.rgt ';
        //        $subQuery .= ' WHERE parent.extension = ' . $this->db->quote($this->mvc['option']);
        //        $subQuery .= '   AND cat.published > ' . MOLAJO_STATUS_VERSION;
        //        $subQuery .= '   AND parent.published > ' . MOLAJO_STATUS_VERSION;
        //        $subQuery .= ' GROUP BY parent.id ';
        //        $this->query->join(' LEFT OUTER', '(' . $subQuery . ') AS minimumState ON minimumState.id = c.id ');

        /** archived ancestor = archived descendents **/
        //        $this->query->select(' CASE WHEN maximumState.published > ' . MOLAJO_STATUS_PUBLISHED . ' THEN 1 ELSE 0 END AS archived_category');
        //        $subQuery = ' SELECT parent.id, MAX(parent.published) AS published ';
        //        $subQuery .= ' FROM #__categories AS cat ';
        //        $subQuery .= ' JOIN #__categories AS parent ON cat.lft BETWEEN parent.lft AND parent.rgt ';
        //       $subQuery .= ' WHERE parent.extension = ' . $this->db->quote($this->mvc['option']);
        //        $subQuery .= ' GROUP BY parent.id ';
        //        $this->query->join(' LEFT OUTER', '(' . $subQuery . ') AS maximumState ON maximumState.id = c.id ');

        /**
        $date = Services::Date();
        $now = $date->toSql();
        $nullDate = $db->getNullDate();
        $query->where('(m.start_publishing_datetime = '.$db->quote($nullDate).' OR m.start_publishing_datetime <= '.$db->quote($now).')');
        $query->where('(m.stop_publishing_datetime = '.$db->quote($nullDate).' OR m.stop_publishing_datetime >= '.$db->quote($now).')');
         */
        /** set view access criteria for site visitor **/
        $acl = new MolajoACL ();
        $results = $acl->getQueryInformation($this->mvc['view'], $this->query, 'user', '', $this->mvc['view']);

        /** set ordering and direction **/
        $orderCol = $this->state->get('list.ordering', 'a.title');
        $orderDirn = $this->state->get('list.direction', 'asc');
        //        if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
        //            $orderCol = 'category_title ' . $orderDirn . ', a.ordering';
        //        }
        $this->query->order($this->db->getEscaped($orderCol . ' ' . $orderDirn));

        /** pass query object to event */
        //        $this->dispatcher->trigger('queryBeforeQuery', array(&$this->status, &$this->query, &$this->parameters));

        return $this->query;
    }

    /**
     * getPagination
     *
     * Method to get a JPagination object for the data set.
     *
     * @return    object    A JPagination object for the data set.
     * @since    1.0
     */
    public function getPagination()
    {
        /** get pagination id **/
        $store = $this->getStoreId('getPagination');

        /** if available, load from cache **/
        if (empty($this->cache[$store])) {
        } else {
            return $this->cache[$store];
        }

        /** pagination object **/
        $limit = (int)$this->getState('list.limit') - (int)$this->getState('list.links');
        $page = new JPagination($this->getTotal(), $this->getStart(), $limit);

        /** load cache **/
        $this->cache[$store] = $page;

        /** return from cache **/
        return $this->cache[$store];
    }

    /**
     * getTotal
     *
     * Method to get the total number of items for the data set.
     *
     * @return    integer
     * @since    1.0
     */
    public function getTotal()
    {
        /** cache **/
        $store = $this->getStoreId('getTotal');
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        /** get total of items returned from the last query **/
        $this->db->setQuery($this->queryStatement);
        $this->db->query();

        $total = (int)$this->db->getNumRows();

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** load cache **/
        $this->cache[$store] = $total;

        /** return from cache **/
        return $this->cache[$store];
    }

    /**
     * getStart
     *
     * Method to get the starting number of items for the data set.
     *
     * @return    integer
     * @since    1.0
     */
    public function getStart()
    {
        /** cache **/
        $store = $this->getStoreId('getStart');
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        /** get list object **/
        $start = $this->getState('list.start');
        $limit = $this->getState('list.limit');
        $total = $this->getTotal();
        if ($start > $total - $limit) {
            $start = max(0, (int)(ceil($total / $limit) - 1) * $limit);
        }

        /** load cache **/
        $this->cache[$store] = $start;

        /** return from cache **/
        return $this->cache[$store];
    }

    /**
     * getStoreId
     *
     * Method to get a unique store id based on model configuration status.
     *
     * @param    string        $id    A prefix for the store id.
     *
     * @return    string        A store id.
     * @since    1.0
     */
    protected function getStoreId($id = '')
    {
        $id = ':' . $this->getState('filter.search');

        //        for ($i = 1; $i < 1000; $i++) {
        //            $temp = $this->parameters->def($this->filterFieldName . $i);
        //            $filterName = substr($temp, 0, stripos($temp, ';'));
        //            $filterDataType = substr($temp, stripos($temp, ';') + 1, 1);
        //            if ($filterName == null) {
        //                break;
        //            } else {
        //                $id .= ':' . $this->getState('filter.' . $filterName);
        //            }
        //        }

        $id .= ':' . $this->getState('filter.view');

        $id .= ':' . $this->getState('list.start');
        $id .= ':' . $this->getState('list.limit');
        $id .= ':' . $this->getState('list.ordering');
        $id .= ':' . $this->getState('list.direction');

        return md5($this->context . ':' . $id);
    }

    /**
     * getAuthors
     *
     * Build a list of authors
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    public function getAuthors()
    {
        $this->query = $this->db->getQuery(true);

        $this->query->select('a.id AS value, a.name AS text');
        $this->query->from('#__users AS a');
        $this->query->from($this->db->namequote('#' . $this->mvc['component_table']) . ' AS b');
        $this->query->where('a.created_by = b.id');
        $this->query->group('a.id');
        $this->query->order('a.name');

        $this->db->setQuery($this->query->__toString());

        return $this->db->loadObjectList();
    }

    /**
     * getMonthsCreate
     *
     * Build a list of created date months in content
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    public function getMonthsCreate($table = null)
    {
        return $this->getMonths('created', $table);
    }

    /**
     * getMonthsModified
     *
     * Build a list of modified months in content
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    public function getMonthsModified($table = null)
    {
        return $this->getMonths('modified', $table);
    }

    /**
     * getMonthsUpdate
     *
     * Build a list of publish months in content
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    public function getMonthsUpdate($table = null)
    {
        return $this->getMonths('modified', $table);
    }

    /**
     * getMonthsPublish
     *
     * Build a list of publish months in content
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    public function getMonthsPublish($table = null)
    {
        return $this->getMonths('start_publishing_datetime', $table);
    }

    /**
     * getMonths
     *
     * Build a list of months in content
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    public function getMonths($columnName, $table = null)
    {
        $this->query = $this->db->getQuery(true);

        $this->query->select('DISTINCT CONCAT(SUBSTRING(a.' . $this->db->namequote($columnName) . ', 1, 4),
                                            SUBSTRING(a.' . $this->db->namequote($columnName) . ', 6, 2)) AS value,
                                            SUBSTRING(a.' . $this->db->namequote($columnName) . ', 1, 7) AS text');

        if ($table == null) {
            $this->queryTable = '#' . $this->mvc['component_table'];
        } else {
            $this->queryTable = $table;
        }
        $this->query->from($this->db->namequote($this->queryTable) . ' AS a');
        $this->query->group('SUBSTRING(a.' . $this->db->namequote($columnName) . ', 1, 4),
                                            SUBSTRING(a.' . $this->db->namequote($columnName) . ', 6, 2),
                                            SUBSTRING(a.' . $this->db->namequote($columnName) . ', 1, 7)');
        $this->query->order('SUBSTRING(a.' . $this->db->namequote($columnName) . ', 1, 7)');

        $this->db->setQuery($this->query->__toString());

        return $this->db->loadObjectList();
    }

    /**
     * getOptionList
     *
     * Return Query results for two fields
     *
     * @param string $name1
     * @param string $name2
     * @param boolean $showKey
     * @param boolean $showKeyFirst
     * @param string $table
     * @return object query results
     */
    public function getOptionList($name1, $name2, $showKey = false, $showKeyFirst = false, $table = null)
    {
        $this->parameters = MolajoComponent::getParameters($this->mvc['option']);

        $this->query = $this->db->getQuery(true);

        /** select **/
        if ($showKey == true) {
            if ($showKeyFirst == true) {
                $nameArray2 = 'CONCAT(' . $this->db->namequote($name1) . ', ": ",' . $this->db->namequote($name2) . ' )';
            } else {
                $nameArray2 = 'CONCAT(' . $this->db->namequote($name2) . ', " (",' . $this->db->namequote($name1) . ', ")")';
            }
        } else {
            $nameArray2 = $name2;
        }
        $this->query->select('DISTINCT ' . $this->db->namequote($name1) . ' AS value, ' . $nameArray2 . ' as text');

        /** from **/
        if ($table == null) {
            $this->queryTable = '#' . $this->mvc['component_table'];
        } else {
            $this->queryTable = $table;
        }
        $this->query->from($this->db->namequote($this->queryTable) . ' AS a');

        /** where **/
        $this->filterFieldName = JRequest::getCmd('filterFieldName', 'config_manager_list_filters') . '_query_filters';

        for ($i = 1; $i < 1000; $i++) {

            $filterName = $this->parameters->def($this->filterFieldName . $i);

            /** end of filter processing **/
            if ($filterName == null) {
                break;
            }

            /** configuration option not selected **/
            if ($filterName == '0') {

            } else if ($filterName == $name2) {

                /** process selected filter (only where clause) **/
            } else {
                $this->setQueryInformation($filterName, true);
            }
        }

        /** group by **/
        $this->query->group($name1, $nameArray2);

        /** order by **/
        if ($showKey == true && $showKeyFirst == true) {
            $this->query->order($name1);
        } else {
            $this->query->order($name2);
        }

        /** run query and return results **/
        $this->db->setQuery($this->query->__toString());
        return $this->db->loadObjectList();
        //$this->db->setQuery($query, $limitstart, $limit);
        //$result = $this->db->loadObjectList();
        //return $result;
    }

    /**
     * setQueryInformation
     *
     * @param string $name
     * @param boolean $onlyWhereClause - true - all query parts; false - only where clause
     * @return sets $query object
     */
    public function setQueryInformation($name, $onlyWhereClause = false)
    {
        $selectedState = $this->getState('filter.status');
        $nameClassName = 'MolajoField' . ucfirst($name);
        $this->fieldClass->getClass($name, false);

        if (class_exists($nameClassName)) {
            $value = $this->getState('filter.' . $name);
            $molajoSpecificFieldClass = new $nameClassName();
            $molajoSpecificFieldClass->getQueryInformation($this->query, $value, $selectedState, $onlyWhereClause, $this->mvc['view']);

        } else {
            if ($onlyWhereClause === true) {
                Services::Message()
                    ->set(Services::Language()->_('MOLAJO_INVALID_FIELD_CLASS') . ' ' . $nameClassName, 'error');
                return false;
            } else {
                $this->query->select('a.' . $name);
                return true;
            }
        }
    }

    /**
     * validateValue
     *
     * @param string $columnName
     * @param string $value
     * @param string $valueType
     * @param string $table
     * @return mixed either false or the validated value
     */
    public function validateValue($columnName, $value, $valueType, $table = null)
    {
        $this->query = $this->db->getQuery(true);

        $this->query->select('DISTINCT ' . $this->db->namequote($columnName) . ' as value');

        if ($table == null) {
            $this->query->from($this->db->namequote('#' . $this->mvc['component_table']));
        } else {
            $this->query->from($this->db->namequote($table));
        }

        if ($valueType == 'numeric') {
            $this->query->where($this->db->namequote($columnName) . ' = ' . (int)$value);
        } else {
            $this->query->where($this->db->namequote($columnName) . ' = ' . $this->db->quote($value));
        }

        $this->db->setQuery($this->query->__toString());

        if (!$results = $this->db->loadObjectList()) {
            Services::Message()
                ->set($this->db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $count => $result) {
                $singleValue = $result->value;
            }
            return true;
        }

        return false;
    }

    /**
     * checkCategories
     *
     * Verifies if one of the Category Values sent in matches a Category for the Component
     *
     * @param string $columnName
     * @param string $value
     * @return boolean
     * @since    1.0
     */
    public function checkCategories($categoryArray)
    {
        $this->query = $this->db->getQuery(true);

        $this->query->select('DISTINCT id');
        $this->query->from($this->db->namequote('#__categories'));

        /** category array **/
        JArrayHelper::toInteger($categoryArray);
        if (empty($categoryArray)) {
            return;
        }
        $this->query->where($this->db->namequote('id') . ' IN (' . $categoryArray . ')');
        $this->query->where($this->db->namequote('extension') . ' = ' . $this->db->quote($this->mvc['option']));

        $this->db->setQuery($this->query->__toString());

        if (!$results = $this->db->loadObjectList()) {
            Services::Message()
                ->set($this->db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            return true;
        }

        return false;
    }

    /**
     * getModel
     *
     * Returns a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     *
     * @return    MolajoModel    A database object
     */
    public function getModel($type = '', $prefix = 'Molajo', $config = array())
    {
        $mc = ucfirst($prefix) . $type . 'Model';
        if (class_exists($mc)) {
            return new $mc($config);
        }
    }

    /**
     * saveSelective
     *
     * Not using this right now, may choose to later
     *
     * This code restricts the select list to a required list, those items specified for the filter and for the specific view
     *
     * Could help with speed (a bit)
     *
     */
    private function saveSelective()
    {
        /** 1: required fields **/
        $nameArray = array();
        $requireList = 'id,title,alias,status,category_id,created_by,access,checked_out,checked_out_time,search';
        $requiredArray = explode(',', $requireList);

        foreach ($requiredArray as $required) {
            if (in_array($required, $nameArray)) {
            } else {
                $nameArray[] = $required;
            }
        }

        /** 2: selected for display **/
        $this->filterFieldName = JRequest::getCmd('selectFieldName', 'config_manager_grid_column');

        for ($i = 1; $i < 1000; $i++) {

            $name = $this->parameters->get($this->filterFieldName . $i);

            /** end of filter processing **/
            if ($name == null) {
                break;
            }

            /** configuration option not selected **/
            if ($name == '0') {

                /** selected twice by user in configuration **/
            } else if (in_array($name, $nameArray)) {

                /** store for filtering and then processing **/
            } else {
                $nameArray[] = $name;
            }
        }

        /** 3: selected as a filter **/
        $this->filterFieldName = JRequest::getCmd('filterFieldName', 'config_manager_list_filters');

        for ($i = 1; $i < 1000; $i++) {

            $name = $this->parameters->def($this->filterFieldName . $i);

            /** end of filter processing **/
            if ($name == null) {
                break;
            }

            /** configuration option not selected **/
            if ($name == '0') {

                /** listed twice, ignore after first use **/
            } else if (in_array($name, $nameArray)) {

                /** process selected field **/
            } else {
                $nameArray[] = $name;
            }
        }

        /** filter by known field names and append into query object **/
        foreach ($nameArray as $name) {
            if ((in_array($name, $this->tableFieldList)) || $name == 'search') {
                $this->setQueryInformation($name, false);
            }
        }
    }
}
