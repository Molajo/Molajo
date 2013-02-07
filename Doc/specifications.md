Filesystem Package
=====================

This document describes the Molajo Filesystem, a collection of interfaces, adapters, and concrete classes which
provide a common approach for applications to interact with files and folders regardless of the type of host filesystem.
Adapters are used to define filesystems in a common way so that interacting with FTP services is done using
the same approach as local services or remote cloud-based services.

The approach borrows liberally from [W3C File API: Directories and System] W3C Working Draft 17 April 2012 → http://www.w3.org/TR/file-system-api/
and is architected with interoperability with other PHP libraries in mind.



1. Overview
-----------------

### 1.1 Basics:

Following are the filesystem interfaces:

    Namespace: Molajo\Filesystem

1. **Filesystem Interface** represents a filesystem.
2. **Adapter Interface** represents a specific type of filesystem, like a local server, an FTP server, a temporary registry, or a cloud-based filesystem service.
3. **Entry Interface** represents a set of shared properties and methods used by directory and file interfaces within a filesystem.
4. **Directory Interface** represents a directory and methods needed to create, read, update, delete, and list directory data.
5. **File Interface** represents a file and methods needed to create, read, update, delete, and list file data.

### 1.2 Benefits:

1. **DRY** code reuse between adapters
2. **Events** file processes can be processed the same using Events and backups, logging, permissions, and so on.
3. **Storage** Where files are stored can change without impact
4. **Reuse** Across the industry, code can be shared. An Adapter created for one library could be used in the filesystem of another library.
5. **Extensible** Approach shares common code and allows customizing by Adapter and Library.

### 1.3 Implementation:

The filesystem interfaces are implemented using adapters for filesystem types and concrete classes which represent files and folders on the filesystems.

### 1.3.1 Adapters:

Defines a specific type of filesystem and implement of common set of methods for interacting with files and folders on this type of system.

    Namespace: Molajo\Filesystem\Adapter

1. **Adapter** abstract class that represents an adapter and implements the Adapter Interface.
2. **Ftp** FTP server adapter (extends the **Adapter** class, as do each of the following.)
3. **Github** Github adapter
4. **Ldap** Ldap adapter
5. **Local** Local filesystem adapter
6. **Media** Media filesystem adapter
7. **Registry** Registry adapter
8. **Stream** Stream adapter

### 1.3.2 Filesystem Concrete Classes:

Defines files, directories, metadata, and methods needed for interaction with filesystems.

    Namespace: Molajo\Filesystem\Concrete

1. **Filesystem** abstract class that represents an instance of the current Adapter.
2. **Entry** abstract class that represents file and folder shared information and methods.
3. **Directory** concrete class that extends the **Entry** class and interacts with Directories.
4. **File** concrete class that extends the **Entry** class and interacts with Files.

### 1.3.3 Molajo Services Locator:

When the Filesystem package is used within **Molajo**, filesystem resources are accessed via the *Services Locator which is
 implemented using a Facade Pattern to mask the complexity of creating class instances, injecting dependencies,
 connecting to the adapter filesystems, verifying permissions, and, finally, retrieving the
 requested ata.

    Namespace: Molajo\Services\Filesystem

1. **Files** abstract class that represents an instance of the current Adapter.

### 1.4 Implementation Options:

1. **Simple**

2. **Advanced**

2. Specification
-----------------

### 2.1 Adapter Interface

Describes an adapter instance for the filesystem.

    Namespace: Molajo\Filesystem\Adapter

#### Constants

Constants are not defined for this interface.

#### Attributes

1. **root** The root directory of the filesystem is REQUIRED.
2. **adapter** The adapter instance is REQUIRED and should be injected into the class constructor.
3. **options** Options CAN be provided as an associative array of key value pairs. The following values
    are used within the FTP Adapter for securing access to the FTP Server. Values needed by
    the Adapter can be passed in this way.

*  **username**
*  **password**
*  **host**
*  **port**
*  **root**
*  **timeout**
*  **is_passive**

#### Methods

The **Adapter Interface** has the following methods:

