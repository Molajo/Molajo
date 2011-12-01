<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Abstract Form Field class
 *
 * @package     Molajo
 * @subpackage  Form
 * @since       1.0
 */
abstract class MolajoFormField
{
    /**
     * The description text for the form field.  Usually used in tooltips.
     *
     * @var    string
     * @since  1.0
     */
    protected $description;

    /**
     * The JXMLElement object of the <field /> XML element that describes the form field.
     *
     * @var    object
     * @since  1.0
     */
    protected $element;

    /**
     * The MolajoForm object of the form attached to the form field.
     *
     * @var    object
     * @since  1.0
     */
    protected $form;

    /**
     * The form control prefix for field names from the MolajoForm object attached to the form field.
     *
     * @var    string
     * @since  1.0
     */
    protected $formControl;

    /**
     * The hidden state for the form field.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $hidden = false;

    /**
     * True to translate the field label string.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $translateLabel = true;

    /**
     * True to translate the field description string.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $translateDescription = true;

    /**
     * The document id for the form field.
     *
     * @var    string
     * @since  1.0
     */
    protected $id;

    /**
     * The calendar for the form field.
     *
     * @var    string
     * @since  1.0
     */
    protected $calendar;

    /**
     * The label for the form field.
     *
     * @var    string
     * @since  1.0
     */
    protected $label;

    /**
     * The multiple state for the form field.  If true then multiple values are allowed for the
     * field.  Most often used for list field types.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $multiple = false;

    /**
     * The name of the form field.
     *
     * @var    string
     * @since  1.0
     */
    protected $name;

    /**
     * The name of the field.
     *
     * @var    string
     * @since  1.0
     */
   
    protected $group;

    /**
     * The required state for the form field.  If true then there must be a value for the field to
     * be considered valid.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $required = false;

    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type;

    /**
     * The validation method for the form field.  This value will determine which method is used
     * to validate the value for a field.
     *
     * @var    string
     * @since  1.0
     */
    protected $validate;

    /**
     * The value of the form field.
     *
     * @var    mixed
     * @since  1.0
     */
    protected $value;

    /**
     * The count value for generated name field
     *
     * @var    integer
     * @since  1.0
     */
    static protected $count = 0;

    /**
     * The string used for generated fields names
     *
     * @var    integer
     * @since  1.0
     */
    static protected $generated_name = '__field';

    /**
     * Tracks pre and post processing for Form Fieldtypes
     *
     * @var    integer
     * @since  11.1
     */
    static protected $preFieldTypeProcessor = true;

    /**
     * Method to instantiate the form field object.
     *
     * @param   object  $form  The form to attach to the form field object.
     *
     * @return  MolajoFormField
     *
     * @since   1.0
     */
    public function __construct($form = null)
    {
        // If there is a form passed into the constructor set the form and form control properties.
        if ($form instanceof MolajoForm) {
            $this->form = $form;
            $this->formControl = $form->getFormControl();
        }
    }

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param   string  $name  The property name for which to the the value.
     *
     * @return  mixed  The property value or null.
     *
     * @since   1.0
     */
    public function __get($name)
    {
        switch ($name) {
            case 'class':
            case 'description':
            case 'formControl':
            case 'hidden':
            case 'id':
            case 'multiple':
            case 'name':
            case 'required':
            case 'type':
            case 'validate':
            case 'value':
            case 'name':
            case 'group':
                return $this->$name;
                break;

            case 'calendar':
                // If the calendar hasn't yet been generated, generate it.
                if (empty($this->calendar)) {
                    $this->calendar = $this->getInput();
                }

                return $this->calendar;
                break;

            case 'label':
                // If the label hasn't yet been generated, generate it.
                if (empty($this->label)) {
                    $this->label = $this->getLabel();
                }

                return $this->label;
                break;
            case 'title':
                return $this->getTitle();
                break;
        }

        return null;
    }

    /**
     * Method to attach a MolajoForm object to the field.
     *
     * @param   object  $form  The MolajoForm object to attach to the form field.
     *
     * @return  object  The form field object so that the method can be used in a chain.
     *
     * @since   1.0
     */
    public function setForm(MolajoForm $form)
    {
        $this->form = $form;
        $this->formControl = $form->getFormControl();

        return $this;
    }

