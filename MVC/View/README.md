# WORKING WITH MOLAJO ViewS #

Please check the README file on the root for the location of other README files.

---

## SECTION I. Introduction ##

### What is a View? ###

A View is a set of files used to format and display data.

Views can be shared between extensions.

There are several different types of Views, called 'View Types'.

### What is a View Type? ###

View Types are designed to handle specific types of rendered output:

* Page - Included within a Theme and used to define the layout and content of the page using <include:xzy statements
* Template - Renders the output of each individual section of the page. For example, one Template renders a blog post, while a second Template could be used to render a set of related articles links.
* Wrap - Views which take the rendered output from a Template and 'wrap it' in HTML, like <div or <article tags, so that it can be styled using CSS.

### What is a Controller? ###

A Controller controls the logic, instructing the Model and View to do specific actions.

### What is a Model? ###

A Model retrieves data, as instructed by the Controller.

### What are query_results? ###

Query_results are collections of data, retrieved by a Model at the instruction of the Controller.

The Controller passes each row of data from query_results into the View.

The View formats and displays data from the row, one at a time, until each row has been processed.

### What is a row? ###

A row is single set of columns populated with data that describe a specific item. To display the column names and content for a View, use this command within the body.php file of the View:

    <?php
    echo '<pre>';
    var_dump($this->row);
    echo '</pre>';
    ?>

### What is a column? ###

A column is a single piece of information about an item. To display the contents of a column within a View, use this syntax with the column name desired:

    <?php echo $this->row->title; ?>

---

## SECTION II. What is a Molajo View? ##

### What is a Molajo View? ###

A Molajo View is a collection of files used to render output.

The same View can be used in different ways. For example, a View that renders a list of links can be used to display latest posts or the names of group members.

There are three View Types: Page, Template, and Wrap.

### What is the structure of a View? ###

A Molajo View uses this folder and file structure:

    View-type
        ...View-name
        ... ... Css
        ... ... ... Files with CSS extension are automatically loaded
        ... ... ... Files that begin with rtl_ are loaded for RTL Languages.
        ... ... Images
        ... ... ... Images used by the View can be stored here.
        ... ... Js
        ... ... ... defer
        ... ... ... ... Files with JS extension are automatically loaded
        ... ... ... ... Files contained within the defer folder are loaded at the bottom of the page
        ... ... Language
        ... ... ... en-GB.ini
        ... ... ... ... Each language installed on the site has a file named using the language tag value.
        ... ... Views (View Type: Page)
        ... ... ... Index.php
        ... ... ... ... The Page View is a single file named Index.php included by the Theme to initiate rendering.
        ... ... Views (For View Types: Templates and Wraps)
        ... ... ... Header.php
        ... ... ... Body.php
        ... ... ... Footer.php
        ... ... ... ... Molajo renders the Header.php file one time before processing the query_results.
        ... ... ... ... The Body.php file is processed once for each row within the query_results.
        ... ... ... ... Molajo renders the Footer.php file after all rows have been rendered using the Body.php file
        ... ... Configuration.xml
        ... ... ... A system file which includes author and license information and defines processing requirements for Molajo.
---

### Where are Views located? ###

Views can be stored in the following locations within a Molajo website, as defined below in selectivity order:

1. *Theme* If available, Views contained within the *Extension/Themes/Theme-name/View-type/View-name* folder for the current Theme are selected first.

2. *Menu Item* Views stored in the Extension/Menuitem/Menuitem-type folder override are selected second.

3. *Resource Extension* Views stored in the Extension/Resource/Resource-name folder are selected after Theme and Menu Item.

4. *View Extension* If the View has not been overridden by the Theme, Menu Item, or Resource Extension, Molajo selects it from the Extension/View/View-Type/View-Name location.

## SECTION III. Working with Molajo Themes, Page Views and Include Statements ##

Priorities


"asset_priority_site":"100",
"asset_priority_application":"200",
"asset_priority_user":"300",
"asset_priority_extension":"400",
"asset_priority_request":"500",
"asset_priority_primary_category":"600",
"asset_priority_menuitem":"700",
"asset_priority_item":"800",
"asset_priority_theme":"900",



