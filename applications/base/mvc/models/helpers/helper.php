<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Model
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoModelHelper
{
    /**
     * validateCheckedOut
     *
     * Verify that the row has been checked out for update by the user
     *
     * @return  boolean  True if checked out to user
     * @since   1.0
     */
    public function validateCheckedOut($table)
    {
        if ($table->checked_out == Services::User()->get('id')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * validateAlias
     *
     * Verify that the alias is unique for this component
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateAlias($table)
    {

    }

    /**
     * validateDates
     *
     * Verify and set defaults for dates
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateDates($table)
    {

    }

    /**
     * validateLanguage
     *
     * Verify language setting
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateLanguage($table)
    {

    }


    /**
   	 * Checks if this field contains only letters a-z and A-Z.
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isAlphabetical($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphabetical($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field only contains letters & numbers (without spaces).
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isAlphaNumeric($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphaNumeric($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if the field is between a given minimum and maximum (includes min & max).
   	 *
   	 * @return	bool
   	 * @param	int $minimum				The minimum.
   	 * @param	int $maximum				The maximum.
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isBetween($minimum, $maximum, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isBetween($minimum, $maximum, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks this field for a boolean (true/false | 0/1).
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isBool($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isBool($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field only contains numbers 0-9.
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isDigital($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isDigital($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks this field for a valid e-mail address.
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isEmail($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isEmail($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// has error
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks for a valid file name (including dots but no slashes and other forbidden characters).
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isFilename($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isFilename($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// has error
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field was submitted & filled.
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isFilled($error = null)
   	{
   		// post/get data
   		$data = $this->getMethod(true);

   		// validate
   		if(!(isset($data[$this->attributes['name']]) && trim($data[$this->attributes['name']]) != ''))
   		{
   			if($error !== null) $this->setError($error);
   			return false;
   		}

   		return true;
   	}


   	/**
   	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error			The error message to set.
   	 * @param	bool[optional] $allowCommas		Do you want to use commas as a decimal separator?
   	 */
   	public function isFloat($error = null, $allowCommas = false)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isFloat($data[$this->attributes['name']], $allowCommas))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field is greater than another value.
   	 *
   	 * @return	bool
   	 * @param	int $minimum				The minimum.
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isGreaterThan($minimum, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isGreaterThan($minimum, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isInteger($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isInteger($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field is a proper ip address.
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isIp($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isIp($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field does not exceed the given maximum.
   	 *
   	 * @return	bool
   	 * @param	int $maximum				The maximum.
   	 * @param	int[optional] $error		The error message to set.
   	 */
   	public function isMaximum($maximum, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximum($maximum, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field's length is less (or equal) than the given maximum.
   	 *
   	 * @return	bool
   	 * @param	int $maximum				The maximum number of characters.
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isMaximumCharacters($maximum, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximumCharacters($maximum, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field is at least a given minimum.
   	 *
   	 * @return	bool
   	 * @param	int $minimum				The minimum.
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isMinimum($minimum, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimum($minimum, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field's length is more (or equal) than the given minimum.
   	 *
   	 * @return	bool
   	 * @param	int $minimum				The minimum number of characters.
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isMinimumCharacters($minimum, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimumCharacters($minimum, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Alias for isDigital (Field may only contain numbers 0-9).
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isNumeric($error = null)
   	{
   		return $this->isDigital($error);
   	}


   	/**
   	 * Checks if the field is smaller than a given maximum.
   	 *
   	 * @return	bool
   	 * @param	int $maximum				The maximum.
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isSmallerThan($maximum, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isSmallerThan($maximum, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if this field contains any string that doesn't have control characters (ASCII 0 - 31) but spaces are allowed.
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isString($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isString($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks this field for a valid url.
   	 *
   	 * @return	bool
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isURL($error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isURL($data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}


   	/**
   	 * Checks if the field validates against the regexp.
   	 *
   	 * @return	bool
   	 * @param	string $regexp				The regular expresion to test the value.
   	 * @param	string[optional] $error		The error message to set.
   	 */
   	public function isValidAgainstRegexp($regexp, $error = null)
   	{
   		// filled
   		if($this->isFilled())
   		{
   			// post/get data
   			$data = $this->getMethod(true);

   			// validate
   			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isValidAgainstRegexp((string) $regexp, $data[$this->attributes['name']]))
   			{
   				if($error !== null) $this->setError($error);
   				return false;
   			}

   			return true;
   		}

   		// not submitted
   		if($error !== null) $this->setError($error);
   		return false;
   	}

}
