<?php
/**
 * @version     $id: single.php
 * @package     Molajo
 * @subpackage  Single Model
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoModelPage
 *
 * @package        Molajo
 * @subpackage    Page Information
 * @since       1.0
 */
class MolajoModelPage extends JModel
{
    /**
     * @var    object    parameters
     * @since    1.0
     */
    protected $parameters;

    /**
     * setMeta
     *
     * Method to set Meta Data for the current page
     *
     * @param    integer    The id of the primary key.
     *
     * @return    mixed    Object on success, false on failure.
     */
    public function setMeta($id = null)
    {

        $documentHelper->prepareDocument($this->parameters, $this->item, $this->document, JRequest::getCmd('option'), JRequest::getCmd('view'));
        $menus = MolajoFactory::getApplication()->getMenu();
        $pathway = MolajoFactory::getApplication()->getPathway();
        $title = null;
        $document = MolajoFactory::getDocument();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu) {
            $parameters->def('page_heading', $parameters->get('page_title', $menu->title));
        } else {
            $parameters->def('page_heading', MolajoText::_('COM_' . strtoupper($content_item) . '_DEFAULT_PAGE_TITLE'));
        }

        $title = $parameters->get('page_title', '');

        $id = (int)@$menu->query['id'];

        // if the menu item does not concern this contact
        if ($menu &&
            ($menu->query['option'] != $component_option
             || $menu->query['view'] != $component_view
             || $id != $content_item->id)
        ) {
            // If this is not a single item menu item, set the page title to the item title
            if ($content_item->name) {
                $title = $content_item->name;
            }
            $path = array(array('title' => $content_item->name, 'link' => ''));
            // amy  $category = JCategories::getInstance($component_view)->get($content_item->catid);

            while ($category &&
                   ($menu->query['option'] != $component_option
                    || $menu->query['view'] == $component_view
                    || $id != $category->id)
                   && $category->id > 1)
            {
                //amy                $path[] = array('title' => $category->title,
                //                              'link' => MolajoHelperRoute::getCategoryRoute($content_item->catid));

                $category = $category->getParent();
            }

            $path = array_reverse($path);

            foreach ($path as $pathwayItem)
            {
                $pathway->addItem($pathwayItem['title'], $pathwayItem['link']);
            }
        }

        if (empty($title)) {
            $title = MolajoFactory::getApplication()->getConfig('sitename');

        } elseif (MolajoFactory::getApplication()->getConfig('sitename_pagetitles', 0)) {
            $title = MolajoText::sprintf('JPAGETITLE', MolajoFactory::getApplication()->getConfig('sitename'), $title);
        }

        if (empty($title)) {
            $title = $content_item->title;
        }
        $document->setTitle($title);

        if ($content_item->metadesc) {
            $document->setDescription($content_item->metadesc);

        } elseif (!$content_item->metadesc && $parameters->get('menu-meta_description')) {
            $document->setDescription($parameters->get('menu-meta_description'));
        }

        if ($content_item->metakey) {
            $document->setMetadata('keywords', $content_item->metakey);

        } elseif (!$content_item->metakey && $parameters->get('menu-meta_keywords')) {
            $document->setMetadata('keywords', $parameters->get('menu-meta_keywords'));
        }

        if ($parameters->get('robots')) {
            $document->setMetadata('robots', $parameters->get('robots'));
        }

        if (MolajoFactory::getApplication()->getConfig('MetaTitle') == '1') {
            $document->setMetaData('title', $content_item->title);
        }

        //amy        $mdata = $content_item->metadata->toArray();
        $mdata = array();
        foreach ($mdata as $k => $v) {
            if ($v) {
                $document->setMetadata($k, $v);
            }
        }

    }
}