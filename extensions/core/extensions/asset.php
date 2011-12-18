<?php
/**
 * @package     Molajo
 * @subpackage  Asset
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Asset Class
 *
 * Base class
 */
class MolajoAsset
{
    /**
     *  Option
     *
     * @var string
     * @since 1.0
     */
    protected $option = null;

    /**
     *  View
     *
     * @var string
     * @since 1.0
     */
    protected $view = null;

    /**
     *  Task
     *
     * @var string
     * @since 1.0
     */
    protected $task = null;

    /**
     *  Layout
     *
     * @var string
     * @since 1.0
     */
    protected $layout = null;

    /**
     *  Id
     *
     * @var string
     * @since 1.0
     */
    protected $id = null;

    /**
     *  Template
     *
     * @var string
     * @since 1.0
     */
    protected $template = null;

    /**
     *  Template Page
     *
     * @var string
     * @since 1.0
     */
    protected $template_page = null;

    /**
     *  Asset
     *
     * @var string
     * @since 1.0
     */
    protected $asset = null;

    /**
     *  Asset Type ID
     *
     * @var string
     * @since 1.0
     */
    protected $asset_type_id = null;

    /**
     *  Source Table
     *
     * @var string
     * @since 1.0
     */
    protected $source_table = null;

    /**
     *  Source ID
     *
     * @var string
     * @since 1.0
     */
    protected $source_id = null;

    /**
     *  Primary Category ID
     *
     * @var string
     * @since 1.0
     */
    protected $primary_category_id = null;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   mixed  $id    An optional argument to provide dependency injection for the asset
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($request, $id = null)
    {
        $results = $this->getAsset($request, $id);
    }

    /**
     *  Function to retrieve asset information given the request
     *
     * @param   string   $uri
     *
     * @return  array
     * @since   11.1
     */
    public function getAsset($request, $id = null)
    {
        $sef = MolajoFactory::getApplication()->get('sef');
        $sef_rewrite = MolajoFactory::getApplication()->get('sef_rewrite');
        $sef_suffix = MolajoFactory::getApplication()->get('sef_suffix');
        $unicodeslugs = MolajoFactory::getApplication()->get('unicodeslugs');

        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.'.$db->nameQuote('id').' as asset_id');
        $query->select($db->nameQuote('source_id'));
        $query->select($db->nameQuote('primary_category_id'));
        $query->select($db->nameQuote('template_id'));
        $query->select($db->nameQuote('template_page'));
        $query->select($db->nameQuote('language'));
        $query->select($db->nameQuote('translation_of_id'));
        $query->select($db->nameQuote('redirect_to_id'));
        $query->select($db->nameQuote('view_group_id'));
        $query->select($db->nameQuote('primary_category_id'));

        $query->select('b.'.$db->nameQuote('component_option').' as option');
        $query->select('b.'.$db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets').' as a');
        $query->from($db->nameQuote('#__asset_types').' as b');

        $query->where('a.asset_type_id = b.id');

        if ($id == null) {
            if (MolajoFactory::getApplication()->get('sef') == 1) {
                $query->where($db->nameQuote('sef_request').' = ' . $db->Quote($request));
            } else {
                $query->where($db->nameQuote('request').' = ' . $db->Quote($request));
            }
        } else {
            $query->where($db->nameQuote('id').' = ' . (int) $id);
        }

        $results = $db->setQuery($query->__toString());
echo '<pre>';var_dump($results);'</pre>';

        if ($results = $db->loadObjectList()) {
        } else {
            MolajoFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
            return false;
        }

        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->option = $result->option_value_literal;
                $this->view = $result->option_value_literal;
                $this->task = $result->option_value_literal;
                $this->layout = $result->option_value_literal;
                $this->id  = $result->option_value_literal;
                $this->template = $result->option_value_literal;
                $this->template_page = $result->option_value_literal;
                $this->asset = $result->asset_id;
                $this->asset_type_id = $result->option_value_literal;
                $this->source_table = $result->option_value_literal;
                $this->source_id = $result->source_id;
                $this->language = $result->option_value_literal;
                $this->translation_of_id = $result->option_value_literal;
                $this->redirect_to_id = $result->option_value_literal;
                $this->view_group_id = $result->option_value_literal;
                $this->primary_category_id = $result->primary_category_id;

                return $result->option_value_literal;
            }
        }

    }
}