<table>
	<thead>
		<tr>
			<th scope="col">Method</th>
			<th scope="col">Description</th>
			<th scope="col">Parameters</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>getRoot</td>
			<td>Get Root for Filesystem.</td>
			<td></td>
		</tr>
		<tr>
			<td>setRoot</td>
			<td>Set Root for Filesystem.</td>
			<td>$root</td>
		</tr>
		<tr>
			<td>setUsername</td>
			<td>Set the username</td>
			<td>$username</td>
		</tr>
		<tr>
			<td>getUsername</td>
			<td>Get the username</td>
			<td></td>
		</tr>
		<tr>
			<td>setPassword</td>
			<td>Set the password</td>
			<td>$password</td>
		</tr>
		<tr>
			<td>getPassword</td>
			<td>Get the passwor</td>
			<td></td>
		</tr>
		<tr>
			<td>setHost</td>
			<td>Set the host</td>
			<td>$host</td>
		</tr>
		<tr>
			<td>getHost</td>
			<td>Get the host</td>
			<td></td>
		</tr>
        <tr>
			<td>setPort</td>
			<td>Set the Port</td>
			<td>$port = 21</td>
		</tr>
		<tr>
			<td>getPort</td>
			<td>Get the Port</td>
			<td></td>
		</tr>
		<tr>
			<td>setTimeout</td>
			<td>Set the Timeout</td>
			<td>$timeout = 15</td>
		</tr>
		<tr>
			<td>getTimeout</td>
			<td>Get the Timeout</td>
			<td></td>
		</tr>
        <tr>
			<td>setIs_passive</td>
			<td>Set the Passive Indicator</td>
			<td>$is_passive = 1</td>
		</tr>
		<tr>
			<td>getIs_passive</td>
			<td>Get the Passive indicator</td>
			<td></td>
		</tr>
        <tr>
			<td>connect</td>
			<td>Connect to Filesystem</td>
			<td></td>
		</tr>
		<tr>
			<td>isConnected</td>
			<td>Checks to see if the connection is set, returning true, or not, returning false</td>
			<td>$timeout = 15</td>
		</tr>
		<tr>
			<td>login</td>
			<td>Method to login to a server once connected</td>
			<td></td>
		</tr>
		<tr>
			<td>setConnection</td>
			<td>Set the Connection</td>
			<td>$connection</td>
		</tr>
		<tr>
			<td>getConnection</td>
			<td>Get the Connection</td>
			<td></td>
		</tr>
		<tr>
			<td>__destruct</td>
			<td>Triggers close</td>
			<td></td>
		</tr>
		<tr>
			<td>close</td>
			<td>Close the Connection</td>
			<td></td>
		</tr>
	</tbody>
</table>


### 2.2 Filesystem Interface

This interface represents a generic filesystem.

    Namespace: Molajo\Filesystem\Filesystem

#### Constants

* const unsigned short TEMPORARY = 0;
* const unsigned short PERSISTENT = 1;

#### Attributes

1. **name** Filesystem Name IS REQUIRED.
2. **adapter** The adapter instance is REQUIRED to be injected into the class constructor.
3. **options** Associative array of key value pairs
4. **root** The root directory of the filesystem is REQUIRED.

#### Methods

The **Filesystem Interface** has the following methods:

<table>
	<thead>
		<tr>
			<th scope="col">Method</th>
			<th scope="col">Description</th>
			<th scope="col">Parameters</th>
		</tr>
	</thead>
	<tbody>
			<tr>
    			<td>Constructor</td>
    			<td>Sets values for injected parameters</td>
    			<td>$adapter, $options</td>
    		</tr>
		<tr>
			<td>getAdapter</td>
			<td>Retrieves adapter instance.</td>
			<td></td>
		</tr>
		<tr>
			<td>setAdapter</td>
			<td>Set current filesystem adapter with adapter instance passed in..</td>
			<td>$adapter</td>
		</tr>
		<tr>
			<td>getRoot</td>
			<td>Retrieves the root of the filesystem.</td>
			<td></td>
		</tr>
		<tr>
			<td>setRoot</td>
			<td>Sets the Filesystem name.</td>
			<td>$root</td>
		</tr>
		<tr>
			<td>options</td>
			<td>Array of parameter values.</td>
			<td>$options</td>
		</tr>
		<tr>
			<td>root</td>
			<td>Root Directory for Filesystem.</td>
			<td>$root</td>
		</tr>
	</tbody>
</table>

### 2.3 Entries Interface

Interface representing the common properties and methods for both files and directory entries in a filesystem.

    Namespace: Molajo\Filesystem\Entries

#### Constants

Constants are not defined for this interface.

#### Attributes

1. **filesystem** type FileSystem
2. **fullPath**
3. **isDirectory** Boolean Set to 1 if this entry is a directory.
4. **isFile** boolean Set to 1 if this entry is a file.
5. **name**
6. **Metadata** defined in *Metadata Interface*, below

#### Methods

The **Entries Interface** has XXX methods: *copyTo*, *getMetadata*, *moveTo*, *remove*, and *toURL*.


