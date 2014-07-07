<?php
/**
 * Comments Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use CommonApi\Event\DisplayInterface;
use stdClass;

/**
 * Comments Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CommentsPlugin extends CommentsHeading implements DisplayInterface
{
    /**
     * Retrieve data for the Comments, Commentform, and Comment Template Views
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderView()
    {
        if ($this->processCommentsPlugin() === false) {
            return $this;
        }

        $this->initialiseCommentsPlugin();

        if ($this->getCommentsOpen() === true) {
            $this->plugin_data->comment_open = 1;
        } else {
            $this->plugin_data->comment_open = 0;
        }

        $this->getCommentHeading();
        $this->getComments();
        $this->getCommentForm();

        return $this;
    }

    /**
     * Retrieve data for the Comments, Commentform, and Comment Template Views
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRenderView()
    {
        if (isset($this->parameters->enable_response_comments)
            && isset($this->query_results[0]->id)
        ) {
        } else {
            return $this;
        }

        if ($this->parameters->enable_response_comments === 1) {
        } else {
            return $this;
        }

        $name                                 = 'comments' . $this->query_results[0]->id;
        $this->plugin_data->$name             = new stdClass();
        $this->plugin_data->$name->parameters = $this->parameters;
        $this->plugin_data->$name->data       = $this->query_results;

        return $this;
    }
}
