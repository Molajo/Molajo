<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Footer
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class BlockquoteModuleModel extends MolajoModel
{
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
     * getItems
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getItems()
    {
        $rows = Molajo::Request()->get('query_results');
        if (count($rows) == 0) {
            return array();
        }

        foreach ($rows as $row) {
        }

        /** search for pullquotes **/
        preg_match_all(
            "#{blockquote}(.*?){/blockquote}#s",
            $row,
            $matches
        );
        if (count($matches[1]) == 0) {
            return array();
        }
        $workText = $row;

        for ($i = 0; $i < count($matches); $i++) {

            /** model **/
            $excerpt = substr($matches[0][$i], 12, strlen($matches[0][$i]) - 25);
            $unique = $i;

            /** cite: extract from blockquote **/
            preg_match("#{cite}(.*?){/cite}#s", $excerpt, $matchCite);

            if (count($matchCite) > 0) {
                $cite = $matchCite[1];
                $excerpt = str_replace($matchCite[0], '', $excerpt);
            } else {
                $cite = '';
            }

            /** layout **/
            $item = array();
            $item->cite = $cite;
            $item->excerpt = $excerpt;

            /** replace **/
            $workText = str_replace($matches[0][$i], $renderedLayout, $workText);
        }
        /** update source **/
        $row = $workText;

        return;
    }
}