## SECTION IV. Using Molajo Views with Extensions ##

Resources and Menu Items define which Views are used to render output.

For each Resource, a Template and Wrap are needed to render a List, Item, and Form.

Molajo looks in these locations, in this order, selecting the first Template and/or Wrap it finds:

1. *Resource Item* (ex., an Article or a User or an Image). Template and Wrap Views can be defined specifically for each specific resource within Molajo.

2. *Primary Category* - if a Primary Category has be assigned to specific Resources or the entire Resource Extension, default Template and Wrap Views can be defined and then used for associated Items, Lists, and Forms.

3. *Menu Items* - Menu items can be created to display custom selections of data and used to define the Template and Wrap for rendering the Menu Item output.

4. *Resource Extension* (ex. Articles, or Media, or Users). The next level of specificity is the Resource Extension, itself. When no value is provided for a Primary Category or Resource Item, and when the output is not rendered from a Menu Item (or the Menu Item did not define the Template and Wrap), Molajo will use Template and Wrap assignments for Items, Lists, and Forms for the Resource Extension.

5. *Application* - when there are no Template or Wrap definitions at any of the lower levels just described, Molajo will use the Application default selections for Item, List, and Form. In some cases, that is all that is needed. A good example of this is the Administrator Interface which uses the same Template and View definitions for all Resources, resulting in a consistent user experience. In situations where a resource requires a slightly different interface, a Template and/or Wrap can be defined at a lower level to override the default Application options.

---

## SECTION V. Adding Views ##

With Molajo it is nearly as easy to create an Extension as it is to create a new spreadsheet or document. You are encouraged to first try to create an Extension before looking at shared Extensions. Very little code is used in an Extension and it has a low risk of change during upgrades or of security issues. The configuration options are adequate for most to build innovate sites without adding non-core software.

### How can I create a Molajo View? ###

To install a Molajo View, navigate to the Molajo Administrator-Install-Create location.

Select the type of Extension and press Create.

Molajo can create a default Extension of each type that can then be easily customized, as needed.

### How can I install a Molajo View? ###

To install a Molajo View, navigate to the Molajo Administrator-Install location.

Extensions available for install are listed in the Install facility and can be quickly added by double-clicking the name of the Extension.


### How can I share my View with other Molajo users? ###

To share your custom Extension, simply Zip the Extension folder. In that form, it is installable in other Molajo installations.

At this time, only those Extensions listed in the Molajito Github Repository are available for installation in the Administrator Interface. To have your Extension considered for sharing in this manner, post in the Molajo Github Issues queue or in the Molajo Users List. Only free of charge Extensions dual licensed using the MIT, will be considered. A key factor in deciding whether or not to share an Extension is if it satisfies an unmet need and has been developed in a safe manner withing the guidelines of the API. Extensions which duplicate functionality of existing work will not be considered, but rather developers will be encouraged to collaborate on the Molajito Repository on combining work into one better solution.

---

## SECTION VI. How do I override a View? ##

### What sequence does Molajo use to search for Views? ###

Molajo searches for Views in this order, selecting the first it finds:

* Extension/Themes/Theme-name/View-type/View-name

* Extension/Menuitems/Menuitem-type/View-type/View-name

* Extension/Resources/Resource-name/View-type/View-name

* Extension/View/View-type/View-name

### How do I override a View? ###

Simply place a View with the same name in a location with a higher search priority.


---

## SECTION VII. Working with CSS, JS, and other Media ##

### How do I add CSS for a View?

Place the CSS files into the View-name/Css folder. Files with a css extension are automatically loaded when the View is used to render the page.

### How do I provide for right-to-left Languages? ###

Prefix files with *rtl_* to indicate files which should only be loaded for when serving a right-to-left Language.

### How do I add JS to a View? ###

Place the JS files into the View-name/Js folder. Those files which should be loaded at the bottom of the page should be placed into the Js/Defer/ folder. All files with a JS extension are automatically loaded when the View is used to render the Page.

### How can I include images? ###