    /**
     * Method to attach a MolajoForm object to the field.
     *
     * @param   object  $element  The JXMLElement object representing the <field /> tag for the
     *                            form field object.
     * @param   mixed   $value    The form field default value for display.
     * @param   string  $group    The field name group control value. This acts as as an array
     *                            container for the field. For example if the field has name="foo"
     *                            and the group value is set to "bar" then the full field name
     *                            would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @since   1.0
     */
    public function setup(& $element, $value, $group = null)
    {
        // Make sure there is a valid MolajoFormField XML element.
        if (!($element instanceof JXMLElement) || (string)$element->getName() != 'field') {
            return false;
        }


        // Reset the calendar and label values.
        $this->calendar = null;
        $this->label = null;

        // Set the XML element object.
        $this->element = $element;

        // Get some important attributes from the form field element.
        $class = (string)$element['class'];
        $id = (string)$element['id'];
        $multiple = (string)$element['multiple'];
        $name = (string)$element['name'];
        $required = (string)$element['required'];

        // Set the required and validation options.
        $this->required = ($required == 'true' || $required == 'required' || $required == '1');
        $this->validate = (string)$element['validate'];

        // Add the required class if the field is required.
        if ($this->required) {
            if ($class) {
                if (strpos($class, 'required') === false) {
                    $this->element['class'] = $class.' required';
                }
            } else {
                $this->element->addAttribute('class', 'required');
            }
        }

        // Set the multiple values option.
        $this->multiple = ($multiple == 'true' || $multiple == 'multiple');

        // Allow for field classes to force the multiple values option.
        if (isset($this->forceMultiple)) {
            $this->multiple = (bool)$this->forceMultiple;
        }

        // Set the field description text.
        $this->description = (string)$element['description'];

        // Set the visibility.
        $this->hidden = ((string)$element['type'] == 'hidden' || (string)$element['hidden'] == 'true');

        // Determine whether to translate the field label and/or description.
        $this->translateLabel = !((string)$this->element['translate_label'] == 'false' || (string)$this->element['translate_label'] == '0');
        $this->translateDescription = !((string)$this->element['translate_description'] == 'false' || (string)$this->element['translate_description'] == '0');

        // Set the group of the field.
        $this->group = $group;

        // Set the field name and id.
        $this->name = $this->getFieldName($name);
        $this->name = $this->getName($this->name);
        $this->id = $this->getId($id, $this->name);

        // Set the field default value.
        $this->value = $value;

        return true;
    }

    /**
     * Method to get the id used for the field calendar tag.
     *
     * @param   string  $nameId    The field element id.
     * @param   string  $name  The field element name.
     *
     * @return  string  The id to be used for the field calendar tag.
     *
     * @since   1.0
     */
    protected function getId($nameId, $name)
    {
        // Initialise variables.
        $id = '';

        // If there is a form control set for the attached form add it first.
        if ($this->formControl) {
            $id .= $this->formControl;
        }

        // If the field is in a group add the group control to the field id.
        if ($this->group) {
            // If we already have an id segment add the group control as another level.
            if ($id) {
                $id .= '_'.str_replace('.', '_', $this->group);
            }
            else {
                $id .= str_replace('.', '_', $this->group);
            }
        }

        // If we already have an id segment add the field id/name as another level.
        if ($id) {
            $id .= '_'.($nameId ? $nameId : $name);
        }
        else {
            $id .= ($nameId ? $nameId : $name);
        }

        // Clean up any invalid characters.
        $id = preg_replace('#\W#', '_', $id);

        return $id;
    }

    /**
     * getInput
     *
     * Method to get the field calendar markup.
     *
     * @return  string  The field calendar markup.
     * @since   1.0
     */
    protected function getInput()
    {
        if ($this->preFieldTypeProcessor) {
            $this->preFieldTypeProcessor = false;
            $this->getInputPreFieldTypeProcessing();
        } else {
            $this->getInputPostFieldTypeProcessing();
        }
    }

