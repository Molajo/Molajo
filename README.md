**NOT COMPLETE**

=======
Foundation
=======

[![Build Status](https://travis-ci.org/Molajo/Foundation.png?branch=master)](https://travis-ci.org/Molajo/Foundation)

Validates and filters input. Escapes and formats output.

Supports standard data type and PHP-specific filters and validation, value lists verification, callbacks, regex checking, and more.
 Use with rendering process to ensure proper escaping of output data and for special formatting needs.

## Basic Usage ##

Each field is processed by one, or many, field handlers for validation, filtering, or escaping.

```php
    try {
        $adapter = new Molajo/Foundation/Adapter
            ->($method, $field_name, $field_value, $fieldhandler_type_chain, $options);

    } catch (Exception $e) {
        //handle the exception here
    }

    // Success!
    echo $adapter->field_value;
```

###There are five input parameters:###

1. **$method** can be `validate`, `filter`, or `escape`;
2. **$field_name** name of the field containing the data value to be verified or filtered;
3. **$field_value** contains the data value to be verified or filtered;
4. **$fieldhandler_type_chain** one or more field handlers, separated by a comma, processed in left-to-right order;
5. **$options** associative array of named pair values required by field handlers.

###Two possible results:###

1. **Success** Retrieve the resulting field value from the object.
2. **Failure** Handle the exception.

#### Example Usage ####

The following example processes the `id` field using the `int`, `default`, and `required` field handlers.
The `options` associative array defines two data elements: `default` is the default value for the field, if needed;
the `required` element with a `true` value is used by the `required` field handler to verify a value has been
 provided.

Chaining is supported and field handlers are processed in left-to-right order. The example shows how to sequence
 the default before the required check in the field handler chain.

```php
    try {
        $fieldhandler_type_chain = array('int', 'default', 'required');
        $options = array('default' => 14, 'required' => true);
        $adapter = new Molajo/Foundation/Adapter
            ->('Validate', 'id', 12, $fieldhandler_type_chain, $options);

    } catch (Exception $e) {
        //handle the exception here
    }

    // Success!
    echo $adapter->field_value;

```
**Results:**

If the method was a success, simply retrieve the field value from the resulting object.

Use the Try/Catch pattern, as presented above, to catch thrown exceptions for errors.

## Available Foundations ##

### Accepted ###

* **Validate:** True if field value is true, 1, 'yes', or 'on.'
* **Filter:** If not true, 1, 'yes', or 'on', value is set to NULL.
* **Escape:** If not true, 1, 'yes', or 'on', value is set to NULL.

```php
    try {
        $fieldhandler_type_chain = array('accepted');
        $adapter = new Molajo/Foundation/Adapter
            ->('Validate', 'agreement', 1, $fieldhandler_type_chain);
```

* Alias
* Alpha
* Alphanumeric
* Arrays
* Boolean
* Callback
* Date
* Defaults
* Digit
* Email
* Encoded
* Equals
* Extensions
* Float
* Extensions
* Float
* Fullspecialchars
* Int
* Lower
* Maximum
* Mimetypes
* Minimum
- Notequal
* Numeric
* Raw
* Regex
* Required
* String
* Trim
* Upper
* Url
* Values (Inarray) (Inlist)

## System Requirements ##

* PHP 5.3.3, or above
* [PSR-0 compliant Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
* PHP Framework independent
* [optional] PHPUnit 3.5+ to execute the test suite (phpunit --version)

### Installation

#### Install using Composer from Packagist

**Step 1** Install composer in your project:

```php
    curl -s https://getcomposer.org/installer | php
```

**Step 2** Create a **composer.json** file in your project root:

```php
{
    "require": {
        "Molajo/Foundation": "1.*"
    }
}
```

**Step 3** Install via composer:

```php
    php composer.phar install
```

About
=====

Molajo Project observes the following:

 * [Semantic Versioning](http://semver.org/)
 * [PSR-0 Autoloader Interoperability](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
 * [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
 and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
 * [phpDocumentor2] (https://github.com/phpDocumentor/phpDocumentor2)
 * [phpUnit Testing] (https://github.com/sebastianbergmann/phpunit)
 * [Travis Continuous Improvement] (https://travis-ci.org/profile/Molajo)
 * [Packagist] (https://packagist.org)


Submitting pull requests and features
------------------------------------

Pull requests [GitHub](https://github.com/Molajo/Foundation/pulls)

Features [GitHub](https://github.com/Molajo/Foundation/issues)

Author
------

Amy Stephen - <AmyStephen@gmail.com> - <http://twitter.com/AmyStephen><br />
See also the list of [contributors](https://github.com/Molajo/Foundation/contributors) participating in this project.

License
-------

**Molajo Foundation** is licensed under the MIT License - see the `LICENSE` file for details

More Information
----------------
- [Extend](https://github.com/Molajo/Foundation/blob/master/.dev/Doc/extend.md)
- [Install](https://github.com/Molajo/Foundation/blob/master/.dev/Doc/install.md)
