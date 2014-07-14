=======
[ALPHA] Database
=======

[![Build Status](https://travis-ci.org/Molajo/Database.png?branch=master)](https://travis-ci.org/Molajo/Database)

Simple, uniform Database Services API for PHP applications enabling interaction with multiple Database types
and packages. Currently supported is the Joomla Framework database package which supports MySqli, Oracle, Pdo,
Postgresql, Sqlazure, Sqlite, and MS SQL Server.

## Instantiate the Database Adapter ##

Before using the Database Adapter, instantiate the desired database Handler and pass that object into the
Database Adapter constructor as a dependency. This example shows how to use the Joomla Database Handler and
assumes the $configuration object contains credentials required to access the Joomla database.

```php

    // 1. Instantiate the Handler Desired
    $options                    = array();
    $options['db_type']         = $configuration->db_type;
    $options['db_host']         = $configuration->db_host;
    $options['db_user']         = $configuration->db_user;
    $options['db_password']     = $configuration->db_password;
    $options['db_name']         = $configuration->db;
    $options['db_prefix']       = $configuration->db_prefix;
    $options['process_events'] = $configuration->process_events;
    $options['select']          = true;

    $class = 'Molajo\\Database\\Handler\\Joomla';
    $handler = new $class($options);

    // 2. Instantiate the Molajo Database Adapter, passing in the instantiated Handler
    $class = 'Molajo\\Database\\Adapter';
    $adapter = new $class($handler);

```

## Interacting with Databases ##

Once instantiated as defined above, the database $adapter can be used to execute database queries.


### Escape and Filter ###

It is an important security precaution to escape strings and filter numeric data before sending it to the database.

```php
    $secure_string_data = $adapter->escape("this isn't that hard.");

    $secure_numeric_data = (int) $integer;
```

### Quote and Namequote ###

String data must be quoted. Use namequote for table names, columns, etc.

```php
    $quoted_fieldname = $adapter->quotename('Fieldname');

    $where = ' where ' . $adapter->quotename('Fieldname')
        . ' = ' . $adapter->quote($adapter->escape("don't forget to escape"));

   ```

## Querying the Database ##

### Query Object ###

First, obtain a Query Object which you will use to build queries:

```php
    $query_object = $adapter->getQueryObject();

```

Molajo has three possible query options:

1. Return a single value
2. Return an array containing one or more rows of data where the row is defined as an object
3. Directly interacting with the database using an object.

### Select: Single Result ###

```php

    $query_object = $adapter->getQueryObject();

    $query_object->select('count(*)');
    $query_object->from('#__actions');

    $result = $this->product_result->loadResult();

    echo $result;

```

### Select Row(s) ###

```php

    $query_object = $adapter->getQueryObject();

    $query_object->select('*');
    $query_object->from('#__actions');
    $query_object->order('id');

    $results = $this->product_result->loadObjectList();

    if (count($results) > 0) {
        foreach ($results as $row) {
            $id = $row->id;
            $title = $row->title;
            //etc.
        }
    }

```

### Offset and Limit ###

To limit the number of rows returned and/or to start at a certain row, add offset and limit to the loadObjectList
parameters.

```php

    $query_object = $adapter->getQueryObject();

    $query_object->select('*');
    $query_object->from('#__actions');
    $query_object->order('id');

    $query_offset = 0;
    $query_count = 5;
    $results = $adapter->loadObjectList($query_offset, $query_count);

```

### Update ###

Get a query object, write the query, execute the query.

```php
   $query_object = $adapter->getQueryObject();

   $query_object->update('#__table');
   $query_object->set('x = y');
   $query_object->where('id = 1');

   $results = $adapter->execute();

```
### Delete ###

```php
   $query_object = $adapter->getQueryObject();

   $query_object->delete('#__table');
   $query_object->where('id = 1');

   $results = $adapter->execute();

```
### Other SQL ###

For other database functions, like creating a table or executing a stored procedure,
use *execute*.

```php
   // connect to $adapter, as described above
   $query_object = $adapter->getQueryObject();

   $sql = 'build this or whatever sql is needed';
   $results = $adapter->execute($sql);

   // that's it.

```


## Install using Composer from Packagist

### Step 1: Install composer in your project

```php
    curl -s https://getcomposer.org/installer | php
```

### Step 2: Create a **composer.json** file in your project root

```php
{
    "require": {
        "Molajo/Database": "1.*"
    }
}
```

### Step 3: Install via composer

```php
    php composer.phar install
```

## Requirements and Compliance
 * PHP framework independent, no dependencies
 * Requires PHP 5.4, or above
 * [Semantic Versioning](http://semver.org/)
 * Compliant with:
    * [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) and [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) Namespacing
    * [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) Coding Standards
 * [phpDocumentor2] (https://github.com/phpDocumentor/phpDocumentor2)
 * [phpUnit Testing] (https://github.com/sebastianbergmann/phpunit)
 * Author [AmyStephen](http://twitter.com/AmyStephen)
 * [Travis Continuous Improvement] (https://travis-ci.org/profile/Molajo)
 * Listed on [Packagist] (http://packagist.org) and installed using [Composer] (http://getcomposer.org/)
 * Use github to submit [pull requests](https://github.com/Molajo/Database/pulls) and [features](https://github.com/Molajo/Database/issues)
 * Licensed under the MIT License - see the `LICENSE` file for details
