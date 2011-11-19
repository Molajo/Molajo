# Sample Molajo Template #

This is an explanation of the various elements defined within a Molajo Templates and instructions for use.

## Molajo Configuration ##

### Install a Template: ###

1. Login to the Administrator as the system admin.
2. Navigate to the Extend-Install menu item.
3. Install using the ZIP or URL format, or use the Create option to start with this sample.

### Configure the Template ###

1. In the Administrator, navigate to the Build-Template menu item.
2. Select the Template and update it to be the *default* template.

### Specifying a Page Layout ###

A template page can be used to generate different types of layouts. For example, if the site requires a certain layout for the home page than the blog or the article view, a template page is useful.

There are many locations where the page can be selected. After setting the default template, as described in the previous post, you can set a default page value for the application. That value will be used if no other configuration options are made.

The first page configuration found in the this order is used for the page layout:

1. If a URL Request Parameter of `page=value` is found, `value` is used as page. This is useful for testing layouts.
2. The page configuration value for a detail item which is defined in the editor for that content in the parameter section.
3. The menu item page configuration (only for a *Component Menu Item Type*) will be selected next.
4. The page configuration for a category associated with the content (primary category has precedence over secondary categories).
5. Components each have a configuration section where page can be specified. If a value has not been found above, the component page will be used.
6. Application Configuration
7. The default folder within the page folder, or the only folder there.
8. The template/index.php file would be used without a page.

## Template Folders and Files ##

### 1. template.xml ###

XML file defining template metadata, files and folders and parameters for the installer.

### 2. index.php ###

Initial file retrieved and executed by Molajo during the rendering process.

File containing html and **doc statements** that define content requirements in the form of *messages*, *modules*, and *components*, placement of rendered output, and additional attributes required for rendering.

The following line in the index.php file retrieves the page parameter and uses it to include the required layout file.

    <?php include dirname(__FILE__).'/'.MolajoFactory::getDocument()->page; ?>

### 3. css folder ###

Files within this subfolder which have the .css file extension are automatically loaded when this page is used. Any files that begin with `rtl_` are also automatically loaded if the current language warrants.

### 4. js folder ###

As is the case with CSS files, files with .js file extensions will be loaded by Molajo.

### 5. language folder ###



### 6. page folder ###

The page parameter is a Molajo configuration option (explained above) which is used to specify a page layout treatment for the content processed. This technique can be used to manage different layouts, like a blog, contact, and home page, within the same Template.

Page options available to the application are derived from the set of subfolders contained within the page folder.

#### Required page layouts ####

The following are special purpose page formats that should be available in each template:

* **default** page layout is only selected when a special layout is needed, otherwise, Molajo will use the default page layout.
* **error** used if Molajo is unable to process the request due to a problem like a 404-page not found or a 500-component not found condition.
* **logon** necessary for templates used by applications requiring logon, like the administrator or an intranet.
* **offline** during site development or maintainance, an application can be configured to appear offline and not allow visitors without appropriate access to enter. This page layout is used to inform visitors of the situation.
* **print** page layout that renders component output without displaying page modules.

#### Other page layouts ####

In addition to those listed in the previous section, additional page layouts can be provided, as needed. When a subfolder exists in the page folder, the page layout option is available for selection within Molajo. See previous **Molajo Configuration** section for more information.

#### Page layout folders and files ####

A Molajo page layout is comprised of the following files and folders. (Additional files and folders can be added, as needed.)

* **css** Files within this subfolder which have the .css file extension are automatically loaded when this page is used. Any files that begin with `rtl_` are also automatically loaded if the current language warrants.
* **js** As is the case with CSS files, files with .js file extensions will be loaded by Molajo.
* **index.php** File containing html and **doc statements** that define content requirements in the form of *messages*, *modules*, and *components*, placement of rendered output, and additional attributes required for rendering.

## More on the index.php file ##

Molajo templates use a mix of HTML, php, and special **doc Statements** to instruct the application what content is desired and to correctly locate rendered output.

### What <?php and the `defined('MOLAJO') or die;`? ###

    <?php
    /**
     * @package     Molajo
     * @subpackage  Template
     * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
     * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
     */
    defined('MOLAJO') or die;
    ?>

Towards the top of the index.php file, there is a `<?php` value signifying the start of PHP statements. The PHP statements end when the ?> is encountered. A semicolon is required to end any PHP statement. Template and layout files use a combination of simple PHP statements, doc statements, described below, and regular HTML.

The `defined('MOLAJO') or die;` line is for security to prevent direct entry to the file. All php files (other than the application entry points) must have this line.

Within the index.php file are a combination of normal html statements and doc statements. Use HTML to arrange the layout of the page. The doc statements, described in the next section, instruct Molajo as to the specific extension output to be rendered in that location.

    <div class="container">
        <doc:include type="modules" name="header" wrap="header" />
        <doc:include type="message" />
        <section>
            <doc:include type="modules" name="menu" wrap="none" />
            <doc:include type="component" />
        </section>
        <doc:include type="modules" name="footer" wrap="footer" />
    </div>

### doc: include type="head" ###

    <doc:include type="head" />

Included once within the <head> element to render style, script and meta elements.

### doc: include type="message" ###

    <doc:include type="message" />

Renders messages. The default css for system messages is in media\css\system.css and can be overridden by defining the statements in the template css.

### doc: include type="component" ###

    <doc:include type="component" />

In Molajo, a component is the primary output for the page. Rendered output for the component is inserted at the location of the component doc statement.

### doc: include type="modules" ###

    <doc:include type="modules" name="sidebar" wrap="div" />

Renders modules identified by the `position` identified in the name attribute. Note: depending on the specific module, the criteria might prohibit the module from rendering on this specific page. For example. the module might select only that content which shares a common tag. If there are no such matches, the module will not display even if it has the position specified.

#### Where are positions specified? ####

In the module configuration, the position is identified.

### doc: include type="module" ###

    <doc:include type="module" name="mainmenu" title="Main Menu" wrap="none" color="blue" />

Renders a single module, rather than all modules defined for a specific position, as is true with the previous doc statement.

The specific instance of the module is identified by both the module name and the title.

Additional attributes can be specified and are used to override the existing parameter values defined for the module.

### wrap attribute ###

The *wrap* attribute identifies the enclosing treatment for rendered output, what it should be wrapped in.

Both normal wraps, like div, table, none, and outline are available, along with HTML5 options, like article, aside, hav, and section, to name a few.

To determine what wrap options are available, review the list of subfolders defined in the extensions/layouts/wraps folder.

#### Override or create new Wraps ####

Wraps can be created or overridden. See the extensions/layouts/README for more information on how to do so.

## Sharing Templates ##

A properly created template can be shared by zipping the template folder and then installing as a Molajo extension.


**This README file on the root of the website lists all README files available.**




