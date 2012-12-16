<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Service\Services\Text;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Text
 *
 * @package     Niambie
 * @subpackage  Service
 * @since       1.0
 */
Class TextService
{
    /**
     * Add rows to model
     *
     * @param   string  $extension_name
     * @param   string  $model_name
     * @param   string  $source_path
     * @param   string  $destination_path
     *
     * @return  bool
     * @since   1.0
     */
    public function extension($model_name, $source_path = null, $destination_path = null)
    {
        $controller = new CreateController();
        $model_registry = ucfirst(strtolower($model_name)) . DATA_SOURCE_LITERAL;

        $data = new \stdClass();

        $data->title = $model_name;
        $data->model_name = $model_name;
        $controller->data = $data;

        $id = $controller->execute();
        if ($id === false) {
            //install failed
            return false;
        }
    }

    /**
     * Retrieves Lorem Ipsum text
     *
     * Usage:
     * Services::Text()->getPlaceHolderText(4, 20, 'html', 1);
     *
     * @param   int     $paragraph_word_count - number of words per paragraph
     * @param   int     $paragraph_count
     * @param   string  $format               txt, plain, html
     * @param   $start_with_lorem_ipsum 0 or 1
     *
     * @return  string
     * @since   1.0
     */
    public function getPlaceHolderText(
        $paragraph_word_count,
        $paragraph_count,
        $format,
        $start_with_lorem_ipsum
    ) {
        /**
        $generator = new LoremIpsumGenerator($paragraph_word_count);

        return ucfirst(
        $generator->getContent(
        $paragraph_word_count * $paragraph_count,
        $format,
        $start_with_lorem_ipsum
        )
        );
         */
    }

    /**
     * getDatalist creates named pair lists
     *
     * @param   string  $model_name     ex. Articles or Templateviews
     * @param   string  $model_type     ex. Datalist, ResourceLists, Database, etc.
     * @param   string  $parameters
     *
     * @return  array|bool|object
     * @since   1.0
     */
    public function getDatalist($model_name, $model_type, $parameters)
    {
        $model_name = ucfirst(strtolower($model_name));
        $model_type = ucfirst(strtolower($model_type));
        $model_registry = $model_name . $model_type;

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($model_type, $model_name, 1);

        $multiple = (int)Services::Registry()->get($model_registry, 'multiple');
        $size = (int)Services::Registry()->get($model_registry, 'size');

        if ((int)$multiple == 1) {
            if ((int)$size == 0) {
                $size = 5;
            }
        } else {
            $multiple = 0;
            $size = 0;
        }

        $values = Services::Registry()->get($model_registry, 'values', array());
        if ($values === false || count($values) === 0) {

        } else {
            $query_results = array();

            $temp_row = new \stdClass();

            $temp_row->listitems = $values;
            $temp_row->multiple = $multiple;
            $temp_row->size = $size;

            $query_results[] = $temp_row;

            return $query_results;
        }

        $values = $this->getQueryResults($controller, $model_type, $parameters);

        $query_results = array();

        $temp_row = new \stdClass();

        $temp_row->listitems = $values;
        $temp_row->multiple = $multiple;
        $temp_row->size = $size;

        $query_results[] = $temp_row;

        return $query_results;
    }

    /**
     * getQueryResults for list
     *
     * @param   $controller
     * @param   $model_type      ex. ArticlesResource
     * @param   $parameters
     *
     * @return  object
     * @since   1.0
     */
    protected function getQueryResults($controller, $model_type, $parameters)
    {
        $registry_entry = $controller->get('registry_entry');

        if ($registry_entry == '') {
            $results = array();
        } else {
            $results = Services::Registry()->get(DATALIST_LITERAL, $registry_entry, array());
            if (count($results) > 0) {
                return $results;
            }
        }

        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $primary_key = $controller->get('primary_key', 'id', 'model_registry');
        $name_key = $controller->get('name_key', '', 'data_registry');

        $model_registry = $controller->get('model_registry');

        $controller->model->set('model_offset', 0, 'data_registry');
        $controller->model->set('model_count', 999999, 'data_registry');

        $fields = Services::Registry()->get($model_registry, FIELDS_LITERAL, 'data_registry');

        $first = true;
        if (count($fields) < 2) {

            $controller->model->query->select(
                'DISTINCT '
                    . $controller->model->db->qn($primary_prefix . '.' . $primary_key) . ' as id'
            );

            $controller->model->query->select(
                $controller->model->db->qn(
                    $primary_prefix
                        . '.' . $name_key
                ) . ' as value'
            );

            $controller->model->query->order(
                $controller->model->db->qn(
                    $primary_prefix
                        . '.' . $name_key
                ) . ' ASC'
            );

        } else {

            $ordering = '';
            foreach ($fields as $field) {

                if (isset($field['alias'])) {
                    $alias = $field['alias'];
                } else {
                    $alias = $primary_prefix;
                }

                $name = $field['name'];

                if ($first) {
                    $first = false;
                    $as = 'id';
                    $distinct = QUERY_OBJECT_DISTINCT;

                } else {
                    $as = 'value';
                    $distinct = '';
                    $ordering = $alias . '.' . $name;
                }

                $controller->model->query->select(
                    $distinct . ' '
                        . $controller->model->db->qn($alias . '.' . $name) . ' as ' . $as
                );
            }

            $controller->model->query->order($controller->model->db->qn($ordering) . ' ASC');
        }

        if ($controller->get('extension_instance_id', 0) == 0) {
        } else {
            $this->setWhereCriteria(
                'extension_instance_id',
                $controller->get('extension_instance_id'),
                $primary_prefix,
                $controller
            );
        }

        if ($controller->get('catalog_type_id', 0) == 0) {
        } else {
            $this->setWhereCriteria(
                'catalog_type_id',
                $controller->get('catalog_type_id'),
                $primary_prefix,
                $controller
            );
        }

        $query_object = QUERY_OBJECT_DISTINCT;

        $offset = $controller->set('model_offset', 0, 'model_registry');
        $count = $controller->set('model_count', 9999999);

        return $controller->getData($query_object);
    }

    /**
     * setWhereCriteria
     *
     * @param   $field
     * @param   $value
     * @param   $alias
     * @param   $connection
     *
     * @return  void
     * @since   1.0
     */
    protected function setWhereCriteria($field, $value, $alias, $connection)
    {
        if (strrpos($value, ',') > 0) {
            $connection->model->query->where(
                $connection->model->db->qn($alias . '.' . $field)
                    . ' IN (' . $value . ')'
            );

        } elseif ((int)$value == 0) {

        } else {
            $connection->model->query->where(
                $connection->model->db->qn($alias . '.' . $field) . ' = ' . (int)$value
            );
        }

        return;
    }

    /**
     * add publishedStatus information to list query
     *
     * @return  void
     * @since   1.0
     */
    protected function publishedStatus($controller)
    {
        $primary_prefix = Services::Registry()->get($controller->model_registry, 'primary_prefix', 'a');

        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('status')
                . ' > ' . STATUS_UNPUBLISHED
        );

        $controller->model->query->where(
            '(' . $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('start_publishing_datetime')
                . ' = ' . $controller->model->db->q($controller->model->null_date)
                . ' OR ' . $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('start_publishing_datetime')
                . ' <= ' . $controller->model->db->q($controller->model->now) . ')'
        );

        $controller->model->query->where(
            '(' . $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('stop_publishing_datetime')
                . ' = ' . $controller->model->db->q($controller->model->null_date)
                . ' OR ' . $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('stop_publishing_datetime')
                . ' >= ' . $controller->model->db->q($controller->model->now) . ')'
        );

        return;
    }

    /**
     * buildSelectlist - build select list for insertion into webpage
     *
     * @param   string  $listname
     * @param   array   $items
     * @param   int     $multiple
     * @param   int     $size
     * @param   string  $selected
     *
     * @return  array
     * @since   1.0
     */
    public function buildSelectlist($listname, $items, $multiple = 0, $size = 5, $selected = null)
    {
        $query_results = array();

        if (count($items) == 0) {
            return false;
        }

        foreach ($items as $item) {

            $temp_row = new \stdClass();

            $temp_row->listname = $listname;
            $temp_row->id = $item->id;
            $temp_row->value = $item->value;

            if ($temp_row->id == $selected) {
                $temp_row->selected = ' selected ';
            } else {
                $temp_row->selected = '';
            }

            $temp_row->multiple = '';

            if ($multiple == 1) {
                $temp_row->multiple = ' multiple ';
                if ((int)$size == 0) {
                    $temp_row->multiple .= 'size=5 ';
                } else {
                    $temp_row->multiple .= 'size=' . (int)$size;
                }
            }
            $query_results[] = $temp_row;
        }

        return $query_results;
    }

    /** tests

    $results = Services::Text()->convertNumberToText(0);
    if ($results == 'zero') {
    } else {
    echo 'This should be zero but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(5);
    if ($results == 'five') {
    } else {
    echo 'This should be five but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(15);
    if ($results == 'fifteen') {
    } else {
    echo 'This should be fifteen but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(20);
    if ($results == 'twenty') {
    } else {
    echo 'This should be twenty but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(23, 0, 1);
    if ($results == 'twentythree') {
    } else {
    echo 'This should be twentythree but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(100);
    if ($results == 'one hundred') {
    } else {
    echo 'This should be one hundred but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(123);
    if ($results == 'one hundred and twenty three') {
    } else {
    echo 'This should be one hundred and twenty three but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(103);
    if ($results == 'one hundred three') {
    } else {
    echo 'This should be one hundred three but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(1000);
    if ($results == 'one thousand') {
    } else {
    echo 'This should be one thousand but is: ' . $results;
    die;
    }

    $results = Services::Text()->convertNumberToText(923403123);
    if ($results == 'nine hundred and twenty three million, four hundred three thousand, one hundred and twenty three') {
    } else {
    echo 'This should be nine hundred and twenty three million, four hundred three thousand, one hundred and twenty three but is: ' . $results;
    die;
    }
     **/

    /**
     * Converts a numeric value, with or without a decimal, up to a 999 quattuordecillion to words
     *
     * @param   string  $number
     * @param   string  $translate
     * @param   string  $remove_spaces
     *
     * @return  string
     * @since   1.0
     */
    public function convertNumberToText($number, $translate = 1, $remove_spaces = 0)
    {
        if ($translate == 0) {
            $translate = 'en-GB';
        } else {
            $translate = '';
        }

        $results = '';

        $split = explode('.', $number);
        if (count($split) > 1) {
            $decimal = $split[1];
        } else {
            $decimal = null;
        }

        $sign = '';
        if (substr($split[0], 0, 1) == '+') {
            $split[0] = substr($split[0], 1, strlen($split[0]) - 1);
        }
        if (substr($split[0], 0, 1) == '-') {
            $split[0] = substr($split[0], 1, strlen($split[0]) - 1);
            $sign = Services::Language()->translate('negative', $translate) . ' ';
        }

        if ((int)$number == 0) {
            return Services::Language()->translate('zero', $translate);
        }

        $word_value = $sign;

        $reverseDigits = str_split($number, 1);
        $number = implode(array_reverse($reverseDigits));

        if ((strlen($number) % 3) == 0) {
            $padToSetsOfThree = strlen($number);
        } else {
            $padToSetsOfThree = 3 - (strlen($number) % 3) + strlen($number);
        }

        $number = str_pad($number, $padToSetsOfThree, 0, STR_PAD_RIGHT);
        $groups = str_split($number, 3);

        $onesDigit = null;
        $tensDigit = null;
        $hundredsDigit = null;

        $temp_word_value = '';

        $i = 0;
        foreach ($groups as $digits) {

            $digit = str_split($digits, 1);

            $onesDigit = $digit[0];
            $tensDigit = $digit[1];
            $hundredsDigit = $digit[2];

            if ($onesDigit == 0 && $tensDigit == 0 && $hundredsDigit == 0) {
            } else {

                $temp_word_value = $this->convertPlaceValueOnes(
                    $onesDigit,
                    $translate
                );

                $temp_word_value = $this->convertPlaceValueTens(
                    $tensDigit,
                    $onesDigit,
                    $temp_word_value,
                    $translate
                );

                $temp_word_value = $this->convertPlaceValueHundreds(
                    $hundredsDigit,
                    $tensDigit,
                    $temp_word_value,
                    $onesDigit,
                    $translate
                );

                $temp_word_value .= ' ' . $this->convertGrouping($i, $translate);
            }

            $onesDigit = null;
            $tensDigit = null;
            $hundredsDigit = null;

            if (trim($word_value) == '') {
                $word_value = trim($temp_word_value);
            } else {
                $word_value = trim($temp_word_value) . ', ' . $word_value;
            }
            $temp_word_value = '';
            $i++;
        }

        if ($decimal === null) {
        } else {
            $word_value .= ' ' . Services::Language()->translate('point', $translate) . ' ' . $decimal;
        }

        if ((int)$remove_spaces == 1) {
            $word_value = str_replace(' ', '', $word_value);
        }

        return trim($word_value);
    }

    /**
     * Convert the ones place value to a word
     *
     * @param   string  $digit
     * @param   string  $translate
     *
     * @return  bool
     * @since   1.0
     */
    protected function convertPlaceValueOnes($digit, $translate)
    {
        switch ($digit) {

            case '0':
                return '';
            case '1':
                return Services::Language()->translate('one', $translate);
            case '2':
                return Services::Language()->translate('two', $translate);
            case '3':
                return Services::Language()->translate('three', $translate);
            case '4':
                return Services::Language()->translate('four', $translate);
            case '5':
                return Services::Language()->translate('five', $translate);
            case '6':
                return Services::Language()->translate('six', $translate);
            case '7':
                return Services::Language()->translate('seven', $translate);
            case '8':
                return Services::Language()->translate('eight', $translate);
            case '9':
                return Services::Language()->translate('nine', $translate);

        }

        return false;
    }

    /**
     * Convert the tens placeholder to a word, combining with the ones placeholder word
     *
     * @param   string  $tensDigit
     * @param   string  $onesDigit
     * @param   string  $translate
     *
     * @return  bool
     * @since   1.0
     */
    protected function convertPlaceValueTens($tensDigit, $onesDigit, $onesWord, $translate)
    {
        if ($onesDigit == 0) {

            switch ($tensDigit) {

                case 0:
                    return '';
                case 1:
                    return Services::Language()->translate('ten', $translate);
                case 2:
                    return Services::Language()->translate('twenty', $translate);
                case 3:
                    return Services::Language()->translate('thirty', $translate);
                case 4:
                    return Services::Language()->translate('forty', $translate);
                case 5:
                    return Services::Language()->translate('fifty', $translate);
                case 6:
                    return Services::Language()->translate('sixty', $translate);
                case 7:
                    return Services::Language()->translate('seventy', $translate);
                case 8:
                    return Services::Language()->translate('eighty', $translate);
                case 9:
                    return Services::Language()->translate('ninety', $translate);

            }

        } elseif ($tensDigit == 0) {
            return $onesWord;

        } elseif ($tensDigit == 1) {

            switch ($onesDigit) {

                case 1:
                    return Services::Language()->translate('eleven', $translate);
                case 2:
                    return Services::Language()->translate('twelve', $translate);
                case 3:
                    return Services::Language()->translate('thirteen', $translate);
                case 4:
                    return Services::Language()->translate('fourteen', $translate);
                case 5:
                    return Services::Language()->translate('fifteen', $translate);
                case 6:
                    return Services::Language()->translate('sixteen', $translate);
                case 7:
                    return Services::Language()->translate('seventeen', $translate);
                case 8:
                    return Services::Language()->translate('eighteen', $translate);
                case 9:
                    return Services::Language()->translate('nineteen', $translate);
            }

        } else {

            switch ($tensDigit) {

                case 2:
                    return Services::Language()->translate('twenty', $translate) . ' ' . $onesWord;
                case 3:
                    return Services::Language()->translate('thirty', $translate) . ' ' . $onesWord;
                case 4:
                    return Services::Language()->translate('forty', $translate) . ' ' . $onesWord;
                case 5:
                    return Services::Language()->translate('fifty', $translate) . ' ' . $onesWord;
                case 6:
                    return Services::Language()->translate('sixty', $translate) . ' ' . $onesWord;
                case 7:
                    return Services::Language()->translate('seventy', $translate) . ' ' . $onesWord;
                case 8:
                    return Services::Language()->translate('eighty', $translate) . ' ' . $onesWord;
                case 9:
                    return Services::Language()->translate('ninety', $translate) . ' ' . $onesWord;
            }

        }

        return '';
    }

    /**
     * Creates words for Hundreds Digit, combining previously determined tens digit word
     *
     * @param   string  $hundredsDigit
     * @param   string  $tensDigit
     * @param   string  $tensWord
     * @param   string  $onesDigit
     * @param   string  $translate
     *
     * @return  string
     * @since   1.0
     */
    protected function convertPlaceValueHundreds($hundredsDigit, $tensDigit, $tensWord, $onesDigit, $translate)
    {
        $temp = '';

        switch ($hundredsDigit) {

            case 0:
                return $tensWord;
                break;
            case 1:
                $temp = Services::Language()->translate('one', $translate);
                break;
            case 2:
                $temp = Services::Language()->translate('two', $translate);
                break;
            case 3:
                $temp = Services::Language()->translate('three', $translate);
                break;
            case 4:
                $temp = Services::Language()->translate('four', $translate);
                break;
            case 5:
                $temp = Services::Language()->translate('five', $translate);
                break;
            case 6:
                $temp = Services::Language()->translate('six', $translate);
                break;
            case 7:
                $temp = Services::Language()->translate('seven', $translate);
                break;
            case 8:
                $temp = Services::Language()->translate('eight', $translate);
                break;
            case 9:
                $temp = Services::Language()->translate('nine', $translate);
                break;
        }

        $temp .= ' ' . Services::Language()->translate('hundred', $translate);

        if ($tensDigit == 0 && $onesDigit == 0) {
            return $temp;

        } elseif ($tensDigit == 0) {
            return $temp . ' ' . $tensWord;
        }

        return $temp . ' ' . Services::Language()->translate('and', $translate) . ' ' . $tensWord;
    }

    /**
     * Creates the high-level word associated with the numeric group
     *
     * ex. for 300,000: we want 'thousand' to combine with 'three hundred' to make 'three hundred thousand'
     *
     * Called once for each set of (up to) three numbers over one hundred.
     *
     * Ex. for 3,000,000 it will be called for the middle "000" and the first digit, 3
     *
     * Source: http://en.wikipedia.org/wiki/Names_of_large_numbers
     *
     * @param   string  $number
     * @param   string  $translate
     *
     * @return  string
     * @since   1.0
     */
    protected function convertGrouping($number, $translate)
    {
        switch ($number) {

            case 0:
                return '';
            case 1:
                return Services::Language()->translate('thousand');
            case 2:
                return Services::Language()->translate('million');
            case 3:
                return Services::Language()->translate('billion');
            case 4:
                return Services::Language()->translate('trillion');
            case 5:
                return Services::Language()->translate('quadrillion');
            case 6:
                return Services::Language()->translate('quintillion');
            case 7:
                return Services::Language()->translate('sextillion');
            case 8:
                return Services::Language()->translate('septillion');
            case 9:
                return Services::Language()->translate('septillion');
            case 10:
                return Services::Language()->translate('octillion');
            case 11:
                return Services::Language()->translate('nonillion');
            case 12:
                return Services::Language()->translate('decillion');
            case 13:
                return Services::Language()->translate('undecillion');
            case 14:
                return Services::Language()->translate('duodecillion');
            case 15:
                return Services::Language()->translate('tredecillion');
        }

        return Services::Language()->translate('quattuordecillion');
    }
}
