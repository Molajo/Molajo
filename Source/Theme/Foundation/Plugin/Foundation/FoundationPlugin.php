<?php
/**
 * Foundation Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Foundation;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Foundation Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FoundationPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * loadMedia - Method executed in Theme Rendering to load external media, special metadata or links
     *
     * @return void
     * @since   1.0
     */
    public function onBeforeParse()
    {
        /** Theme Folder */
        $theme = $this->registry->get('runtime_data', 'theme_path_node');

        /** Mobile Specific Meta: Sets the viewport width to device width for mobile */
        $this->document_metadata->set(
            'viewport',
            array('width=device-width, initial-scale=1', 'name')
        );

        /** Favicons */

        /** For non-Retina iPhone, iPod Touch, and Android 2.1+ devices */
        $this->document_links->set(
            $url = BASE_FOLDER . '/Source' . '/' . THEME_URL . '/' . $theme
                . '/' . 'images/favicons/apple-touch-icon-precomposed.png',
            $relation = 'apple-touch-icon-precomposed'
        );
        /** For first- and second-generation iPad */
        $this->document_links->set(
            $url = BASE_FOLDER . '/Source' . '/' . THEME_URL . '/' . $theme
                . '/' . 'images/favicons/apple-touch-icon-72x72-precomposed.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('sizes,72x72')
        );
        /** For iPhone with high-resolution Retina display */
        $this->document_links->set(
            $url = BASE_FOLDER . '/Source' . '/' . THEME_URL . '/' . $theme
                . '/' . 'images/favicons/apple-touch-icon-114x114-precomposed.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('sizes,114x114')
        );
        /** For third-generation iPad with high-resolution Retina display */
        $this->document_links->set(
            $url = BASE_FOLDER . '/Source' . '/' . THEME_URL . '/' . $theme
                . '/' . 'images/favicons/apple-touch-icon-144x144-precomposed.png',
            $relation = 'apple-touch-icon-precomposed',
            $relation_type = 'rel',
            $attributes = array('sizes,144x144')
        );

        /** Application-specific CSS: Both share Foundation CSS but require different max-width and top bar breakpoint*/
        if ($this->runtime_data->application->id == 1) {
            $this->document_css->set(
                $url = BASE_FOLDER . '/Source' . '/' . THEME_URL . '/' . $theme . '/' . 'css/1/Template.css',
                $priority = 1000,
                $mimetype = 'test/css',
                $media = 'all',
                $conditional = '',
                $attributes = array()
            );
        }

        /** jQuery CDN and fallback */
        $this->document_js->set('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', 1, 1);

        $jQueryUrl   = BASE_FOLDER . '/Source' . '/' . THEME_URL . '/' . $theme . '/' . 'Js/Fallback/jquery-1.8.0.min.js';
        $jQueryUIUrl = BASE_FOLDER . '/Source' . '/' . THEME_URL . '/' . $theme . '/' . 'Js/Fallback/jquery-ui-1.8.23.min.js';
        $fallback    = "
        if (typeof jQuery == 'undefined') {
            document.write(unescape(" . '"' . "%3Cscript src='" . $jQueryUrl . "' type='text/javascript'%3E%3C/script%3E" . '"' . "));
            document.write(unescape(" . '"' . "%3Cscript src='" . $jQueryUIUrl . "' type='text/javascript'%3E%3C/script%3E" . '"' . "));
         }";

        $this->document_js->set('inline', $fallback, 1, 1, 'text/javascript');

        /** jQueryUI CDN and fallback */
        $this->document_js->set('http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js', 2, 1);

        /** Modernizer */
        $this->document_js->set('http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.5.3/modernizr.min.js', 10000, 0);

        /** HTML5 Shiv */
        $this->document_js->set('http://html5shiv.googlecode.com/svn/trunk/html5.js', 1, 0);

        return;
    }
}