    /**
     * getInputPreProcessing
     *
     * Method to Define Common Attributes for all FormFields
     *
     * @return  array  $this->rowset
     * @since   1.0
     */
    protected function getInputPreFieldTypeProcessing()
    {
        $this->rowset = array();

        /** column */
        if (isset($this->row)) {
            $this->rowset[0]['column'] = (int)$this->column;
        } else {
            $this->rowset[0]['column'] = 0;
        }
        if ($this->column > 1000) {
            $this->column = 10;
        }

        /** class */
        if ($this->element->class) {
            $this->rowset[0]['class'] = (string)$this->element->class;
        } else {
            $this->rowset[0]['class'] = '';
        }

        /** description */
        if (isset($this->description)) {
            $this->rowset[0]['description'] = trim($this->description);
        } else {
            $this->rowset[0]['description'] = '';
        }

        /** disabled */
        if ($this->element->disabled == 'true' || $this->element->disabled === true) {
            $this->rowset[0]['disabled'] = true;
        } else {
            $this->rowset[0]['disabled'] = false;
        }

        /** name */
        if (isset($this->name)) {
            $this->rowset[0]['name'] = trim($this->name);
        } else {
            $this->rowset[0]['name'] = '';
        }

        /** group */
        if (isset($this->group)) {
            $this->rowset[0]['group'] = trim($this->group);
        } else {
            $this->rowset[0]['group'] = '';
        }

        /** html5 */
        if ($this->element->html5) {
            $this->rowset[0]['html5'] = (boolean)$this->element->html5;
        } else {
            $this->rowset[0]['html5'] = true;
        }

        /** id */
        if (isset($this->id)) {
            $this->rowset[0]['id'] = trim($this->id);
        } else {
            $this->rowset[0]['id'] = '';
        }

        /** label */
        if (isset($this->label)) {
            $this->rowset[0]['label'] = trim($this->label);
        } else {
            $this->rowset[0]['label'] = '';
        }

        /** layout */
        if (isset($this->layout)) {
            $this->rowset[0]['layout'] = trim($this->layout);
        } else {
            $this->rowset[0]['layout'] = 'text';
        }

        /** maxlength */
        if (isset($this->maxlength)) {
            $this->rowset[0]['maxlength'] = (int)$this->maxlength;
        } else {
            $this->rowset[0]['maxlength'] = 0;
        }

        /** multiple */
        if (isset($this->multiple)) {
            $this->rowset[0]['multiple'] = $this->multiple;
        }
        if ($this->multiple == true || trim($this->multiple) == 'true' || trim($this->multiple) == 'multiple') {
            $this->rowset[0]['multiple'] = 'multiple';
        } else {
            $this->rowset[0]['multiple'] = '';
        }

        /** name */
        if (isset($this->name)) {
            $this->rowset[0]['name'] = trim($this->name);
        } else {
            $this->rowset[0]['name'] = '';
        }

        /** onchange */
        if ($this->element->onchange) {
            $this->rowset[0]['onchange'] = ' onchange="'.(string)$this->element->onchange.'"';
        } else {
            $this->rowset[0]['onchange'] = '';
        }

        /** onclick */
        if ($this->element->onclick) {
            $this->rowset[0]['onclick'] = ' onclick="'.(string)$this->element->onclick.'"';
        } else {
            $this->rowset[0]['onclick'] = '';
        }

        /** readonly */
        if (trim($this->readonly) == 'true' || trim($this->readonly) == 'readonly' || $this->readonly === true) {
            $this->rowset[0]['readonly'] = 'readonly';
        } else {
            $this->rowset[0]['readonly'] = '';
        }

        /** required */
        if (trim($this->required) == 'true' || trim($this->required) == 'required' || $this->required === true) {
            $this->rowset[0]['required'] = 'required';
        } else {
            $this->rowset[0]['required'] = '';
        }

        /** row */
        if (isset($this->row)) {
            $this->rowset[0]['row'] = (int)$this->row;
        } else {
            $this->rowset[0]['row'] = 0;
        }
        if ($this->row > 1000) {
            $this->row = 10;
        }

        /** size */
        if (isset($this->size)) {
            $this->rowset[0]['size'] = (int)$this->size;
        } else {
            $this->rowset[0]['size'] = 0;
        }
        if ($this->size > 10) {
            $this->size = 10;
        }

        /** translateDescription */
        if (trim($this->translateDescription) == 'true' || trim($this->translateDescription) == 'required' || $this->translateDescription === true) {
            $this->rowset[0]['translate_description'] = true;
        } else {
            $this->rowset[0]['translate_description'] = false;
        }

        /** translateLabel */
        if (trim($this->translateLabel) == 'true' || trim($this->translateLabel) == 'required' || $this->translateLabel === true) {
            $this->rowset[0]['translate_label'] = true;
        } else {
            $this->rowset[0]['translate_label'] = false;
        }

        /** type */
        if (isset($this->type)) {
            $this->rowset[0]['type'] = $this->type;
        } else {
            $this->rowset[0]['type'] = '';
        }

        return;
    }

