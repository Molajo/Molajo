<?php
/**
 * @version     $id: display.php
 * @package     Molajo
 * @subpackage  Display Model
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoModelDisplay
 *
 * Component Model for Display Views
 *
 * @package	    Molajo
 * @subpackage	Model
 * @since 1.0
 */
class MolajoModelDisplay extends JModel
{
    /**
     * $request
     *
     * @var		array
     * @since	1.0
     */
    public $request = null;

    /**
     * $params
     *
     * @var		string
     * @since	1.0
     */
    protected $params = null;

    /**
     * $query
     *
     * @var		string
     * @since	1.0
     */
    protected $query = null;

    /**
     * $this->dispatcher
     *
     * @var    string
     * @since  1.6
     */
    protected $dispatcher = null;

    /**
     * $filterFieldName (REMOVE)
     *
     * @var		string
     * @since	1.0
     */
    protected $filterFieldName = null;

    /**
     * Internal memory based cache array of data.
     *
     * @var		array
     * @since	1.0
     */
    protected $cache = array();

    /**
     * Context string for the model for uniqueness with caching data structures.
     *
     * @var		string
     * @since	1.0
     */
    protected $context = null;

    /**
     * Valid filter fields or ordering.
     *
     * @var		array
     * @since	1.0
     */
    protected $filter_fields = array();

    /**
     * Valid fields in table for verifying to select list and ordering values
     *
     * @var		array
     * @since	1.0
     */
    protected $tableFieldList = array();

    /**
     * Model Object for Molajo configuration
     *
     * @var		array
     * @since	1.0
     */
    protected $molajoConfig = array();

    /**
     * Molajo Field Class
     *
     * @var		array
     * @since	1.0
     */
    protected $molajoField = array();

    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * @return	void
     * @since	1.0
     */
    protected function populateState ()
    {
        $this->context = strtolower($this->request['option'].'.'.$this->getName()).'.'.$this->request['layout'];

        $this->params = $this->request['params'];

            $this->filterFieldName = $this->request['filterFieldName'];

            $this->molajoConfig = new MolajoModelConfiguration($this->request['option']);

            $this->molajoField  = new MolajoField();

            $this->dispatcher = JDispatcher::getInstance();

            MolajoPluginHelper::importPlugin('query');
            MolajoPluginHelper::importPlugin($this->request['plugin_type']);



            $this->dispatcher->trigger('onQueryPopulateState', array (&$this->state, &$this->params));

    }

    /**
     * populateStateMultiple
     *
     * Method to auto-populate the model state.
     *
     * @return	void
     * @since	1.0
     */
    public function populateStateMultiple ()
    {
        /** search **/
        $this->processFilter ('search');

        /** filters **/
        $loadFilterArray = array();

        /** always do state filter **/
        $loadFilterArray[] = 'state';
        $this->processFilter('state');

        /** force title filter for restore list **/
        if ($this->state->get('filter.state') == MOLAJO_STATE_VERSION) {
            $loadFilterArray[] = 'title';
            $this->processFilter('title');
        }

        /** selected filters **/
        for ($i=1; $i < 1000; $i++) {

            $filterName = $this->params->def($this->filterFieldName.$i);

            /** end of filter processing **/
            if ($filterName == null) { break; }

            /** state already processed **/
            if ($filterName == 'state') {

            /** configuration option not selected **/
            } else if ($filterName == '0') {

            /** no filter was selected for configuration option **/
            } else if (in_array($filterName, $loadFilterArray)) {

            /** process selected filter **/
            } else {
                $loadFilterArray[] = $filterName;
                $this->processFilter($filterName);
            }
        }

        /** list limit **/
        $limit = (int) MolajoFactory::getApplication()->getUserStateFromRequest('global.list.limit', 'limit',
                                                                           MolajoFactory::getApplication()->getCfg('list_limit'));
        $this->setState('list.limit', (int) $limit);

        /** list start **/
        $value = MolajoFactory::getApplication()->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
        $this->setState('list.start', (int) $limitstart);

        /** ordering by field **/
        $ordering = 'a.title';
        $value = MolajoFactory::getApplication()->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
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
        MolajoFactory::getApplication()->setUserState($this->context.'.ordercol', $ordering);

        $this->setState('list.ordering', $value);

        if (in_array($value, $this->tableFieldList)) {
            $ordering = $value;
        } else {
            $ordering = 'a.title';
        }

        /** ordering direction **/
        $direction = 'ASC';
        $value = MolajoFactory::getApplication()->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
        if (in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
        } else {
            $value = $direction;
            MolajoFactory::getApplication()->setUserState($this->context.'.orderdirn', $value);
        }
        $this->setState('list.direction', $value);

        return;
    }

