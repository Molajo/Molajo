<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Comment;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CommentPlugin extends Plugin
{
    /**
     * Retrieve Comment for Resource
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if (strtolower($this->get('template_view_path_node')) == 'comment' ||
            strtolower($this->get('template_view_path_node')) == 'comments' ||
            strtolower($this->get('template_view_path_node')) == 'commentform') {
        } else {
            return true;
        }

        $results = $this->getParentKeys();

        if ($results === false) {
            return true;
        }

        $parent_model_type = $results['parent_model_type'];
        $parent_model_name = $results['parent_model_name'];
        $parent_source_id = $results['parent_source_id'];

        /** Connect to Comment Resource */
        $controllerClass = 'Molajo\\MVC\\Controller\\Controller';
        $connect = new $controllerClass();
        $results = $connect->connect('Resource', 'Comments');
        if ($results === false) {
            return false;
        }

        $connect->set('get_customfields', 2);
        $connect->set('use_special_joins', 1);
        $connect->set('check_view_level_access', 1);

        /** Connect to Parent */
        $parentConnect = new $controllerClass();
        $results = $parentConnect->connect($parent_model_type, $parent_model_name);
        if ($results === false) {
            return false;
        }

        $method = 'get' . ucfirst(strtolower($this->get('template_view_path_node')));

        return $this->$method($connect, $parentConnect,
            $parent_model_type, $parent_model_name, $parent_source_id);

    }

    /**
     * getParentKeys - retrieve the values which identify the parent for the requested comments
     *
     * If comments are required for content that is not the primary request, the parent variables
     * can be defined on the include statement, as shown below:
     *
     * Note: Include statements must not break on multiple lines
     *
     * <include:template name=Comment wrap=none parent_model_type=<?php echo Services::Registry()->get('Parameters', 'catalog_model_type'); ?> parent_model_name=<?php echo Services::Registry()->get('Parameters', 'catalog_model_name'); ?> parent_source_id=<?php echo Services::Registry()->get('Parameters', 'catalog_source_id'); ?>/>
     *
     * @return array|bool
     */
    public function getParentKeys()
    {
        $parent_model_type = $this->get('parent_model_type', '');
        $parent_model_name = $this->get('parent_model_name', '');
        $parent_source_id = (int) $this->get('parent_source_id', 0);

        if ($parent_model_type == '' || $parent_model_name == '' || $parent_source_id == 0) {
            $parent_model_type = Services::Registry()->get('RouteParameters', 'catalog_model_type');
            $parent_model_name = Services::Registry()->get('RouteParameters', 'catalog_model_name');
            $parent_source_id = (int) Services::Registry()->get('RouteParameters', 'catalog_source_id');
        }

        if ($parent_model_type == '' || $parent_model_name == '' || $parent_source_id == 0) {
            return false;
        }

        return array('parent_model_type' => $parent_model_type,
            'parent_model_name' => $parent_model_name,
            'parent_source_id' => $parent_source_id);
    }

    /**
     * Retrieve Data for Comment Heading
     *
     * @param $connect
     * @param $parentConnect
     * @param $parent_model_type
     * @param $parent_model_name
     * @param $parent_source_id
     *
     * @return bool
     * @since  1.0
     */
    public function getComment($connect, $parentConnect, $parent_model_type, $parent_model_name, $parent_source_id)
    {
        $primary_prefix = $connect->get('primary_prefix');

        $connect->model->query->select('count(*)');
        $connect->model->query->where(
            $connect->model->db->qn($primary_prefix)
                . '.' . $connect->model->db->qn('root')
                . ' = ' . (int) $parent_source_id
        );

        $count = $connect->getData('result');

        $results = array();
        $row = new \stdClass();
        $row->count_of_comments = $count;

        if ($count == 0) {
            $row->title = Services::Language()->translate('COMMENTS_TITLE_NO_COMMENTS');
            $row->content_text = Services::Language()->translate('COMMENTS_TEXT_NO_COMMENTS');

        } else {
            $row->title = Services::Language()->translate('COMMENTS_TITLE_HAS_COMMENTS');
            $row->content_text = Services::Language()->translate('COMMENTS_TEXT_HAS_COMMENTS');
        }

        $open = $this->getCommentsOpen($parentConnect, $parent_source_id);
        if ($open === false) {
            $row->closed_comment = Services::Language()->translate('COMMENTS_ARE_CLOSED');
            $row->closed = 1;
        } else {
            $row->closed_comment = '';
            $row->closed = 0;
        }

        $results[] = $row;
        $this->data = $results;

        return true;
    }

    /**
     * Retrieve Comments
     *
     * @param $connect
     * @param $parentConnect
     * @param $parent_model_type
     * @param $parent_model_name
     * @param $parent_source_id
     *
     * @return bool
     * @since  1.0
     */
    public function getComments($connect, $parentConnect, $parent_model_type, $parent_model_name, $parent_source_id)
    {
        $primary_prefix = $connect->get('primary_prefix');

        $connect->set('root', (int) $parent_source_id);

        $connect->model->query->where(
            $connect->model->db->qn($primary_prefix)
                . '.' . $connect->model->db->qn('root')
                . ' = ' . (int) $parent_source_id
        );
        $connect->model->query->order(
            $connect->model->db->qn($primary_prefix)
                . '.' . $connect->model->db->qn('lft')
        );

        $this->data = $connect->getData('list');

        $open = $this->getCommentsOpen($parentConnect, $parent_source_id);

        return true;
    }

    /**
     * Retrieve Data for Comment Heading
     *
     * @param $connect
     * @param $parentConnect
     * @param $parent_model_type
     * @param $parent_model_name
     * @param $parent_source_id
     *
     * @return bool
     * @since  1.0
     */
    public function getCommentform($connect, $parentConnect, $parent_model_type, $parent_model_name, $parent_source_id)
    {
        $results = array();
        $row = new \stdClass();

        $open = $this->getCommentsOpen($parentConnect, $parent_source_id);
        if ($open === false) {
            $row->closed_comment = Services::Language()->translate('COMMENTS_ARE_CLOSED');
            $row->closed = 1;
        } else {
            $row->closed_comment = '';
            $row->closed = 0;
        }

        $row->parent_model_type = $parent_model_type;
        $row->parent_model_name = $parent_model_name;
        $row->parent_source_id = $parent_source_id;

        $results[] = $row;
        $this->data = $results;

        return true;

        /** Get configuration menuitem settings for this resource */
//		$menuitem = Helpers::Content()->getResourceMenuitemParameters('Configuration', 17000);

        /** Create Tabs */
        $namespace = 'Comments';

        $page_array = Services::Registry()->get('ConfigurationMenuitemParameters', 'commentform_page_array');
        $page_array = '{{Comments,visitor*,email*,website*,ip*,spam*}}';

        /*
        visitor_name
        email_address
        website
        ip_address
        spam_protection
        */

        $tabs = Services::Form()->setPageArray(
            'System',
            'Comments',
            'Comments',
            $page_array,
            'comments_page_',
            'Comment',
            'Commenttab',
            17000,
            array()
        );

        $this->set('model_name', 'Plugindata');
        $this->set('model_type', 'dbo');
        $this->set('model_query_object', 'getPlugindata');
        $this->set('model_parameter', 'Edit');

        $this->parameters['model_name'] = 'Plugindata';
        $this->parameters['model_type'] = 'dbo';

        Services::Registry()->set('Plugindata', 'Commentform', $tabs);

        echo '<pre>';
        var_dump($tabs);
        echo '</pre>';

        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata', 'Commentform'));
        echo '</pre>';
        die;

        return true;
    }

    /**
     * Determine if Comments are still open for Content
     *
     * @param $parentConnect
     * @param $parent_source_id
     *
     * @return string
     * @since  1.0
     */
    public function getCommentsOpen($parentConnect, $parent_source_id)
    {
        $primary_prefix = $parentConnect->get('primary_prefix');

        $parentConnect->set('id', (int) $parent_source_id);

        $parentConnect->model->query->select(
            $parentConnect->model->db->qn($primary_prefix)
                . '.' .
                $parentConnect->model->db->qn('start_publishing_datetime')
        );

        $published = $parentConnect->getData('result');
        if ($published === false) {
            return false;
        }

        $converted = Services::Date()->convertCCYYMMDD($published);
        if ($converted === false) {
            return false;
        }

        $actual = Services::Date()->getNumberofDaysAgo($converted);

        $open_days = $this->get('enable_response_comment_form_open_days');

        if ($actual >  $open_days) {
            return false;
        }

        return true;
    }
}