<table>
	<thead>
		<tr>
			<th scope="col">Method</th>
			<th scope="col">Description</th>
			<th scope="col">Parameters</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>setName</td>
			<td>Sets the current filesystem adapter.</td>
			<td>$path</td>
		</tr>
		<tr>
			<td>getName</td>
			<td>Sets as the name of the file or directory specified in the path.</td>
			<td></td>
		</tr>
		<tr>
			<td>getAbsolutePath</td>
			<td>Retrieves the absolute path, which is the relative path from the root directory, prepended with a '/'.</td>
			<td></td>
		</tr>
		<tr>
			<td>getRelativePath</td>
			<td>Retrieves the relative path, which is the path between a specific directory to  a specific file or directory.</td>
			<td></td>
		</tr>
		<tr>
			<td>toUrl</td>
			<td>Returns a URL that can be used to identify this entry. Ex. filesystem:http://example.domain/persistent-or-temporary/path/to/file.html.</td>
			<td></td>
		</tr>
		<tr>
			<td>exists</td>
			<td>Determine if the file or directory specified in path exists</td>
			<td></td>
		</tr>
		<tr>
			<td>getType</td>
			<td>Returns the value 'directory, 'file' or 'link' for the type determined from the path.</td>
			<td></td>
		</tr>
		<tr>
			<td>isDirectory</td>
			<td>Returns true if the path is a directory</td>
			<td></td>
		</tr>
		<tr>
			<td>isFile</td>
			<td>Returns true if the path is a file</td>
			<td></td>
		</tr>
		<tr>
			<td>isLink</td>
			<td>Returns true if the path is a link</td>
			<td></td>
		</tr>
		<tr>
			<td>getOwner</td>
			<td>Returns the owner of the file or directory defined in the path</td>
			<td>$group</td>
		</tr>
		<tr>
			<td>setOwner</td>
			<td>Changes the owner to the value specified in group for the file or directory defined in the path</td>
			<td>$group</td>
		</tr>
		<tr>
			<td>getGroup</td>
			<td>Returns the group for the file or directory defined in the path</td>
			<td></td>
		</tr>
		<tr>
			<td>setGroup</td>
			<td>Changes the group to the value specified for the file or</td>
			<td></td>
		</tr>
		<tr>
			<td>getPermissions</td>
			<td>Returns associative array: 'read', 'update', 'execute'  as true or false for the set group: 'owner', 'group', or 'world' and the set value for path</td>
			<td></td>
		</tr>
		<tr>
			<td>setPermissions</td>
			<td>Set the values in the associative array $this->permissions where each group will have a set of three actions: 'read', 'update', 'execute', each of which will have true or false.</td>
			<td></td>
		</tr>
		<tr>
			<td>isReadable</td>
			<td>Tests if the group specified: 'owner', 'group', or 'world' has read access</td>
			<td>$group</td>
		</tr>
		<tr>
			<td>isWriteable</td>
			<td>Tests if the group specified: 'owner', 'group', or 'world' has write access<  'owner', 'group', or 'world'/td>
			<td>$group</td>
		</tr>
		<tr>
			<td>isExecutable</td>
			<td>Get write access to true or false for the group specified:  'owner', 'group', or 'world'</td>
			<td>$group</td>
		</tr>
        <tr>
			<td>setReadable</td>
			<td>Tests if the group specified: 'owner', 'group', or 'world' has read access</td>
			<td>$group</td>
		</tr>
		<tr>
			<td>setWriteable</td>
			<td>Tests if the group specified: 'owner', 'group', or 'world' has write access</td>
			<td>$group</td>
		</tr>
		<tr>
			<td>setExecutable</td>
			<td>Returns</td>
			<td>Set execute access to true or false for the group specified:</td>
		</tr>
		<tr>
			<td>getCreateDate</td>
			<td>Retrieves Create Date for directory or file identified in the path</td>
			<td></td>
		</tr>
		<tr>
			<td>getAccessDate</td>
			<td>Retrieves Last Access Date for directory or file identified in the path</td>
			<td></td>
		</tr>
		<tr>
			<td>getUpdateDate</td>
			<td>Retrieves Last Update Date for directory or file identified in the path</td>
			<td></td>
		</tr>
		<tr>
			<td>setAccessDate</td>
			<td>Sets the Last Access Date for directory or file identified in the path</td>
			<td></td>
		</tr>
		<tr>
			<td>setUpdateDate</td>
			<td>Sets the Last Update Date for directory or file identified in the path</td>
			<td></td>
		</tr>
	</tbody>
</table>



##### getMetadata #####

Look up metadata about this entry.

