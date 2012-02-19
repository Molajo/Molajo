<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Mustache
 *
 * Template helper file can extend this and add functions
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoMustacheHelper extends Mustache
{
    /**
     * $data
     *
     * Allows collection of any set of data for a single $item
     *
     * @var    array
     * @since  1.0
     */
    public $data = array();

    /**
     * $rows
     *
     * Retains pointer to current row contained within the $data array
     *
     * @var    int
     * @since  1.0
     */
    protected $rows = 0;

    /**
     * items
     *
     * Returns a single row of information to mustache
     * around the {# item } {/ item } controlbreak
     *
     * tracks row number in #this->rows so that rowset can be exploited
     *
     * @return ArrayIterator
     * @since  1.0
     */
    public function items() {
        $this->rows++;
        return new ArrayIterator($this->data);
    }

    /**
     * intro
     *
     * Using the $this->row value, the data element introtext can be
     * printed for this specific article.
     *
     * @return string
     * @since  1.0
     */
    public function intro() {
        return '<i>'.$this->data[$this->rows-1]->introtext.'</i>';
    }

    /**
     * hello
     *
     * Returns hello for {{ hello }}
     * Template example overrides for different result
     *
     * @return string
     * @since  1.0
     */
    public function hello() {
        return 'Hello!';
    }

    /**
     * profile
     *
     * Renders the Author Profile Module for this article
     *
     * $results  text
     * $since    1.0
     */
    public function profile()
    {
        $rc = new MolajoModuleRenderer ('profile', '');
        $attributes = array();
        $attributes['name'] = 'dashboard';
        $attributes['template'] = 'dashboard';
        $attributes['wrap'] = 'section';
        $attributes['id'] = $this->data[$this->rows-1]->id;

        return $rc->process($attributes);
    }
}
