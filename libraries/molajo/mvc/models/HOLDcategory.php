<?php
/**
 * @version     $id: category.php
 * @package     Molajo
 * @subpackage  Category Model
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Category Model
 *
 * @package    Molajo
 * @subpackage    Model
 * @since 1.0
 */
class MolajoModelCategory extends JModel
{
    /**
     * Category items data
     *
     * @var array
     */
    protected $_item = null;

    protected $_articles = null;

    protected $_siblings = null;

    protected $_children = null;

    protected $_parent = null;

    /**
     * Model context string.
     *
     * @var        string
     */
    protected $_context = 'com_articles.category';

    /**
     * The category that applies.
     *
     * @access    protected
     * @var        object
     */
    protected $_category = null;

    /**
     * The list of other newfeed categories.
     *
     * @access    protected
     * @var        array
     */
    protected $_categories = null;

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.0
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * return    void
     * @since    1.0
     */
    protected function populateState($ordering = null, $direction = null)
    {
        /** Molajo_note: go thru app and make certain catid is always used for category id, never id */
        $this->setState('category.id', JRequest::getInt('id'));

        /** Merge Component and Menu Item Parameters */
        $parameters = MolajoFactory::getApplication()->getParams();
        $menuParams = new JRegistry;
        if ($menu = MolajoFactory::getApplication()->getMenu()->getActive()) {
            $menuParams->loadJSON($menu->parameters);
        }
        $mergedParams = clone $menuParams;
        $mergedParams->merge($parameters);
        $this->setState('parameters', $mergedParams);

        /** show_noauth: show titles for unauthorized content in blog and list layouts */
        if ($parameters->get('show_noauth')) {
            $this->setState('filter.access', false);
        } else {
            $this->setState('filter.access', true);
        }

        // Optional filter text
        $this->setState('list.filter', JRequest::getString('filter-search'));

        // filter.order
        $itemid = JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
        $orderCol = MolajoFactory::getApplication()->getUserStateFromRequest('com_articles.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
        if (!in_array($orderCol, $this->filter_fields)) {
            $orderCol = 'a.ordering';
        }
        $this->setState('list.ordering', $orderCol);

        $listOrder = MolajoFactory::getApplication()->getUserStateFromRequest('com_articles.category.list.' . $itemid . '.filter_order_Dir',
                                                                              'filter_order_Dir', '', 'cmd');
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
            $listOrder = 'ASC';
        }
        $this->setState('list.direction', $listOrder);

        $this->setState('list.start', JRequest::getVar('limitstart', 0, '', 'int'));

        // set limit for query. If list, use parameter. If blog, add blog parameters for limit.
        if (JRequest::getString('layout') == 'blog') {
            $limit = $parameters->get('num_leading_articles') + $parameters->get('num_intro_articles') + $parameters->get('num_links');
            $this->setState('list.links', $parameters->get('num_links'));
        }
        else {
            $limit = MolajoFactory::getApplication()->getUserStateFromRequest('com_articles.category.list.' . $itemid . '.limit', 'limit', $parameters->get('display_num'));
        }

        $this->setState('list.limit', $limit);

        // set the depth of the category query based on parameter
        $showSubcategories = $parameters->get('show_subcategory_content', '0');

        if ($showSubcategories) {
            $this->setState('filter.max_category_levels', $parameters->get('show_subcategory_content', '1'));
            $this->setState('filter.subcategories', true);
        }

        $this->setState('filter.language', MolajoFactory::getApplication()->getLanguageFilter());

        $this->setState('layout', JRequest::getCmd('layout'));
    }

    /**
     * Get the articles in the category
     *
     * @return    mixed    An array of articles or false if an error occurs.
     * @since    1.5
     */
    function getItems()
    {
        $parameters = $this->getState()->get('parameters');

        // set limit for query. If list, use parameter. If blog, add blog parameters for limit.
        if (JRequest::getString('layout') == 'blog') {
            $limit = $parameters->get('num_leading_articles') + $parameters->get('num_intro_articles') + $parameters->get('num_links');
        }
        else {
            $limit = $this->getState('list.limit');
        }

        if ($this->_articles === null && $category = $this->getCategory()) {
            $model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
            $model->setState('parameters', MolajoFactory::getApplication()->getParams());
            $model->setState('filter.category_id', $category->id);
            $model->setState('filter.published', $this->getState('filter.published'));
            $model->setState('filter.access', $this->getState('filter.access'));
            $model->setState('filter.language', $this->getState('filter.language'));
            $model->setState('list.ordering', $this->_buildContentOrderBy());
            $model->setState('list.start', $this->getState('list.start'));
            $model->setState('list.limit', $limit);
            $model->setState('list.direction', $this->getState('list.direction'));
            $model->setState('list.filter', $this->getState('list.filter'));
            // filter.subcategories indicates whether to include articles from subcategories in the list or blog
            $model->setState('filter.subcategories', $this->getState('filter.subcategories'));
            $model->setState('filter.max_category_levels', $this->setState('filter.max_category_levels'));
            $model->setState('list.links', $this->getState('list.links'));

            if ($limit >= 0) {
                $this->_articles = $model->getItems();

                if ($this->_articles === false) {
                    $this->setError($model->getError());
                }
            }
            else {
                $this->_articles = array();
            }

            $this->_pagination = $model->getPagination();
        }

        return $this->_articles;
    }