    /**
     * populateItemState
     * Method to populate state values needed for Item Layout
     *
     * @return	void
     * @since	1.0
     */
    public function populateItemState ()
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
    protected function processFilter ($filterName)
    {
        /** class name **/
        $fieldClassName = 'MolajoField'.ucfirst($filterName);

        /** class file **/
        $this->molajoField->requireFieldClassFile ($filterName);

        /** class instantiation **/
        if (class_exists($fieldClassName)) {
            $molajoSpecificFieldClass = new $fieldClassName();
        } else {
            MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_FIELD_CLASS').' '.$fieldClassName, 'error');
            return false;
        }

        /** retrieve filtered, validated value **/
        $filterValue = $molajoSpecificFieldClass->getSelectedValue();

        /** set state **/
        $this->setState('filter.'.$filterName, $filterValue);

        return true;
    }

    /**
     * Method to set model state variables
     *
     * @param   string  $property	The name of the property
     * @param   mixed   $value		The value of the property to set
     *
     * @return  mixed   The previous value of the property
     * @since   11.1
     */
    public function setState($property, $value = null)
    {
        return $this->state->set($property, $value);
    }

    /**
     * getItems
     *
     * - set filters (before)
     *      - triggers onQueryPopulateState event, passing in the full filter set
     *
     * - create query (called from View)
     *      - triggers onQueryBeforeQuery event, passing in the Query object
     *
     * - run query
     *      - triggers onQueryAfterQuery event, passing in the full query resultset
     *
     * - loops through the recordset
     *
     *      - triggers onQueryBeforeItem event, passing in the new item in the recordset
     *
     *      - creates 'added value' fields, like author, permanent URL, etc.
     *
     *      - remove items due to post query examination
     *
     *      - triggers onQueryAfterItem event, passing in the current item with added value fields
     *
     * - loop complete
     *      - triggers onQueryComplete event, passing in the resultset, less items removed, with augmented data
     *
     *      - Returns resultset to the View
     *
     * @return	mixed	An array of objects on success, false on failure.
     *
     * @since	1.0
     */
    public function getItems()
    {
        /** extract actual column names from table **/
        $table = $this->getTable();
        $fields = $table->getProperties();

        $this->tableFieldList = array();
        foreach ($fields as $fieldname => $value) {
            $this->tableFieldList[] = $fieldname;
        }

        /** create query **/
        $store = $this->getStoreId();
        if (empty($this->cache[$store])) {

        } else {
            return $this->cache[$store];
        }

        $query	= $this->getListQueryCache();

        /** run query **/
		$this->_db->setQuery($query, $this->getStart(), $this->getState('list.limit'));
		$items = $this->_db->loadObjectList();

        /** error handling */
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        /** pass query results to event */
        $this->dispatcher->trigger('onQueryAfterQuery', array(&$this->state, &$items, &$this->params));

        /** publish dates (if the user is not able to see unpublished - and the dates prevent publilshing) **/
        $nullDate = $this->_db->Quote($this->_db->getNullDate());
        $nowDate = $this->_db->Quote(MolajoFactory::getDate()->toMySQL());

        /** retrieve names of json fields for this type of content **/
        $jsonFields = $this->molajoConfig->getOptionList (MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS);

        /** ACL **/
        $aclClass = 'MolajoACL'.ucfirst($this->request['view']);

        /** process rowset */
        $rowCount = 0;
        if (count($items) > 0) {

            for ($i=0; $i < count($items); $i++) {

                $keep = true;
$items[$i]->canCheckin = false;
$items[$i]->checked_out = false;
                $this->dispatcher->trigger('onQueryBeforeItem', array(&$this->state, &$items[$i], &$this->params, &$keep));

                /** category is archived, so item should be too **/
                if ($items[$i]->minimum_state_category < $items[$i]->state && $items[$i]->state > MOLAJO_STATE_VERSION) {
                    $items[$i]->state = $items[$i]->minimum_state_category;
                    /** recheck the new status against query filter **/
                    if ($this->getState('filter.state') > $items[$i]->state) {
                        $keep = false;
                    }
                }

                /** category is unpublished, spammed, or trashed, so item should be too **/
                if ($items[$i]->archived_category == 1 && $items[$i]->state < MOLAJO_STATE_ARCHIVED) {
                    $items[$i]->state = MOLAJO_STATE_ARCHIVED;
                    /** recheck the new status against query filter **/
                    if ($this->getState('filter.state') == MOLAJO_STATE_ARCHIVED
                        || $this->getState('filter.state') =='*') {
                    } else {
                        $keep = false;
                    }
                }

                /** split into intro text and full text */
                $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
                $tagPos	= preg_match($pattern, $items[$i]->content_text);

                if ($tagPos == 0) {
                    $introtext = $items[$i]->content_text;
                    $fulltext  = '';
                } else {
                    list($introtext, $fulltext) = preg_split($pattern, $items[$i]->content_text, 2);
                }

                $items[$i]->introtext = $introtext;
                $items[$i]->fulltext = $fulltext;

                /** some content plugins expect column named text */
                if ($this->params->get('layout_show_intro','1') == '1') {
                    $items[$i]->text = $items[$i]->introtext.' '.$items[$i]->fulltext;
                } else if ($items[$i]->fulltext) {
                    $items[$i]->text = $items[$i]->fulltext;
                } else {
                    $items[$i]->text = $items[$i]->introtext;
                }

                /** text snippet */
                $items[$i]->snippet = substr($items[$i]->text, 0, $this->params->get('layout_text_snippet_length', 200));

                if ($items[$i]->created_by_alias == '') {
                    $items[$i]->display_author_name = $items[$i]->author_name;
                } else {
                    $items[$i]->display_author_name = $items[$i]->created_by_alias;
                }

                $items[$i]->slug		= $items[$i]->alias ? ($items[$i]->id.':'.$items[$i]->alias) : $items[$i]->id;
                $items[$i]->catslug		= $items[$i]->category_alias ? ($items[$i]->category_id.':'.$items[$i]->category_alias) : $items[$i]->category_id;
//                $items[$i]->parent_slug	= $items[$i]->category_alias ? ($items[$i]->parent_id.':'.$items[$i]->parent_alias) : $items[$i]->parent_id;

                $items[$i]->url		= '';
                $items[$i]->readmore_link		= '';
                // TODO: Change based on shownoauth
//                $items[$i]->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($items[$i]->slug, $items[$i]->catslug));

                /** trigger events */
//                $this->_triggerEvents();
                $dateHelper = new MolajoDateHelper ();
                if (isset($items[$i]->created)) {
                    $items[$i]->created_date = date($items[$i]->created);
                    $items[$i]->created_ccyymmdd = $dateHelper->convertCCYYMMDD ($items[$i]->created);
                    $items[$i]->created_n_days_ago = $dateHelper->differenceDays (date('Y-m-d'), $items[$i]->created_ccyymmdd);
                    $items[$i]->created_ccyymmdd = str_replace('-', '', $items[$i]->created_ccyymmdd);
                    $items[$i]->created_pretty_date = $dateHelper->prettydate ($items[$i]->created);
                }  else {
                    $items[$i]->created_n_days_ago = '';
                    $items[$i]->created_ccyymmdd = '';
                    $items[$i]->created_pretty_date = '';
                }

                if (isset($items[$i]->modified)) {
                    $items[$i]->modified_ccyymmdd = $dateHelper->convertCCYYMMDD ($items[$i]->modified);
                    $items[$i]->modified_n_days_ago = $dateHelper->differenceDays (date('Y-m-d'), $items[$i]->modified_ccyymmdd);
                    $items[$i]->modified_ccyymmdd = str_replace('-', '', $items[$i]->modified_ccyymmdd);
                    $items[$i]->modified_pretty_date = $dateHelper->prettydate ($items[$i]->modified);
                }  else {
                    $items[$i]->modified_n_days_ago = '';
                    $items[$i]->modified_ccyymmdd = '';
                    $items[$i]->modified_pretty_date = '';
                }

                if (isset($items[$i]->publish_up)) {
                    $items[$i]->published_ccyymmdd = $dateHelper->convertCCYYMMDD ($items[$i]->publish_up);
                    $items[$i]->published_n_days_ago = $dateHelper->differenceDays (date('Y-m-d'), $items[$i]->published_ccyymmdd);
                    $items[$i]->published_ccyymmdd = str_replace('-', '', $items[$i]->published_ccyymmdd);
                    $items[$i]->published_pretty_date = $dateHelper->prettydate ($items[$i]->publish_up);
                }  else {
                    $items[$i]->published_n_days_ago = '';
                    $items[$i]->published_ccyymmdd = '';
                    $items[$i]->published_pretty_date = '';
                }

                /** Perform JSON to array conversion... **/
                foreach ($jsonFields as $field) {
                    $attribute = $field->value;
                    if (property_exists($items[$i], $attribute)) {
                        $registry = new JRegistry;
                        $registry->loadJSON($items[$i]->$attribute);
                        $items[$i]->$attribute = $registry->toArray();
                    }
                }

                /** acl-append item-specific task permissions **/
                $acl = new $aclClass();
                $results = $acl->getUserItemPermissions ($this->request['option'],
                                                              $this->request['view'],
                                                              $this->request['task'],
                                                              $items[$i]->id,
                                                              $items[$i]->category_id,
                                                              $items[$i]);
                if ($results === false) {
                    $keep = false;
                }

                $this->dispatcher->trigger('onQueryAfterItem', array(&$this->state, &$items[$i], &$this->params, &$keep));

                /** process content plugins */
                $this->dispatcher->trigger('onContentPrepare', array ($this->context, &$items[$i], &$this->params, $this->getState('list.start')));
                $items[$i]->event = new stdClass();
        
                $results = $this->dispatcher->trigger('onContentAfterTitle', array($this->context, &$items[$i], &$this->params, $this->getState('list.start')));
                $items[$i]->event->afterDisplayTitle = trim(implode("\n", $results));
        
                $results = $this->dispatcher->trigger('onContentBeforeDisplay', array($this->context, &$items[$i], &$this->params, $this->getState('list.start')));
                $items[$i]->event->beforeDisplayContent = trim(implode("\n", $results));
        
                $results = $this->dispatcher->trigger('onContentAfterDisplay', array($this->context, &$items[$i], &$this->params, $this->getState('list.start')));
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
        $this->dispatcher->trigger('onQueryComplete', array(&$this->state, &$items, &$this->params));

        /** place query results in cache **/
        $this->cache[$store] = $items;

        /** return from cache **/
        return $this->cache[$store];
    }

    /**
     * getListQueryCache
     *
     * Method to cache the last query constructed.
     *
     * This method ensures that the query is constructed only once for a given state of the model.
     *
     * @return	JDatabaseQuery
     * @since	1.0
     */
    private function getListQueryCache()
    {
        static $lastStoreId;
        $currentStoreId = $this->getStoreId();

        if ($lastStoreId != $currentStoreId || empty($this->query)) {
            $lastStoreId = $currentStoreId;
            $this->query = $this->getListQuery();
        }

        return $this->query;
    }

    /**
     * getListQuery
     *
     * Build query for retrieving a list of items subject to the model state.
     *
     * @return	JDatabaseQuery
     * @since	1.0
     */
    function getListQuery()
    {
        /** retrieve JDatabaseQuery object */
        $this->query = $this->_db->getQuery(true);

        /** Process each field that is 1) required 2) selected for display and 3) selected as a filter **/
        $fieldArray = array();

        /** load all available columns into select list **/
        foreach ($this->tableFieldList as $fieldName) {
            $this->setQueryInformation ($fieldName, false);
        }

        /** process search filter */
        $this->setQueryInformation ('search', false);

        /** primary table **/
        $this->query->from('#'.$this->request['component_table'].' AS a');

        /** parent category **/
        $this->query->select('c.id AS category_id, c.title AS category_title, c.path AS category_route, c.alias AS category_alias');
        $this->query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        /** sins of the parent checking **/

        /** spammed or trashed or unpublished ancestor = same for descendents **/
        $this->query->select(' minimumState.published AS minimum_state_category');
            $subQuery = ' SELECT parent.id, MIN(parent.published) AS published ';
            $subQuery .= ' FROM #__categories AS cat ';
            $subQuery .= ' JOIN #__categories AS parent ON cat.lft BETWEEN parent.lft AND parent.rgt ';
            $subQuery .= ' WHERE parent.extension = '.$this->_db->quote($this->request['option']);
            $subQuery .= '   AND cat.published > '.MOLAJO_STATE_VERSION;
            $subQuery .= '   AND parent.published > '.MOLAJO_STATE_VERSION;
            $subQuery .= ' GROUP BY parent.id ';
        $this->query->join(' LEFT OUTER', '('.$subQuery.') AS minimumState ON minimumState.id = c.id ');

        /** archived ancestor = archived descendents **/
        $this->query->select(' CASE WHEN maximumState.published > '.MOLAJO_STATE_PUBLISHED.' THEN 1 ELSE 0 END AS archived_category');
            $subQuery = ' SELECT parent.id, MAX(parent.published) AS published ';
            $subQuery .= ' FROM #__categories AS cat ';
            $subQuery .= ' JOIN #__categories AS parent ON cat.lft BETWEEN parent.lft AND parent.rgt ';
            $subQuery .= ' WHERE parent.extension = '.$this->_db->quote($this->request['option']);
            $subQuery .= ' GROUP BY parent.id ';
        $this->query->join(' LEFT OUTER', '('.$subQuery.') AS maximumState ON maximumState.id = c.id ');

/**
			$date = MolajoFactory::getDate();
			$now = $date->toMySQL();
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($now).')');
			$query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($now).')');
*/
        /** set view access criteria for site visitor **/
        $acl = new MolajoACL ();
        $results = $acl->getQueryInformation ($this->request['view'], $this->query, 'user', '', $this->request['view']);

        /** set ordering and direction **/
        $orderCol	= $this->state->get('list.ordering', 'a.title');
        $orderDirn	= $this->state->get('list.direction', 'asc');
        if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
            $orderCol = 'category_title '.$orderDirn.', a.ordering';
        }
        $this->query->order($this->_db->getEscaped($orderCol.' '.$orderDirn));

        /** pass query object to event */
        $this->dispatcher->trigger('onQueryBeforeQuery', array(&$this->state, &$this->query, &$this->params));

        return $this->query;
    }

    /**
     * getPagination
     *
     * Method to get a JPagination object for the data set.
     *
     * @return	object	A JPagination object for the data set.
     * @since	1.0
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
        $limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
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
     * @return	integer	The total number of items available in the data set.
     * @since	1.0
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
		$this->_db->setQuery($this->getListQueryCache());
		$this->_db->query();

		$total = (int) $this->_db->getNumRows();

        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
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
     * @return	integer	The starting number of items available in the data set.
     * @since	1.0
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
     * Method to get a unique store id based on model configuration state.
     *
     * @param	string		$id	A prefix for the store id.
     *
     * @return	string		A store id.
     * @since	1.0
     */
    protected function getStoreId ($id = '')
    {
        $id = ':'.$this->getState('filter.search');

        for ($i=1; $i < 1000; $i++) {
            $temp = $this->params->def($this->filterFieldName.$i);
            $filterName = substr($temp, 0, stripos($temp, ';'));
            $filterDataType = substr($temp, stripos($temp, ';') + 1, 1);
            if ($filterName == null) {
                break;
            } else {
                $id .= ':'.$this->getState('filter.'.$filterName);
            }
        }

        $id .= ':'.$this->getState('filter.layout');

        $id .= ':'.$this->getState('list.start');
        $id .= ':'.$this->getState('list.limit');
        $id .= ':'.$this->getState('list.ordering');
        $id .= ':'.$this->getState('list.direction');

        return md5($this->context.':'.$id);
    }

    /**
     * getAuthors
     *
     * Build a list of authors
     *
     * @return	JDatabaseQuery
     * @since	1.0
     */
    public function getAuthors()
    {
        $this->query = $this->_db->getQuery(true);

        $this->query->select('u.id AS value, u.name AS text');
        $this->query->from('#__users AS u');
        $this->query->join('INNER', $this->_db->namequote('#'.$this->request['component_table']).' AS c ON c.created_by = u.id');
        $this->query->group('u.id');
        $this->query->order('u.name');

        $this->_db->setQuery($this->query->__toString());

        return $this->_db->loadObjectList();
    }

    /**
     * getMonthsCreate
     *
     * Build a list of created date months in content
     *
     * @return	JDatabaseQuery
     * @since	1.0
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
     * @return	JDatabaseQuery
     * @since	1.0
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
     * @return	JDatabaseQuery
     * @since	1.0
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
     * @return	JDatabaseQuery
     * @since	1.0
     */
    public function getMonthsPublish($table = null)
    {
        return $this->getMonths('publish_up', $table);
    }

   /**
     * getMonths
     *
     * Build a list of months in content
     *
     * @return	JDatabaseQuery
     * @since	1.0
     */
    public function getMonths($columnName, $table = null)
    {
        $this->query = $this->_db->getQuery(true);

        $this->query->select('DISTINCT CONCAT(SUBSTRING(a.'.$this->_db->namequote($columnName).', 1, 4),
                                            SUBSTRING(a.'.$this->_db->namequote($columnName).', 6, 2)) AS value,
                                            SUBSTRING(a.'.$this->_db->namequote($columnName).', 1, 7) AS text');

        if ($table == null) {
            $this->queryTable = '#'.$this->request['component_table'];
        } else {
            $this->queryTable = $table;
        }
        $this->query->from($this->_db->namequote($this->queryTable).' AS a');
        $this->query->group('SUBSTRING(a.'.$this->_db->namequote($columnName).', 1, 4),
                                            SUBSTRING(a.'.$this->_db->namequote($columnName).', 6, 2),
                                            SUBSTRING(a.'.$this->_db->namequote($columnName).', 1, 7)');
        $this->query->order('SUBSTRING(a.'.$this->_db->namequote($columnName).', 1, 7)');

        $this->_db->setQuery($this->query->__toString());

        return $this->_db->loadObjectList();
    }

    /**
     * getOptionList
     *
     * Return Query results for two fields
     *
     * @param string $field1
     * @param string $field2
     * @param boolean $showKey
     * @param boolean $showKeyFirst
     * @param string $table
     * @return object query results
     */
    public function getOptionList($field1, $field2, $showKey = false, $showKeyFirst = false, $table  = null)
    {
        $this->params = MolajoComponentHelper::getParams($this->request['option']);

        $this->query = $this->_db->getQuery(true);

        /** select **/
        if ($showKey == true) {
            if ($showKeyFirst == true) {
                $fieldArray2 = 'CONCAT('.$this->_db->namequote($field1).', ": ",'.$this->_db->namequote($field2).' )';
            } else {
                $fieldArray2 = 'CONCAT('.$this->_db->namequote($field2).', " (",'.$this->_db->namequote($field1).', ")")';
            }
        } else {
             $fieldArray2 = $field2;
        }
        $this->query->select('DISTINCT '.$this->_db->namequote($field1).' AS value, '.$fieldArray2.' as text');

        /** from **/
        if ($table == null) {
            $this->queryTable = '#'.$this->request['component_table'];
        } else {
            $this->queryTable = $table;
        }
        $this->query->from($this->_db->namequote($this->queryTable).' AS a');

        /** where **/
        $this->filterFieldName = JRequest::getCmd('filterFieldName', 'config_manager_list_filters').'_query_filters';

        for ($i=1; $i < 1000; $i++) {

            $filterName = $this->params->def($this->filterFieldName.$i);

            /** end of filter processing **/
            if ($filterName == null) { break; }

            /** configuration option not selected **/
            if ($filterName == '0') {

            } else if ($filterName == $field2) {    

            /** process selected filter (only where clause) **/
            } else {
                $this->setQueryInformation ($filterName,  true);
            }
        }

        /** group by **/
        $this->query->group($field1, $fieldArray2);

        /** order by **/
        if ($showKey == true && $showKeyFirst == true) {
            $this->query->order($field1);
        } else {
            $this->query->order($field2);
        }

        /** run query and return results **/
        $this->_db->setQuery($this->query->__toString());
        return $this->_db->loadObjectList();
    }

    /**
     * setQueryInformation
     *
     * @param string $fieldName
     * @param boolean $onlyWhereClause - true - all query parts; false - only where clause
     * @return sets $query object
     */
    public function setQueryInformation ($fieldname, $onlyWhereClause=false)
    {
        $selectedState = $this->getState('filter.state');
        $fieldClassName = 'MolajoField'.ucfirst($fieldname);
        $this->molajoField->requireFieldClassFile ($fieldname, false);

        if (class_exists($fieldClassName)) {
            $value = $this->getState('filter.'.$fieldname);
            $molajoSpecificFieldClass = new $fieldClassName();
            $molajoSpecificFieldClass->getQueryInformation($this->query, $value, $selectedState, $onlyWhereClause, $this->request['view']);

        } else {
            if ($onlyWhereClause === true) {
                MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_FIELD_CLASS').' '.$fieldClassName, 'error');
                return false;
            } else {
                $this->query->select('a.'.$fieldname);
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
        $this->query = $this->_db->getQuery(true);

        $this->query->select('DISTINCT '.$this->_db->namequote($columnName).' as value');

        if ($table == null) {
            $this->query->from($this->_db->namequote('#'.$this->request['component_table']));
        } else {
            $this->query->from($this->_db->namequote($table));
        }

        if ($valueType == 'numeric') {
            $this->query->where($this->_db->namequote($columnName).' = '.(int) $value);
        } else {
            $this->query->where($this->_db->namequote($columnName).' = '.$this->_db->quote($value));
        }

        $this->_db->setQuery($this->query->__toString());

        if (!$results = $this->_db->loadObjectList()) {
            MolajoFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
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
     * @since	1.0
     */
    public function checkCategories ($categoryArray)
    {
        $this->query = $this->_db->getQuery(true);

        $this->query->select('DISTINCT id');
        $this->query->from($this->_db->namequote('#__categories'));

        /** category array **/
        JArrayHelper::toInteger($categoryArray);
        if (empty($categoryArray)) {
            return;
        }
        $this->query->where($this->_db->namequote('id').' IN ('.$categoryArray.')');
        $this->query->where($this->_db->namequote('extension').' = '.$this->_db->quote($this->request['option']));

        $this->_db->setQuery($this->query->__toString());

        if (!$results = $this->_db->loadObjectList()) {
            MolajoFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            return true;
        }

        return false;
    }

    /**
     * getTable
     *
     * Returns a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     *
     * @return	MolajoTable	A database object
    */
    public function getTable($type='', $prefix='', $config = array())
    {
        return MolajoTable::getInstance($type=ucfirst($this->request['view']),
                                   $prefix=ucfirst($this->request['view'].'Table'),
                                   $config);
    }

    /**
     * saveSelective
     *
     * Not using this right now, may choose to later
     *
     * This code restricts the select list to a required list, those items specified for the filter and for the specific layout
     *
     * Could help with speed (a bit)
     *
    */
    private function saveSelective ()
    {
        /** 1: required fields **/
        $fieldArray = array();
        $requireList = 'id,title,alias,state,catid,created_by,access,checked_out,checked_out_time,search';
        $requiredArray = explode(',', $requireList);

        foreach ($requiredArray as $required) {
            if (in_array($required, $fieldArray)) {
            } else {
                $fieldArray[] = $required;
            }
        }

        /** 2: selected for display **/
        $this->filterFieldName = JRequest::getCmd('selectFieldName', 'config_manager_grid_column');

        for ($i=1; $i < 1000; $i++) {

            $fieldName = $this->params->get($this->filterFieldName.$i);

            /** end of filter processing **/
            if ($fieldName == null) { break; }

            /** configuration option not selected **/
            if ($fieldName == '0') {

            /** selected twice by user in configuration **/
            } else if (in_array($fieldName, $fieldArray)) {

            /** store for filtering and then processing **/
            } else {
                $fieldArray[] = $fieldName;
            }
        }

        /** 3: selected as a filter **/
        $this->filterFieldName = JRequest::getCmd('filterFieldName', 'config_manager_list_filters');

        for ($i=1; $i < 1000; $i++) {

            $fieldName = $this->params->def($this->filterFieldName.$i);

            /** end of filter processing **/
            if ($fieldName == null) { break; }

            /** configuration option not selected **/
            if ($fieldName == '0') {

            /** listed twice, ignore after first use **/
            } else if (in_array($fieldName, $fieldArray)) {

            /** process selected field **/
            } else {
                $fieldArray[] = $fieldName;
            }
        }

        /** filter by known field names and append into query object **/
        foreach ($fieldArray as $fieldName) {
            if ((in_array($fieldName, $this->tableFieldList)) || $fieldName == 'search') {
                $this->setQueryInformation ($fieldName, false);
            }
        }
    }
}