* *url* - URL referring to a local file in this filesystem
* *successCallback* MetadataCallback - returns the Metadata object
* *errorCallback* Throws an Exception
* *return type* void



* *parent* DirectoryEntry The directory to which to move the entry.
* *newName* integer The new name of the entry. Defaults to the Entry's current name if unspecified.
* *successCallback* FileSystemCallback A callback that is called with the Entry for the new object.
* *errorCallback* Throws an Exception
* *return type* void



#### 2.4. Directory Interface ####

This interface represents a directory on a filesystem.

    Namespace: Molajo\Filesystem\Directory

#### Constants

Constants are not defined for this interface.

#### Attributes

1. *directory_reader* type *DirectoryReader*

#### Methods

The **Entries Interface** has XXX methods: *copyTo*, *getMetadata*, *moveTo*, *remove*, and *toURL*.

##### createReader() #####

Creates an instance of the DirectoryReader

##### getDirectory #####

Creates or looks up a directory.
* *path* void
* *options* Flags (create, exclusive booleans)
    * If create and exclusive are both true and the path already exists, getDirectory must fail.
    * If create is true, the path doesn't exist, and no other error occurs, getDirectory must create and return a corresponding DirectoryEntry.
    * If create is not true and the path doesn't exist, getDirectory must fail.
    * If create is not true and the path exists, but is a file, getDirectory must fail.
    * Otherwise, if no other error occurs, getDirectory must return a DirectoryEntry corresponding to path.
* *successCallback* EntryCallback
* *errorCallback* ErrorCallback
* *return type* void

##### getFile #####

Creates or looks up a file.

* *path* string Either an absolute path or a relative path from this DirectoryEntry to the file to be looked up or created. It is an error to attempt to create a file whose immediate parent does not yet exist.
* *options* Flags (create, exclusive booleans)
    * If create and exclusive are both true, and the path already exists, getFile must fail.
    * If create is true, the path doesn't exist, and no other error occurs, getFile must create it as a zero-length file and return a corresponding FileEntry.
    * If create is not true and the path doesn't exist, getFile must fail.
    * If create is not true and the path exists, but is a directory, getFile must fail.
    * Otherwise, if no other error occurs, getFile must return a FileEntry corresponding to path.

* *successCallback* EntryCallback
* *errorCallback* ErrorCallback
* *return type* void

##### removeRecursively #####

Deletes a directory and all of its contents, if any. In the event of an error [e.g. trying to delete a directory that contains a file that cannot be removed], some of the contents of the directory may be deleted. It is an error to attempt to delete the root directory of a filesystem.

* *successCallback* void
* *errorCallback* Throws an Exception
* *return type* void

#### list

 This interface lets a user list files and directories in a directory. If there are no additions to or deletions from a directory between the first and last call to readEntries, and no errors occur, then:

    * A series of calls to readEntries must return each entry in the directory exactly once.
    * Once all entries have been returned, the next call to readEntries must produce an empty array.
    * If not all entries have been returned, the array produced by readEntries must not be empty.
    * The entries produced by readEntries must not include the directory itself ["."] or its parent [".."].


##### readEntries #####

Read the next block of entries from this directory.

* *successCallback* EntryCallback
* *errorCallback* ErrorCallback
* *return type* void

##### toURL #####

 Returns a URL that can be used to identify this entry. ex. filesystem:http://example.domain/persistent-or-temporary/path/to/file.html

 * *successCallback* void
 * *errorCallback* Throws an Exception
 * *return type* string







### 2.5 File Interface

This interface represents a filesystem.

    Namespace: Molajo\Filesystem\File

#### 2.5.1 Constants

Constants are not defined for this interface.

#### 2.5.2 Attributes

1. Name
2. Value
3. created_datetime - *date* - This is the time at which the file or directory was created.
4. last_accessed_datetime - *date* - This is the time at which the file or directory was last accessed.
5. last_modified_datetime - *date* - This is the time at which the file or directory was last modified.
6. size - *int* - The size of the file, in bytes. This must return 0 for directories.


#### 2.4 Create, Update and Delete Rules

Business rules that must be satisfied and tested for file and directory operations.

##### 2.4.1 Copy #####

It is an error to try to:

* copy a directory inside itself or to any child at any depth;
* copy an entry into its parent if a name different from its current one isn't provided;
* copy a file to a path occupied by a directory;
* copy a directory to a path occupied by a file;
* copy any element to a path occupied by a directory which is not empty.

A copy of a file on top of an existing file must attempt to delete and replace that file.

A copy of a directory on top of an existing empty directory must attempt to delete and replace that directory.

Directory copies are always recursive--that is, they copy all contents of the directory.

