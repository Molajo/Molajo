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
     * @return  boolean
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        if (strtolower($this->get('template_view_path_node')) == 'comment' ||
            strtolower($this->get('template_view_path_node')) == 'comments' ||
            strtolower($this->get('template_view_path_node')) == 'commentform'
        ) {
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
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(CATALOG_TYPE_RESOURCE_LITERAL, 'Comments');
        $controller->setDataobject();

        $controller->set('get_customfields', 2);
        $controller->set('use_special_joins', 1);
        $controller->set('check_view_level_access', 1);

        /** Connect to Parent */
        $parentController = new $controllerClass();
        $parentController->getModelRegistry($parent_model_type, $parent_model_name);
        $controller->setDataobject();

        $method = 'get' . ucfirst(strtolower($this->get('template_view_path_node')));

        return $this->$method(
            $controller,
            $parentController,
            $parent_model_type,
            $parent_model_name,
            $parent_source_id
        );

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
        $parent_source_id = (int)$this->get('parent_source_id', 0);

        if ($parent_model_type == ''
            || $parent_model_name == ''
            || $parent_source_id == 0
        ) {
            $parent_model_type = Services::Registry()->get('RouteParameters', 'model_type');
            $parent_model_name = Services::Registry()->get('RouteParameters', 'model_name');
            $parent_source_id = (int)Services::Registry()->get('RouteParameters', 'source_id');
        }

        if ($parent_model_type == ''
            || $parent_model_name == ''
            || $parent_source_id == 0
        ) {
            return false;
        }

        echo '<pre>';
        var_dump(array(
            'parent_model_type' => $parent_model_type,
            'parent_model_name' => $parent_model_name,
            'parent_source_id' => $parent_source_id));
die;
        return array(
            'parent_model_type' => $parent_model_type,
            'parent_model_name' => $parent_model_name,
            'parent_source_id' => $parent_source_id
        );
    }

    /**
     * Retrieve Data for Comment Heading
     *
     * @param   $controller
     * @param   $parentController
     * @param   $parent_model_type
     * @param   $parent_model_name
     * @param   $parent_source_id
     *
     * @return  bool
     * @since   1.0
     */
    public function getComment(
        $controller,
        $parentController,
        $parent_model_type,
        $parent_model_name,
        $parent_source_id
    ) {
        $primary_prefix = $controller->get('primary_prefix');

        $controller->model->query->select('count(*)');
        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('root')
                . ' = ' . (int)$parent_source_id
        );

        $count = $controller->getData(QUERY_OBJECT_RESULT);

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

        $open = $this->getCommentsOpen(
            $controller,
            $parentController,
            $parent_model_type,
            $parent_model_name,
            $parent_source_id
        );
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
     * @param   $controller
     * @param   $parentController
     * @param   $parent_model_type
     * @param   $parent_model_name
     * @param   $parent_source_id
     *
     * @return  bool
     * @since   1.0
     */
    public function getComments(
        $controller,
        $parentController,
        $parent_model_type,
        $parent_model_name,
        $parent_source_id
    ) {
        $primary_prefix = $controller->get('primary_prefix');

        $controller->set('root', (int)$parent_source_id);

        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('root')
                . ' = ' . (int)$parent_source_id
        );
        $controller->model->query->order(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('lft')
        );

        $this->data = $controller->getData(QUERY_OBJECT_LIST);

        $open = $this->getCommentsOpen(
            $controller,
            $parentController,
            $parent_model_type,
            $parent_model_name,
            $parent_source_id
        );

        return true;
    }

    /**
     * Retrieve Data for Comment Heading
     *
     * @param   $controller
     * @param   $parentController
     * @param   $parent_model_type
     * @param   $parent_model_name
     * @param   $parent_source_id
     *
     * @return  bool
     * @since   1.0
     */
    public function getCommentform(
        $controller,
        $parentController,
        $parent_model_type,
        $parent_model_name,
        $parent_source_id
    ) {
        $results = array();
        $row = new \stdClass();

        $open = $this->getCommentsOpen(
            $controller,
            $parentController,
            $parent_model_type,
            $parent_model_name,
            $parent_source_id
        );

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

        $this->set('model_type', 'Plugindata');
        $this->set('model_name', 'Edit');
        $this->set('model_query_object', QUERY_OBJECT_ITEM);

        $this->parameters['model_type'] = 'Plugindata';
        $this->parameters['model_name'] = 'Edit';

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
     * @param   $controller
     * @param   $parentController
     * @param   $parent_model_type
     * @param   $parent_model_name
     * @param   $parent_source_id
     *
     * @return  bool
     * @since   1.0
     */
    public function getCommentsOpen(
        $controller,
        $parentController,
        $parent_model_type,
        $parent_model_name,
        $parent_source_id
    ) {
        $primary_prefix = $parentController->get('primary_prefix');

        $parentController->set('id', (int)$parent_source_id);

        $parentController->model->query->select(
            $parentController->model->db->qn($primary_prefix)
                . '.' .
                $parentController->model->db->qn('start_publishing_datetime')
        );

        $published = $parentController->getData(QUERY_OBJECT_RESULT);
        if ($published === false) {
            return false;
        }

        $converted = Services::Date()->convertCCYYMMDD($published);
        if ($converted === false) {
            return false;
        }

        $actual = Services::Date()->getNumberofDaysAgo($converted);

        $open_days = $this->get('enable_response_comment_form_open_days');

        if ($actual > $open_days) {
            return false;
        }

        return true;
    }
}
