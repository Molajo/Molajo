<?php
/**
 * Customfields Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Customfields;

use CommonApi\Event\ReadEventInterface;

/**
 * Customfields Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class CustomfieldsPlugin extends CustomfieldsModelRegistry implements ReadEventInterface
{
    /**
     * Process Customfield Groups
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processCustomfieldGroups();

        return $this;
    }

    /**
     * Process each Custom Field Group, expanding the fields, as defined in Model Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processCustomfieldGroups()
    {
        $this->setPageType();

        $this->setCustomfieldGroupModelRegistry();

        foreach ($this->controller['model_registry']['customfieldgroups'] as $group) {

            $this->setCustomfieldGroupContent($group);
            $this->setCustomfieldGroupContentForExtension($group);
            $this->setCustomfieldGroupContentForApplication($group);

            $this->controller['row']->$group = $this->setCustomfieldGroupElements($group);

            $this->controller['model_registry'] = $this->model_registry_merged;

            if ($group === 'parameters') {
                $this->setThemeIdDefault();
            }
        }

        return $this;
    }

    /**
     * Set Theme ID default
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setThemeIdDefault()
    {
        if (isset($this->controller['row']->parameters->theme_id)
            && isset($this->controller['row']->parameters->application_default_theme_id)
        ) {
            if ((int)$this->controller['row']->parameters->theme_id === 0) {
                $this->controller['row']->parameters->theme_id
                    = (int)$this->controller['row']->parameters->application_default_theme_id;
            }
        }

        return $this;
    }
}