##### 2.4.2 Move #####

Move an entry to a different location on the filesystem. It is an error to try to:

* move a directory inside itself or to any child at any depth;
* move an entry into its parent if a name different from its current one isn't provided;
* move a file to a path occupied by a directory;
* move a directory to a path occupied by a file;
* move any element to a path occupied by a directory which is not empty.

A move of a file on top of an existing file must attempt to delete and replace that file.

A move of a directory on top of an existing empty directory must attempt to delete and replace that directory.

##### 2.4.3 Delete #####

Deletes a file or directory. It is an error to attempt to delete a directory that is not empty. It is an error to attempt to delete the root directory of a filesystem.

* *successCallback* void
* *errorCallback* Throws an Exception
* *return type* void

#### 2.5.3 Methods

Constants are not defined for this interface.



### 3 Implementation

### 3.1 Classes

- The `Molajo\FilesystemAbstract` class allows you implement the `**Filesystem Interface**` very easily
    by extending it and implementing the generic `log` method.

- Similarly, using the `Molajo\FilesystemLoggerTrait` only requires you to
  implement the generic `log` method. Note that since traits can not implement
  interfaces, in this case you still have to `implement **Filesystem Interface**`.

- The `Molajo\FilesystemNullLogger` is provided together with the interface. It MAY be
  used by users of the interface to provide a fall-back "black hole"
  implementation if no logger is given to them. However conditional logging
  may be a better approach if context data creation is expensive.


### 3.2 Adapters

Benefit to the application is a common API for file services, regardless of the type of adapter.
Supports data consistency, standard processes and events, leads to better assurance of higher quality results,

#### 3.2.1 Basic
If desired, you could simply use this.

##### 3.2.1.1 Local Filesystem

##### 3.2.2.1 Temporary Registry

#### 3.2.2 Advanced

##### 3.2.2.1 Protocol-specific
- HTTP
- cURL
- FTP Server

##### 3.2.3.1 Segmenting Fileservices for Application

- by person, business area, office, and so on
- Media
- Temporary Space
- Reuse between implementations
- HTTP, cURL,
- Streams
- separate core files from site files

### 3.3 Basic Usage

### 3.3.1 Overview

Molajo uses a Facade Pattern approach to providing access to application services. The connection
to the Services Class is supported by a static instance within the Frontcontroller to the Services
Class. The only function of the static instance is to create a useful entry point back into
the Frontcontroller, and then into the Services class.

An example of how this creates a more productive development environment follows as the difference
between the first example where it takes one command to read a file translates to in reality as
a new instance to a resource is created, and then the filesystem, and then to the file class, injecting
the class with the options needed to secure access and make the request.

It is worth pointing out that the connection to the data are not static. Only the connection between
the classes is maintained as a global entry point.

 Read a file on a local server

#### 3.3.1.1 Application code:

::

$file = Services::Filesystem()->read('local', 'x/y/z/file.txt');

#### 3.3.1.2 What the Filesystem Service does, in response:

Uses a Facade pattern to mask the complexity with the Filesystem Adapter Structure.

::

use Molajo\Filesystem\Adapter\Local as LocalAdapter;
$adapter = new LocalAdapter($options);

use Molajo\Filesystem\Filesystem;
$filesystem = new Filesystem($adapter);

use Molajo\Filesystem\File;
$filesystem = new Filesystem($fileservice, $options);


results = $filesystem->read('x/y/z/file.txt');



### 4 Filesystem API

This is how developers interact with the Filesystem.





#### 4.1 Filesystem File Services API


#### 4.1.1 File Metadata

::

$file = Services::Filesystem()->read('local', 'x/y/z/file.txt');

#### 4.1.2 File Interaction







#### 4.2 Filesystem Directory Services API




#### 4.2.1 Directory Metadata

::

$file = Services::Filesystem()->read('local', 'x/y/z/file.txt');

#### 4.2.2 Directory Interaction





#### 4.3 Directory Services API

::

$file = Services::Filesystem()->read('local', 'x/y/z/file.txt');



#### 5 Filesystem Adapters


#### 5.1 Overview


#### 5.2 Core Adapters


#### 5.3 Create an Adapter


#### 5.6 Install an Adapter



6. Unit Testing
----------


7. Package
----------

The interfaces and classes described as well as relevant exception classes
and a test suite to verify your implementation are provided as part of the
[Molajo/Filesystem](https://packagist.org/packages/Molajo/Filesystem) package.


8. Resources
-------------
[W3C File API: Directories and System] W3C Working Draft 17 April 2012 → http://www.w3.org/TR/file-system-api/

