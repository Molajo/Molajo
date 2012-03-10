<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Image
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class ImageService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * @var numeric $id
     */
    protected $id;

    /**
     * @var numeric $this->size
     *
     * 0 - original size
     * 1 - xsmall; configuration option, defaults to 50 x 50
     * 2 - small; configuration option, defaults to 75 x 75
     * 3 - medium; configuration option, defaults to 150 x 150
     * 4 - large; configuration option, defaults to 300 x 300
     * 5 - xlarge; configuration option, defaults to 500 x 500
     */
    protected $size;

    /**
     * @var numeric $fileName
     */
    protected $fileName;

    /**
     * @var numeric $fileNameOriginal
     */
    protected $fileNameOriginal;

    /**
     * @var numeric $fileNameNew
     */
    protected $fileNameNew;

    /**
     * @var numeric $image
     */
    protected $image;

    /**
     * @var numeric $type
     */
    protected $type;

    /**
     * @var numeric $width
     */
    protected $width;

    /**
     * @var numeric $height
     */
    protected $height;

    /**
     * @var numeric $imageResized
     */
    protected $imageResized;

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
            self::$instance = new ImageService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {
    }

    /**
     * getImage
     *
     * Build an SQL query to select an image.
     *
     * @return    JDatabaseQuery
     * @since    1.0
     */
    public function getImage($id, $size = 0, $type = 'crop')
    {
        /** initialise  */
        $this->id = (int)$id;
        $this->size = (int)$this->size;
        if ($this->size == 1
            || $this->size == 2
            || $this->size == 3
            || $this->size == 4
            || $this->size == 5
        ) {
        } else {
            $this->size = 0;
        }
        if ($this->type = 'exact'
            || $this->type = 'portrait'
                || $this->type = 'landscape'
                    || $this->type = 'auto'
        ) {
        } else {
            $this->type = 'crop';
        }

        /** retrieve filename and perform acl check */
        $results = $this->getImage();
        if ($results === false) {
            return false;
        }

        /** return original size, if selected */
        if ($this->size == 0) {
            return $this->fileNameOriginal;
        }

        /** return resized image */
        $results = $this->getResizedImage();
        if ($results === false) {
        } else {
            return $this->fileNameNew;
        }

        /** resize file */
        $results = $this->createResizedImage();
        if ($results === false) {
            return false;
        } else {
            return $this->fileNameNew;
        }

        $db = Services::DB();
        $query = $db->getQuery(true);

        $date = Services::Date()
            ->format('Y-m-d-H-i-s');

        $now = $date->toSql();
        $nullDate = $db->getNullDate();

        $query->select($db->qn('path'));
        $query->from($db->qn('#__content') . 'as a');
        $query->where('a.' . $db->qn('status') . ' = 1');
        $query->where('(a.' . $db->qn('start_publishing_datetime') . ' = ' . $db->q($nullDate) .
            ' OR a.' . $db->qn('start_publishing_datetime') . ' <= ' . $db->q($now) . ')');
        $query->where('(a.' . $db->qn('stop_publishing_datetime') . ' = ' . $db->q($nullDate) .
            ' OR a.' . $db->qn('stop_publishing_datetime') . ' >= ' . $db->q($now) . ')');
        $query->where('a.id = ' . (int)$this->id);

        $query->from($db->qn('#__assets') . 'as b');
        $query->where('b.' . $db->qn('source_id') . ' = ' . $db->qn('id'));
        $query->where('b.' . $db->qn('asset_type_id') . ' = ' . $db->qn('asset_type_id'));

        $db->setQuery($query->__toString());

        $this->filename = $db->loadResult();
        if ($this->filename === false) {
            return false;
        }

        /** retrieve image folder for original images */
        $images = Services::Configuration()->get('media_path', 'media/images');

        /** folders */
        if (Services::Folder()->exists(SITE_FOLDER_PATH . '/' . $images)) {
        } else {
            Services::Folder()->create(SITE_FOLDER_PATH . '/' . $images);
        }

        /** make certain original image exists */
        $this->fileNameOriginal = SITE_FOLDER_PATH . '/' . $images . '/' . $this->filename;
        if (Services::File()->exists($this->fileNameOriginal)) {
            return $this->fileNameOriginal;
        } else {
            return false;
        }
    }

    /**
     * getResizedImage
     *
     * @return string
     */
    private function getResizedImage()
    {
        /** retrieve image folder for resized images */
        $images = Services::Configuration()->get('thumb_folder', '/media/images/thumbs');

        /** folders */
        if (Services::Folder()->exists(SITE_FOLDER_PATH . '/' . $images)) {
        } else {
            Services::Folder()->create(SITE_FOLDER_PATH . '/' . $images);
        }

        /** if resized image already exists, return it */
        $this->fileNameNew = SITE_FOLDER_PATH . '/' . $images . '/' . 's' . $this->size . '_' . 't' . '_' . $this->type . $this->filename;
        if (Services::File()->exists($this->fileNameNew)) {
            return true;
        }

        return false;
    }

    /**
     * resizeImage
     *
     * @return void
     */
    protected function createResizedImage()
    {
        /** Options: exact, portrait, landscape, auto, crop and size */
        if ($this->size == 1) {
            $dimensions = Services::Configuration()->get('image_xsmall', 50);
        } else if ($this->size == 2) {
            $dimensions = Services::Configuration()->get('image_small', 75);
        } else if ($this->size == 3) {
            $dimensions = Services::Configuration()->get('image_medium', 150);
        } else if ($this->size == 4) {
            $dimensions = Services::Configuration()->get('image_large', 300);
        } else if ($this->size == 5) {
            $dimensions = Services::Configuration()->get('image_xlarge', 500);
        } else {
            $dimensions = 100;
        }

        /** 1. open the original file */
        $this->createImageObject();

        /** 2. set existing dimensions */
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);

        /** 3. resize Image */
        $this->resizeImage($dimensions);

        /** 4. Save image */
        return $this->saveImage(100);
    }

    /**
     * createImageObject
     *
     * @param $file
     * @return bool|resource
     */
    protected function createImageObject()
    {
        $ext = strtolower(strrchr($this->fileNameOriginal, '.'));

        switch ($ext)
        {
            case '.jpg':
            case '.jpeg':
                $this->image = @imagecreatefromjpeg($this->fileNameOriginal);
                break;

            case '.gif':
                $this->image = @imagecreatefromgif($this->fileNameOriginal);
                break;

            case '.png':
                $this->image = @imagecreatefrompng($this->fileNameOriginal);
                break;

            default:
                $this->image = false;
                break;
        }
    }

    /**
     * resizeImage
     *
     * @param $newWidth
     * @param $newHeight
     * @param string $this->type
     * @return void
     */
    public function resizeImage($dimensions)
    {
        /** Get optimal dimensions based on type */
        $newWidth = $dimensions;
        $newHeight = $dimensions;
        $this->typeArray = $this->getDimensions($newWidth, $newHeight, $this->type);

        $optimalWidth = $this->typeArray['optimalWidth'];
        $optimalHeight = $this->typeArray['optimalHeight'];

        /** resample */
        $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);

        if ($this->type == 'crop') {
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
        }
    }

    /**
     * getDimensions
     *
     * @param $newWidth
     * @param $newHeight
     * @param $this->type
     * @return array
     */
    protected function getDimensions($newWidth, $newHeight)
    {
        switch ($this->type)
        {
            case 'exact':
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
                break;

            case 'portrait':
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;
                break;

            case 'landscape':
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
                break;

            case 'auto':
                $this->typeArray = $this->getSizeByAuto($newWidth, $newHeight);
                $optimalWidth = $this->typeArray['optimalWidth'];
                $optimalHeight = $this->typeArray['optimalHeight'];
                break;

            case 'crop':
                $this->typeArray = $this->getOptimalCrop($newWidth, $newHeight);
                $optimalWidth = $this->typeArray['optimalWidth'];
                $optimalHeight = $this->typeArray['optimalHeight'];
                break;
        }
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    /**
     * getSizeByFixedHeight
     *
     * @param $newHeight
     * @return
     */
    protected function getSizeByFixedHeight($newHeight)
    {
        $ratio = $this->width / $this->height;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }

    /**
     * getSizeByFixedWidth
     *
     * @param $newWidth
     * @return
     */
    protected function getSizeByFixedWidth($newWidth)
    {
        $ratio = $this->height / $this->width;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }

    /**
     * getSizeByAuto
     *
     * @param $newWidth
     * @param $newHeight
     * @return array
     */
    protected function getSizeByAuto($newWidth, $newHeight)
    {
        if ($this->height < $this->width) {

            // *** Image to be resized is wider (landscape)
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);

        } elseif ($this->height > $this->width) {

            // *** Image to be resized is taller (portrait)
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;

        } else {

            // *** Image to be resized is a square
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);

            } else if ($newHeight > $newWidth) {
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;

            } else {
                // *** Square resized to a square
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
            }
        }

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    /**
     * getOptimalCrop
     *
     * @param $newWidth
     * @param $newHeight
     * @return array
     */
    protected function getOptimalCrop($newWidth, $newHeight)
    {
        $heightRatio = $this->height / $newHeight;
        $widthRatio = $this->width / $newWidth;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = $this->height / $optimalRatio;
        $optimalWidth = $this->width / $optimalRatio;

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    /**
     * crop
     *
     * @param $optimalWidth
     * @param $optimalHeight
     * @param $newWidth
     * @param $newHeight
     * @return void
     */
    protected function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
    {
        // *** Find center - this will be used for the crop
        $cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
        $cropStartY = ($optimalHeight / 2) - ($newHeight / 2);

        $crop = $this->imageResized;

        // *** Now crop from center to exact requested size
        $this->imageResized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight);
    }

    /**
     * saveImage
     *
     * @param  string $imageQuality
     *
     * @return boolean
     * @since  1.0
     */
    public function saveImage($imageQuality = "100")
    {
        // *** Get extension
        $ext = strrchr($this->fileNameNew, '.');
        $ext = strtolower($ext);

        switch ($ext)
        {
            case '.jpg':
            case '.jpeg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->imageResized, $this->fileNameNew, $imageQuality);
                }
                $results = true;
                break;

            case '.gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->imageResized, $this->fileNameNew);
                }
                $results = true;
                break;

            case '.png':
                // *** Scale quality from 0-100 to 0-9
                $scaleQuality = round(($imageQuality / 100) * 9);

                // *** Invert quality setting as 0 is best, not 9
                $invertScaleQuality = 9 - $scaleQuality;

                if (imagetypes() & IMG_PNG) {
                    imagepng($this->imageResized, $this->fileNameNew, $invertScaleQuality);
                }
                $results = true;
                break;

            default:
                $results = false;
                break;
        }

        imagedestroy($this->imageResized);

        return $results;
    }

    /**
     * getPlaceHolderImage
     *
     * @static
     * @param $width
     * @param $height
     * @param array $options
     * @return mixed
     * @since 1.0
     */
    static public function getPlaceHolderImage($width, $height, $options = array())
    {

        $services_class = array(
            'placehold' => 'PlaceholdImage',
            'lorem_pixel' => 'LoremPixelImage'
        );

        $service = $options['service'];
        $service = isset($service) ? $service : 'placehold';

        $service_class = $services_class[$service];
        if (class_exists($service_class)) {
            $service = new $service_class($width, $height, $options);
            return $service->url();
        } else {
            render_error("No placeholder image service called #{$service} exists!");
        }
    }
}
