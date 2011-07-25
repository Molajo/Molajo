<?php
/**
 * @version  1.6.2 June 9, 2011
 * @author  �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license  http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JFormFieldTemplateStyles extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'TemplateStyles';


    public function getOptions()
    {
        // Initialize variables.
        $options = array();

        $templates = $this->getTemplates(false);

        foreach ($templates as $template)
        {
            $tmp = JHtml::_('select.option', (string)$template->id, trim((string)$template->title), 'value', 'text', false);
            $options[] = $tmp;
        }

        foreach ($this->element->children() as $option)
        {
            // Only add <option /> elements.
            if ($option->getName() != 'option')
            {
                continue;
            }
            // Create a new option object based on the <option /> element.
            $tmp = JHtml::_('select.option', (string)$option['value'], trim((string)$option), 'value', 'text', ((string)$option['disabled'] == 'true'));
            // Set some option attributes.
            $tmp->class = (string)$option['class'];
            // Set some JavaScript option attributes.
            $tmp->onclick = (string)$option['onclick'];
            // Add the option object to the result set.
            $options[] = $tmp;
        }
        reset($options);


        return $options;

    }

    protected function getTemplates()
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, home, template, params, title');
        $query->from('#__template_styles');
        $query->where('application_id = 1');
        $db->setQuery($query);
        $templates = $db->loadObjectList('id');
        foreach ($templates as &$template)
        {
            $template->template = JFilterInput::getInstance()->clean($template->template, 'cmd');
            $template->params = new JRegistry($template->params);
            if (!file_exists(JPATH_THEMES . DS . $template->template . DS . 'index.php'))
            {
                $template->params = new JRegistry();
                $template->template = 'bluestork';
            }
        }

        return $templates;
    }

    /**
     * @param  $name
     * @return TemplatesTableStyle
     */
    protected function getTemplateByName($name)
    {
        $templates = $this->getTemplates();
        foreach ($templates as $id => $template)
        {
            if ($template->template == $name) return $template;
        }
        return null;
    }
}