    /**
     * getInputPostFieldTypeProcessing
     *
     * Output Form Field to Layout
     *
     * @return  array
     * @since   1.0
     */
    protected function getInputPostFieldTypeProcessing()
    {


    }

    /**
     * Method to get the field title.
     *
     * @return  string  The field title.
     * @since   1.0
     */
    protected function getTitle()
    {
        // Initialise variables.
        $title = '';

        if ($this->hidden) {

            return $title;
        }

        // Get the label text from the XML element, defaulting to the element name.
        $title = $this->element['label'] ? (string)$this->element['label'] : (string)$this->element['name'];
        $title = $this->translateLabel ? MolajoText::_($title) : $title;

        return $title;
    }

    /**
     * Method to get the field label markup.
     *
     * @return  string  The field label markup.
     * @since   1.0
     */
    protected function getLabel()
    {
        // Initialise variables.
        $label = '';

        if ($this->hidden) {
            return $label;
        }

        // Get the label text from the XML element, defaulting to the element name.
        $text = $this->element['label'] ? (string)$this->element['label'] : (string)$this->element['name'];
        $text = $this->translateLabel ? MolajoText::_($text) : $text;

        // Build the class for the label.
        $class = !empty($this->description) ? 'hasTip' : '';
        $class = $this->required == true ? $class.' required' : $class;

        // Add the opening label tag and main attributes attributes.
        $label .= '<label id="'.$this->id.'-lbl" for="'.$this->id.'" class="'.$class.'"';

        // If a description is specified, use it to build a tooltip.
        if (!empty($this->description)) {
            $label .= ' title="'.htmlspecialchars(trim($text, ':').'::' .
                                                    ($this->translateDescription ? MolajoText::_($this->description)
                                                            : $this->description), ENT_COMPAT, 'UTF-8').'"';
        }

        // Add the label text and closing tag.
        if ($this->required) {
            $label .= '>'.$text.'<span class="star">&#160;*</span></label>';
        } else {
            $label .= '>'.$text.'</label>';
        }

        return $label;
    }

    /**
     * Method to get the name used for the field calendar tag.
     *
     * @param   string  $name  The field element name.
     *
     * @return  string  The name to be used for the field calendar tag.
     *
     * @since   1.0
     */
    protected function getName($name)
    {
        // Initialise variables.
        $name = '';

        // If there is a form control set for the attached form add it first.
        if ($this->formControl) {
            $name .= $this->formControl;
        }

        // If the field is in a group add the group control to the field name.
        if ($this->group) {
            // If we already have a name segment add the group control as another level.
            $groups = explode('.', $this->group);
            if ($name) {
                foreach ($groups as $group) {
                    $name .= '['.$group.']';
                }
            }
            else {
                $name .= array_shift($groups);
                foreach ($groups as $group) {
                    $name .= '['.$group.']';
                }
            }
        }

        // If we already have a name segment add the field name as another level.
        if ($name) {
            $name .= '['.$name.']';
        }
        else {
            $name .= $name;
        }

        // If the field should support multiple values add the final array segment.
        if ($this->multiple) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Method to get the field name used.
     *
     * @param   string  $name  The field element name.
     *
     * @return  string  The field name
     *
     * @since   1.0
     */
    protected function getFieldName($name)
    {
        if ($name) {
            return $name;
        }
        else {
            self::$count = self::$count + 1;
            return self::$generated_name.self::$count;
        }
    }
}
