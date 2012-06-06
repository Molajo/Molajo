<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Metadata;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Metadata
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class MetadataService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Metadatas
     *
     * @var    array
     * @since  1.0
     */
    protected $metadatas = array();

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MetadataService();
        }

        return self::$instance;
    }

    /**
     * Set the system metadata.
     *
     * @param string  $metadata
     * @param string  $type  metadata, notice, warning, and error
     * @param integer $code
     *
     * @return bool
     * @since   1.0
     */
    public function set()
    {
        return true;
    }

    /**
     * get application metadatas
     *
     * @return array Application metadatas
     *
     * @since   1.0
     */
    public function get($option = null)
    {
        if ($option == 'db') {
            return $this;

        } elseif ($option == 'count') {
            return count($this->metadatas);

        } else {
            return $this;
        }
    }

    /**
     *     Dummy functions to pass service off as a DBO to interact with model
     */
    public function getNullDate()
    {
        return $this;
    }

    public function getQuery()
    {
        return $this;
    }

    public function toSql()
    {
        return $this;
    }

    public function clear()
    {
        return $this;
    }

    /**
     * getData
     *
     * @return array
     *
     * @since    1.0
     */
    public function getMetadatas()
    {
        $query_results = array();

        $defer = (int) Services::Registry()->get('Parameters', 'defer', 0);

        /** get metadata (part used in base) */
        if ($defer == 1) {
        } else {
            $metadata = Services::Registry()->get('Metadata');

            if (count($metadata) > 0) {

				Services::Registry()->set('Metadatas', 'mime_type', $type);

                $row = new \stdClass();
                $row->type = 'base';

                /** Title */
                $title = $metadata['title'];
                if (trim($title) == '') {
                    $title = Services::Registry()->get('Configuration', 'title', 'Molajo');
                }
                $row->title = Services::Filter()->escape_text($title);

                /** Mimetype */
                $mimetype = Services::Registry()->get('Metadata', 'mime_encoding');
                if (trim($mimetype) == '') {
                    $mimetype = 'text/html';
                }
                $row->mimetype = Services::Filter()->escape_text($mimetype);

                /** Base URL for Site */
                $row->base = Services::Registry()->get('Configuration', 'site_base_url');

                /** Last Modified Date */
                $last_modified = Services::Registry()->get('Parameters', 'modified_datetime');
                if (trim($last_modified) == '') {
                    $last_modified = Services::Date()->getDate();
                }
                $row->last_modified = Services::Filter()->escape_text($last_modified);

                $query_results[] = $row;
            }

            /** metadata */
            if (count($metadata) > 0) {

                foreach ($metadata as $name => $content) {

                        //				if ($type == 'http-equiv') {
                        //					$content .= '; charset=' . $document->getCharset();
                        //					$buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
                        //				} else {
                        if (trim($content) == '') {
                        } else {
                            $row = new \stdClass();
                            $row->type = 'metadata';
                            $row->name = Services::Filter()->escape_text($name);
                            $row->content = Services::Filter()->escape_text($content);
                            $query_results[] = $row;
                        }
                        //				}
                    }
                }

        }

            $list = Services::Registry()->get('Metadatas', 'Links', array());

		$list = Services::Registry()->get('Metadatas', 'Css', array());

		$cssPriorities = Services::Registry()->set('Metadatas', 'CssPriorities', array());

		$css = Services::Registry()->get('Metadatas', 'CssDeclarations', array());


        /** type: js */
        $list = Services::Asset()->get_js($defer);



        /** type: js_declarations */
		$query_results = Services::Asset()->get_js_declarations($defer);

        return $query_results;
    }
}
