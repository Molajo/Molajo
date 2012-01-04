<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Utility class to fire onContentPrepare for non-article based content.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class MolajoHtmlContent
{
    /**
     * Fire onContentPrepare for content that isn't part of an article.
     *
     * @param   string  $text     The content to be transformed.
     * @param   array   $parameters   The content parameters.
     * @param   string  $context  The context of the content to be transformed.
     *
     * @return  string   The content after transformation.
     *
     * @since   1.0
     */
    public static function prepare($text, $parameters = null, $context = 'text')
    {
        if ($parameters === null) {
            $parameters = new JObject;
        }
        $article = new stdClass;
        $article->text = $text;
        JPluginHelper::importPlugin('content');
        $dispatcher = JDispatcher::getInstance();
        $results = $dispatcher->trigger('onContentPrepare', array($context, &$article, &$parameters, 0));

        return $article->text;
    }
}
