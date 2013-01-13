<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Url;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * URL
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class UrlService
{

    /**
     * Retrieves URL for a Catalog Type/Source ID or a Catalog ID
     *
     * @param   null  $catalog_type_id
     * @param   null  $source_id
     * @param   null  $catalog_id
     *
     * @return  string
     * @since   1.0
     */
    public function get($catalog_type_id = null, $source_id = null, $catalog_id = null)
    {
        if ($catalog_id == Services::Application()->get('application_home_catalog_id', 0)) {
            return '';
        }

        if (Services::Application()->get('url_sef', 1) == 1) {

            $controllerClass = CONTROLLER_CLASS_NAMESPACE;
            $controller      = new $controllerClass();
            $controller->getModelRegistry('datasource', 'Catalog', 1);

            $prefix = $controller->get('primary_prefix', 'a', 'model_registry');
            $key    = $controller->get('primary_key', 'id', 'model_registry');
            $controller->set('process_plugins', 0, 'model_registry');

            $controller->model->query->select(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn('sef_request')
            );

            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn($key)
                    . ' = '
                    . (int)$catalog_id
            );

            $url = $controller->getData(QUERY_OBJECT_RESULT);

        } else {
            $url = 'index.php?id=' . (int)$catalog_id;
        }

        return $url;
    }

    /**
     * Retrieves Catalog ID for the specified Catalog Type ID and Source ID
     *
     * @param   null $catalog_type_id
     * @param   null $source_id
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public function getCatalogID($catalog_type_id, $source_id = null, $url_sef_request = null)
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('datasource', 'Catalog', 1);

        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $key    = $controller->get('primary_key', 'id', 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');

        $controller->model->query->select(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn($key)
        );

        if ($url_sef_request === null) {
            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.' . $controller->model->db->qn('catalog_type_id')
                    . ' = '
                    . (int)$catalog_type_id
            );

            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn('source_id')
                    . ' = '
                    . (int)$source_id
            );

        } else {

            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn('sef_request')
                    . ' = '
                    . $controller->model->db->q($url_sef_request)
            );
        }

        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('application_id')
                . ' = '
                . $controller->model->db->q(APPLICATION_ID)
        );

        return $controller->getData(QUERY_OBJECT_RESULT);
    }

    /**
     * Retrieves Redirect URL for a specific Catalog id
     *
     * @param   integer  $catalog_id
     *
     * @return  string   URL
     * @since   1.0
     */
    public function getRedirectURL($catalog_id)
    {
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('datasource', 'Catalog', 1);

        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $key    = $controller->get('primary_key', 'id', 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');

        $controller->model->query->select(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('redirect_to_id')
        );

        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn($key)
                . ' = '
                . (int)$catalog_id
        );

        $catalog_id = $controller->getData(QUERY_OBJECT_RESULT);

        if ((int)$catalog_id == 0) {
            return false;
        }

        return $this->get(null, null, $catalog_id);
    }

    /**
     * getApplicationURL - pass in non-application, non-base URL portion, returns full URL
     *
     * @param   string $path
     *
     * @return  string
     * @since   1.0
     */
    public function getApplicationURL($path = '')
    {
        return BASE_URL . APPLICATION_URL_PATH . $path;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param   string  $email
     * @param   string  $size        Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param   string  $type        Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param   string  $rating      Maximum rating (inclusive) [ g | pg | r | x ]
     * @param   boolean $image       true to return a complete IMG tag false for just the URL
     * @param   array   $attributes  Optional, additional key/value attributes to include in the IMG tag
     *
     * @return  mixed
     * @since   1.0
     */
    public function getGravatar(
        $email,
        $size = 0,
        $type = 'mm',
        $rating = 'g',
        $image = false,
        $attributes = array(),
        $align = 'left'
    ) {

        if ((int)$size == 0) {
            $size   = Services::Application()->get('gravatar_size', 80);
            $type   = Services::Application()->get('gravatar_type', 'mm');
            $rating = Services::Application()->get('gravatar_rating', 'pg');
            $image  = Services::Application()->get('gravatar_image', 0);
        }

        /**
        if ($align == 'right') {
        $css = '.gravatar { float:right; margin: 0 0 15px 15px; }';
        } else {
        $css = '.gravatar { float:left; margin: 0 15px 15px 0; }';
        }
        $this->class_asset->addCssDeclaration($css, 'text/css');
         */
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= '?s=' . $size . '&d=' . $type . '&r=' . $rating;
        if ($image) {
            $url = '<img class="gravatar" src="' . $url . '"';
            if (count($attributes) > 0) {
                foreach ($attributes as $key => $val) {
                    $url .= ' ' . $key . '="' . $val . '"';
                }
            }
            $url .= ' />';
        }

        return $url;
    }

    /**
     * obfuscate Email
     *
     * @param   $email_address
     *
     * @return  string
     * @since   1.0
     */
    public function obfuscateEmail($email_address)
    {
        $obfuscate_email = "";

        for ($i = 0; $i < strlen($email_address); $i++) {
            $obfuscate_email .= "&#" . ord($email_address[$i]) . ";";
        }

        return $obfuscate_email;
    }

    /**
     * Add links to a generic text field when URLs are found
     *
     * @param   string $text_field
     *
     * @return  string
     */
    public function addLinks($text_field)
    {
        $pattern = "/(((http[s]?:\/\/)|(www\.))?(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";

        $text_field = preg_replace($pattern, " <a href='$1'>$1</a>", $text_field);

        $text_field = preg_replace("/href=\"www/", "href=\"http://www", $text_field);

        return $text_field;
    }

    /**
     * createWebLinks - marks up a link into an <a href link
     *
     * @todo    pick one of these two (previous and this one)
     *
     * @param   string $url_field
     *
     * @return  string
     * @since   1.0
     */
    public function createWebLinks($url_field)
    {
        return preg_replace(
            '#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is',
            '\\1<a href="\\2">\\2</a>',
            $url_field
        );
    }

    /**
     * checkURLExternal - determines if it is a local site or external link
     *
     * @param  string   $url_field
     *
     * @return  boolean
     * @since   1.0
     */
    public function checkURLExternal($url_field)
    {
        if (substr($url_field, 0, strlen(BASE_FOLDER)) == BASE_FOLDER) {
            return false;

        } elseif ((strtolower(substr($url_field, 0, 3)) == 'www')
            && (substr($url_field, 3, strlen(BASE_FOLDER)) == BASE_FOLDER)
        ) {
            return false;

        } else {
            return true;
        }
    }

    /**
     * getHost - retrieves host from the URL
     *
     * @param   string $url_field
     *
     * @return  boolean
     * @since   1.0
     */
    public function getHost($url_field)
    {
        $hostArray = parse_url($url_field);

        return $hostArray['scheme'] . '://' . $hostArray['host'];
    }

    /**
     * retrieveURLContents - issues request with link via curl
     *
     * @param   string $url_field
     *
     * @return  boolean
     * @since   1.0
     */
    public function retrieveURLContents($url_field)
    {
        return curl::processCurl($url_field);
    }

    /**
     * addTrailingSlash - $url = Services::Url()->addTrailingSlash ($url_field);
     *
     * @param   object  $url_field
     *
     * @return  string
     * @since   1.0
     */
    public function addTrailingSlash($url_field)
    {
        return untrailingslashit($url_field) . '/';
    }

    /**
     * removeTrailingSlash - $url = Services::Url()->removeTrailingSlash ($url_field);
     *
     * @param object $url_field
     *
     * @return  string
     * @since   1.0
     */
    public function removeTrailingSlash($url_field)
    {
        return rtrim($url_field, '/');
    }

    /**
     * urlShortener
     * $longurl
     *
     * @param object $longurl
     * 1 Local Shortened
     * 2 TinyURL
     * 3 is.gd
     * 4 bit.ly
     * 5 tr.im
     *
     * @return
     */
    public function urlShortener($longurl, $username, $apikey, $username, $apikey)
    {
        $shortener = 1;

        if ($shortener == '1') {
            return $longurl; // @todo create local short url

        } elseif ($shortener == '2') {
            return (implode('', file('http://tinyurl.com/api-create.php?url=' . urlencode($longurl))));

        } elseif ($shortener == '3') {
            return (implode('', file('http://is.gd/api.php?longurl=' . urlencode($longurl))));

        } elseif ($shortener == '4') {

            $bitlyURL     = file_get_contents(
                "http://api.bit.ly/v3/shorten" . "&login=" . $username . "&apiKey=" . $apikey . "&longUrl=" . urlencode(
                    $longurl
                ) . "&format=json"
            );
            $bitlyContent = json_decode($bitlyURL, true);
            $bitlyError   = $bitlyContent["errorCode"];
            if ($bitlyError == 0) {
                return $bitlyContent["results"][$longurl]["shortUrl"];
            } else {
                return $bitlyError;
            }

        } elseif ($shortener == '5') {
            return (implode('', file('http://api.tr.im/api/trim_simple?url=' . urlencode($longurl))));

        } else {
            return $longurl;
        }
    }
}
