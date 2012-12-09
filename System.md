/**
Initial System Configuration for a Molajo Distribution

Distribution: Each catalog_type entry is defined as a model in the core files. The initial
 parameters and parameter values are defined in XML and within the parameter data element.
 All data is treated the same, regardless of whether it is system data, like data about
 sites, applications, or users, or data about content, like articles and comments, or
 categorization data like categories and tags.

1. Planning:

    Actions
    Roles
    Groups
	Permissions
    View Access
    How best to handle "own"

Actions

Applied to any and all data within the system. Actions are defined within the #__actions Table.

Signin
Create
Read
Update
Publish
Delete
Administer

Roles

The distribution provides a generic set of roles that provide for basic site development and management.
With the exception of the Administrator, all roles can be modfied or completely removed.

Administrator - has ultimate control over every aspect of the system. It is intentional that the permissions
for this user cannot be reduced. The ID should be used with that in mind.

Developer - permission to create extensions and configure the environment. Has both site and administrator access.

Manager - site administration responsibility, role provides ability to create users and groups and manage
data, but does not have permission to create extensions or modify configurations. Access to site and
administrator applications.

Publisher - oversight of content creation, publishing and management with full control over all site content
but no ability to manage users, groups, permissions, or create and configure extensions.

Editors - ability to update any content on the site but not publish content.

Authors - ability to create content and to edit their own content until it has been published. Cannot
publish or remove content.

The above roles have signin permissions to the site and administrator applications.

Member - registered with the system, has ability to signin and manage a personal profile.

Guest - visitor not logged on. Can be used to customize a membership website, as needed.

Public - any visitor, regardless of whether or not they are logged on. All site content defaults to View
access for public. Can respond to content using comments, ratings, and notifications.

Groups

Roles are implemented using groups.

Each user has a special private group that can be used to assign permissions.

With the exception of guest and the user private groups, all child permissions are automatically
applied to parent groups.

The group structure delivered in the distribution:

Administrator
-- Developer
-- -- Manager
-- -- -- Publisher
-- -- -- -- Editor
-- -- -- -- -- Author

Public
-- Registered
-- Guest

Administrator, Registered, Guest and Public are system defined groups that cannot be removed.

The Registered, Guest and Public groups cannot be specifically assigned (or removed) from users.

Categories

Used to group data that can then be treated one time with policy. It should be said that categories are
not required in that permissions can be assigned to extensions, and all content for that extension, and
permissions can also be assigned on an item by item basis. Categories are used in the distribution
to ensure all content is covered in a way (hopefully) needed by standard deployments.

As with Groups, permissions associated with parent categories inherit the permissions of child objects.

The distribution uses this set of initial categories:

Site Category
-- Application Category
-- -- Extension Category
-- -- -- Content Category
-- -- -- -- Use and Categorization Category
-- -- -- -- Primary Data Category
-- -- -- -- Response Element Category

Permissions

Permissions are the assignment of action capabilities (ex., view, create, delete, etc.) to groups
for specific content. Specific users obtain permission through group membership.

1. Permissions are specifically assigned, not revoked.

2. Parent groups inherit permissions assigned to child groups.

3. Removing permissions from parent groups also removes permissions from child groups.

4. Category and item permissions aggregate into parent categories.

5. Removal of permissions associated with a Category also removes permissions from child categories.

6. Disassociating content from a category is equivalent to removing category permissions.

7. Users have the sum of all permissions assigned to groups to which they belong.

Default permissions implemented for specified categories and groups in this distribution:

Site Category - Administrator Group
-- Application Category - Administrator Group
-- -- Extension Category - Developer Group
-- -- -- Content Category - Manager Group
-- -- -- -- User and Categorization Category  - Publisher Group
-- -- -- -- Primary Data Category - Publisher, Editor and Author Groups
-- -- -- -- Response Element Category - Registered Group

signin to Application 2 - Administrator - Author and above
signin to Application 1 - Registered

View Access Groups

Standard Groups are combined into entities called 'View Access Groups' which are associated with View
access Permissions. These special system groups are not directly managed by the user but rather created
behind the scenes for each unique combination of groups used when assigning View permissions. These
groups are for better performance in that a single value can be joined by the database during
execution of the display queries.

For this distribution, the initial 'View Access Groups' are:

System (Includes only Administrator Group 9)
.. Special (Author, and above, Groups)
.. .. Registered (Registered, and above, Groups)
Guest
Public

On default, the Public Group has Permission to View all Site Application Content (with the exception of User
Profile data).