Add images to the View-name/images folder and use <img src="../images/name.jpg" /> within the View files.

### What about Language support? ###

Place language files within the project in the View/View-type/View-name/Language folder, named using the language tag value.

* en-GB.ini

### How does Molajo load CSS and JSS files? ###

There are two factors:

1. Which files are loaded?

2. In what order (priority) are the files loaded?

#### Which files are loaded? ####

1. All files stored within the View Css and Js folder are loaded when that View is selected as part of rendering the page.

CSS and JS files located within the View-name/Css and Js folders are loaded for the View. To override the CSS and JS for a View, one must override the entire View. See topics above for how to override a View and the sequence used to select the View used.

      => /View/View-type/View-name/css[js]/XYZ.css[js]

2. All files stored within the Theme Css and Js folder are loaded when that Theme is used.

      => /Extension/Theme/Theme-name/css[js]/XYZ.css[js]

For the vast majority of situations, the options above are .

Where extreme control is desired, media can be stored in the Site Media folders and will be used according to these specifications:

#### Site-specific and Multi-site ####

Molajo's folder structure is organized for optional multi-site support. Beneath the Site folder, there is a numbered folder(s) and a Media folder. The numbered folders represent each Site that shares a Molajo implementation. The Media folder is shared with all Sites. Within the numbered folders are Media folders specific to that site.

#### CSS and JS file locations ####

To always load, store files at:

      => Media/css[js]/XYZ.css[js]

To always load for a specific Application, store files at:

      => Media/[application-name]/css[js]/XYZ.css[js]

To load files for a specific Resource Extension, Menu Item, or Resource Item (ex. a specific Article), store files at:

      => Media/[catalog-id]/css[js]/XYZ.css[js]

      => Media/[application-name]/[catalog-id]/css[js]/XYZ.css[js]

### What priority is assigned various files? ###

"asset_priority_site":"100",
"asset_priority_application":"200",
"asset_priority_user":"300",
"asset_priority_extension":"400",
"asset_priority_request":"500",
"asset_priority_primary_category":"600",
"asset_priority_menuitem":"700",
"asset_priority_item":"800",
"asset_priority_theme":"900",


### Does Molajo combine and minify CSS and JSS files? ###



---

## SECTION VIII. USING QUERY RESULTS IN VIEWS ##

### How are the View files processed by Molajo? ###

There are two options for using query results:

1. Custom.php - For advanced users who wish to manage sequence, loop and security escaping independent of Molajo.

2. Body.php (optionally Header.php and Footer.php) - To allow Molajo to loop through the query_results, and ensure all fields are properly handled for security concerns.

#### Option 1: Custom.php View (Advanced users) ####

When Molajo finds a Custom.php file, it pushes the $this->query_results object into the file.

This option should only be used by Advanced users because:

- Molajo will inject the Custom.php file with the $this->query_results object.
- The developer must build in loop processing to cycle through the object.
- The developer is responsible to implement security measures and escape the output appropriately.

#### Option 2: Molajo manages loop and security processing. ####

To let Molajo to manage query and security processing, do not include a Custom.php file, but rather use these files:

 1. *Header.php* Molajo includes once before processing any item data, passing in the $this->row object.
 2. *Body.php* Molajo injects the Body.php file with the $this-row object once for each item in the query results. All columns are pre-processed for security and ready to display.
 3. *Footer.php* Molajo includes this file one time after all items have been processed, passing in the last $this->row object.

---

# SECTION IX. DATA AND COMMANDS AVAILABLE WITHIN VIEWS #

## What data is available within the View file? ##

### Using Services ###

Molajo has many Services available to the Application, many of which can be utilized within Views. Review the Service folder's README for more information.

To use a Service within the View, the following namespace information must be added to the top of each View file calling a Service.

<?php
use Molajo\Service\Services;
?>

### Registry ###

The Services Registry is used to store and retrieve application data ranging from system information, such as configuration, logging, and database options, to data necessary for Theme and View rendering, like the names and locations of Views used and Media that are being processed.

To display a very long list of the parameters used to select and process the Query and Views, use this command:

