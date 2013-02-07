Using Filesystem
=============

Filesystem is available on Packagist ([Molajo/Filesystem](http://packagist.org/packages/molajo/filesystem))
and as such installable via [Composer](http://getcomposer.org/).

Filesystem's built-in adapters provide sufficient directory and file services for most projects. Out of the box,
local filesystem, FTP Server, Github, Registry, and Streaming are supported. Creating new Adapters is a simple
process for most PHP programmers.

## Usage

Basic example here

```php
<?php

use Molajo\Filesystem\Service as FilesystemServices;

// Enable access to filesystem services
$services = new FilesystemServices();

// Enable access to the local filesystem
$local = $services->addFilesystem('local');

// Read a file
$results = $local->read('path/to/your.file');
 ```


Example of copying data between filesystems

```php
<?php

use Molajo\Filesystem\Service as FilesystemServices;

// Enable access to filesystem services
$services = new FilesystemServices();

// Enable access to the local filesystem
$local = $services->addFilesystem('local');

// Enable access to the cloud
$cloud = $services->addFilesystem('cloud');

// Backup a folder from the local system to the cloud
$results = $services->copy('path/to/local/folder', 'path/to/cloud/destination', 'local', 'cloud');
 ```

stuff

> Tip: goes here

Getting Started
---------------

dkkdd kdkdkkd kdkdkdk

## System Requirements

* PSR-0 capable autoloader
* PHP 5.3, or above
* [optional] PHPUnit 3.5+ to execute the test suite (phpunit --version)

Link to system

## Installation

Filesystem is available on Packagist ([Molajo/Filesystem](http://packagist.org/packages/molajo/filesystem))
and as such installable via [Composer](http://getcomposer.org/).

If you do not use Composer, you can download the code from ([Github](https://github.com/Molajo/Filesystem)).
Link to ([manual install steps](https://github.com/Molajo/Filesystem)).

### Autoloader

PSR-0 compatible autoloader (e.g. the [Symfony2 ClassLoader component](https://github.com/symfony/ClassLoader))
to load Filesystem classes.

### Enable within your PHP application

<?php

use Molajo\Filesystem\Service as FilesystemServices;

// Enable access to filesystem services
$services = new FilesystemServices();

// Enable access to the local filesystem
$local = $services->addFilesystem('local');
```

### Basic File Services

<?php

// Read a File
$data = $local->read('path\to\your\file');

// Create a new file or save an existing file
$data = $local->write('path\to\your\file');

// Delete a file
$results = $local->write('path\to\your\file');

// Copy a file
$data = $local->read('path\to\your\file');

```php


### Basic Directory Services

<?php

// List of Files in Directory
$data = $services->read('path\to\your\file');

// Recursively list all files and subfolders within a Directory
$data = $services->read('path\to\your\file');

// Copy a folder
$data = $services->write('path\to\your\file');

// Move a folder
$data = $services->write('path\to\your\file');

// Delete a file
$results = $services->write('path\to\your\file');

```php

More file services...

### Exception Handling

<?php

// Read a File
$data = $services->read('path\to\your\file');

// Create a new file or save an existing file
$data = $services->write('path\to\your\file');

// Delete a file
$results = $services->write('path\to\your\file');

```php

Short conclusion.


About
=====

Molajo Project has adopted the following:

 * ([Semantic Versioning](http://semver.org/))
 * ([PSR-0 Autoloader Interoperability](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md))
 * ([PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md))
 and ([PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md))
 * phpUnit Testing
 * phpDoc

Requirements
------------

- PHP 5.3+
- [optional] PHPUnit 3.5+ to execute the test suite (phpunit --version)

Submitting bugs and feature requests
------------------------------------

Bugs and feature request are tracked on [GitHub](https://github.com/Molajo/Fileservices/issues)

Author
------

Amy Stephen - <AmyStephen@gmail.com> - <http://twitter.com/AmyStephen><br />
See also the list of [contributors](https://github.com/Molajo/Fileservices/contributors) participating in this project.

License
-------

Molajo Filesystem is licensed under the MIT License - see the `LICENSE` file for details

Acknowledgements
----------------

[W3C File API: Directories and System] W3C Working Draft 17 April 2012 → http://www.w3.org/TR/file-system-api/
specifications were followed, as closely as possible.

More Information
----------------

See the doc folder