    /**
     * Build the orderby for the query
     *
     * @return    string    $orderby portion of query
     * @since    1.5
     */
    protected function _buildContentOrderBy()
    {
        $db = $this->getDbo();
        $parameters = $this->state->parameters;
        $itemid = JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
        $orderCol = MolajoFactory::getApplication()->getUserStateFromRequest('com_articles.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
        $orderDirn = MolajoFactory::getApplication()->getUserStateFromRequest('com_articles.category.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
        $orderby = ' ';

        if (!in_array($orderCol, $this->filter_fields)) {
            $orderCol = null;
        }

        if (!in_array(strtoupper($orderDirn), array('ASC', 'DESC', ''))) {
            $orderDirn = 'ASC';
        }

        if ($orderCol && $orderDirn) {
            $orderby .= $db->getEscaped($orderCol) . ' ' . $db->getEscaped($orderDirn) . ', ';
        }

        $articleOrderby = $parameters->get('orderby_sec', 'rdate');
        $articleOrderDate = $parameters->get('order_date');
        $categoryOrderby = $parameters->def('orderby_pri', '');
        $secondary = ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
        $primary = ContentHelperQuery::orderbyPrimary($categoryOrderby);

        $orderby .= $db->getEscaped($primary) . ' ' . $db->getEscaped($secondary) . ' a.created ';

        return $orderby;
    }

    public function getPagination()
    {
        if (empty($this->_pagination)) {
            return null;
        }
        return $this->_pagination;
    }

    /**
     * Method to get category data for the current category
     *
     * @param    int        An optional ID
     *
     * @return    object
     * @since    1.5
     */
    public function getCategory()
    {
        if (!is_object($this->_item)) {
            if (isset($this->state->parameters)) {
                $parameters = $this->state->parameters;
                $options = array();
                $options['countItems'] = $parameters->get('show_cat_num_articles', 1) || !$parameters->get('show_empty_categories_cat', 0);
            }
            else {
                $options['countItems'] = 0;
            }

            $categories = JCategories::getInstance('Content', $options);
            $this->_item = $categories->get($this->getState('category.id', 'root'));

            // Compute selected asset permissions.
            if (is_object($this->_item)) {
                $user = MolajoFactory::getUser();
                $userId = MolajoFactory::getUser()->get('id');
                $asset = 'com_articles.category.' . $this->_item->id;

                // Check general create permission.
                if (MolajoFactory::getUser()->authorise('create', $asset)) {
                    $this->_item->getParams()->set('access-create', true);
                }

                // TODO: Why aren't we lazy loading the children and siblings?
                $this->_children = $this->_item->getChildren();
                $this->_parent = false;

                if ($this->_item->getParent()) {
                    $this->_parent = $this->_item->getParent();
                }

                $this->_rightsibling = $this->_item->getSibling();
                $this->_leftsibling = $this->_item->getSibling(false);
            }
            else {
                $this->_children = false;
                $this->_parent = false;
            }
        }

        return $this->_item;
    }

    /**
     * Get the parent categorie.
     *
     * @param    int        An optional category id. If not supplied, the model state 'category.id' will be used.
     *
     * @return    mixed    An array of categories or false if an error occurs.
     * @since    1.0
     */
    public function getParent()
    {
        if (!is_object($this->_item)) {
            $this->getCategory();
        }

        return $this->_parent;
    }

    /**
     * Get the left sibling (adjacent) categories.
     *
     * @return    mixed    An array of categories or false if an error occurs.
     * @since    1.0
     */
    function &getLeftSibling()
    {
        if (!is_object($this->_item)) {
            $this->getCategory();
        }

        return $this->_leftsibling;
    }

    /**
     * Get the right sibling (adjacent) categories.
     *
     * @return    mixed    An array of categories or false if an error occurs.
     * @since    1.0
     */
    function &getRightSibling()
    {
        if (!is_object($this->_item)) {
            $this->getCategory();
        }

        return $this->_rightsibling;
    }

    /**
     * Get the child categories.
     *
     * @param    int        An optional category id. If not supplied, the model state 'category.id' will be used.
     *
     * @return    mixed    An array of categories or false if an error occurs.
     * @since    1.0
     */
    function &getChildren()
    {
        if (!is_object($this->_item)) {
            $this->getCategory();
        }

        return $this->_children;
    }

}