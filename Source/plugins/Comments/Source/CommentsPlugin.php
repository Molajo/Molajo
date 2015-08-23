<?php
/**
 * Comments Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Comments;

use CommonApi\Event\DisplayEventInterface;
use stdClass;

/**
 * Comments Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class CommentsPlugin extends Process implements DisplayEventInterface
{
    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processPlugin();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        $methods = array(
            'checkProcessPluginExtensionTitle',
            'checkProcessPluginSourceId'
        );

        $this->comments_form = '';

        foreach ($methods as $method) {

            if ($this->$method() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Should plugin be executed based on Extension Title?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPluginExtensionTitle()
    {
        $valid_titles = array('comments', 'comments_form', 'comments_heading', 'comments_list');
        $test_title = strtolower($this->controller['parameters']->token->name);

        if (in_array($test_title, $valid_titles)) {
        } else {
            return false;
        }

        $this->comments_view = $test_title;

        return true;
    }

    /**
     * Should plugin be executed based on Source ID?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPluginSourceId()
    {
        if (isset($this->controller['parameters']->token->attributes['source_id'])) {
            $this->source_id = (int)$this->controller['parameters']->token->attributes['source_id'];
        } else {
            return false;
        }

        if ($this->source_id === 0) {
            return false;
        }

        return true;
    }

    /**
     * Set plugin data for the Comment Heading, Form, and List Template Views
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        $this->setCommentsName();

        if ($this->comments_view === 'comments_heading') {
            return $this->getCommentsHeading();

        } elseif ($this->comments_view === 'comments_form') {
            return $this->getCommentsForm();

        } elseif ($this->comments_view === 'comments_list') {
            return $this->getCommentsData();
        }

        if ($this->getCacheComments() === true) {
            $this->getComments();
            return $this;
        }

        $this->initialiseCommentsPlugin();

        $this->setCommentData();

        $this->setCacheComments();

        return $this;
    }

    /**
     * Set names of comments objects
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCommentsName()
    {
        $this->comments_name    = 'comments_' . (int)$this->source_id;
        $this->comments_form    = $this->comments_name . '_form';
        $this->comments_heading = $this->comments_name . '_heading';
        $this->comments_list    = $this->comments_name . '_list';

        return $this;
    }

    /**
     * Check if cached version of comments are available
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function getCacheComments()
    {
        if ($this->usePluginCache() === true) {
        } else {
            return false;
        }

        $cache_item = $this->getPluginCache($this->comments_name);

        if ($cache_item->isHit() === true) {
        } else {
            return false;
        }

        $cache = $cache_item->getValue();

        $this->plugin_data->{$this->comments_name}    = $cache->{$this->comments_name};
        $this->plugin_data->{$this->comments_form}    = $cache->{$this->comments_form};
        $this->plugin_data->{$this->comments_heading} = $cache->{$this->comments_heading};
        $this->plugin_data->{$this->comments_list}    = $cache->{$this->comments_list};

        return true;
    }

    /**
     * Save cached version of comments if enabled
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCacheComments()
    {
        if ($this->usePluginCache() === true) {
        } else {
            return $this;
        }

        $cache                            = new stdClass();
        $cache->{$this->comments_name}    = $this->plugin_data->{$this->comments_name};
        $cache->{$this->comments_form}    = $this->plugin_data->{$this->comments_form};
        $cache->{$this->comments_heading} = $this->plugin_data->{$this->comments_heading};
        $cache->{$this->comments_list}    = $this->plugin_data->{$this->comments_list};

        $this->setPluginCache($this->comments_name, $cache);

        return $this;
    }
}
