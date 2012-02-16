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
class MolajoBlockquoteModuleModel extends MolajoDisplayModel
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $config
     * @since  1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table = '';
        $this->primary_key = '';

        return parent::__construct($id);
    }

    /**
     * getData
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getData()
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
$renderedLayout = '';
            /** replace **/
            $workText = str_replace($matches[0][$i], $renderedLayout, $workText);
        }
        /** update source **/
        $row = $workText;

        return;
    }
}