#### To display all named pairs within a specific Namespace ####

    <?php echo Molajo::Services()->get('parameters', '*');  ?>

It is a good idea to become familiar with what options are available as this knowledge will help you tap into the flexibility and strength Molajo can offer.

#### To display a single parameter key and value ####

To display just one parameter, follow this example which displays the CSS Class for the current Item Template View:

    <?php echo Services::Registry()->get('parameters', 'item_template_view_css_class'); ?>

#### To display a list of parameters that relate to a specific topic ####

Namespacing is used to help separate parameters in order to make it easier to work with such a complex dataset. In addition, the Registry has been developed to display all entries that follow a specified value, as shown in this example:

    <?php echo Services::Registry()->get('parameters', 'item_*'); ?>

This will return a wealth of information about the Item Theme, Page, Template, Wrap, and Model, including the primary key for these entities and the names. In addition, the location from which the View was used is defined to assist with override questions. Also, the model name, type and query object describe the input criteria used to construct and execute the query that is currently used to render this View. It will be worthwhile to explore use of the Registry to understand and better control Molajo processing.

#### To find all namespaces available ####

 <?php echo Molajo::Services()->get('*');  ?>

 Once you know what namespaces are available, you can dump all contents of the namespace, as described above.

 Be curious and explore various data available. To list a full set of all current namespaces, use this command.

 This data is worth exploring and using in your Views. You can even create your own namespace and store data at that location. Namespace contents can be sorted, deleted, partially deleted, and so on. See the README contained within the Service/Services/Registry folder for more information.


### User ###

To display all data on the current user:

    <?php echo Services::User()->get('*'); ?>

To display the User's Full name:

    <?php echo Services::User()->full_name; ?>


### Query Results ###

All rows (can only be displayed in Custom.php)

    <?php

    echo '<pre>';
        var_dump($this->query_results);
    echo '</pre>';

    ?>

Single Row

    <?php

    echo '<pre>';
        var_dump($this->row);
    echo '</pre>';

    ?>

Specific Column

    <?php echo echo $this->row->title; ?>


---

# SECTION X. WORKING WITH DATES #

1. What options are available for formatting dates in Views?

Dates can be formatted one of two ways, using PHP date or the JHtml class.

2. How can PHP's Date function be used in a View?

http://php.net/manual/en/function.date.php

Example:
        $today = date("F j, Y, g:i a"); // March 10, 2001, 5:16 pm

3. How can Joomla's JHtml function be used in a View to format dates?

        $date	= JHtml::_('date', $this->row->created,      Services::Language()->translate('DATE_FORMAT_LC1'));
        $time	= JHtml::_('date', $this->row->checked_out_time, 'H:i');

Options:
        DATE_FORMAT_LC="l, d F Y"
        DATE_FORMAT_LC1="l, d F Y"
        DATE_FORMAT_LC2="l, d F Y H:i"
        DATE_FORMAT_LC3="d F Y"
        DATE_FORMAT_LC4="Y-m-d"
        DATE_FORMAT_JS1="y-m-d"

4. How can I change the date formats for Molajo?

Date formats can be changed by overriding the language file.


# SECTION XII. WORKING WITH IMAGES #

1. System Configuration

Locations:

Sizes:

 * 0 - original size
 * 1 - xsmall; configuration option, defaults to 50 x 50
 * 2 - small; configuration option, defaults to 75 x 75
 * 3 - medium; configuration option, defaults to 150 x 150
 * 4 - large; configuration option, defaults to 300 x 300
 * 5 - xlarge; configuration option, defaults to 500 x 500

Types:

2. How can I resize an image within a View?

Add this to your View or within your content.

        {image name="dog.png" size=1 type=2}

---

# SECTION XIII. USING OTHER THEME SYSTEMS #

1. What does Molajo use as a default View environment?

The MolajoView class handles View processing. Normal PHP are used in core View files.

2. Using Mustache for PHP with Molajo.

3. Using Twig with Molajo

4. Using Ajax with Molajo

5. Using Mustache JS with Molajo

6. Using <insert name of your favorite Theme System here> to render Molajo Output?

