<?php
/**
 * @package     Molajo
 * @subpackage  Filter
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoIframeFilter
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoIframeFilter extends HTMLPurifier_Filter
{

    /**
     * Based on: http://sachachua.com/blog/2011/08/drupal-html-purifier-embedding-iframes-youtube/
     * Iframe filter that does some primitive whitelisting in a somewhat recognizable and tweakable way
     */

    public $name = 'MyIframe';

    /**
     * preFilter
     *
     * @param string $html
     * @param $config
     * @param $context
     * @return string
     */
    public function preFilter($html, $config, $context)
    {
        $html = preg_replace('#<iframe#i', '<img class="MyIframe"', $html);
        $html = preg_replace('#</iframe>#i', '</img>', $html);
        return $html;
    }

    /**
     * postFilter
     *
     * @param string $html
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    public function postFilter($html, $config, $context)
    {
        $post_regex = '#<img class="MyIframe"([^>]+?)>#';
        return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
    }

    /**
     *  postFilterCallback
     *
     * @param array $matches
     * @return string
     */
    protected function postFilterCallback($matches)
    {
        // Domain Whitelist
        $youTubeMatch = preg_match('#src="https?://www.youtube(-nocookie)?.com/#i', $matches[1]);
        $vimeoMatch = preg_match('#src="http://player.vimeo.com/#i', $matches[1]);
        if ($youTubeMatch || $vimeoMatch) {
            $extra = ' frameborder="0"';
            if ($youTubeMatch) {
                $extra .= ' allowfullscreen';
            } elseif ($vimeoMatch) {
                $extra .= ' webkitAllowFullScreen mozallowfullscreen allowFullScreen';
            }
            return '<iframe ' . $matches[1] . $extra . '></iframe>';
        } else {
            return '';
        }
    }
}
