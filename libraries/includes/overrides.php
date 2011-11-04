<?php
/**
 * @package     Molajo
 * @subpackage  Overrides
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

abstract class JFactory extends MolajoFactory {}

class JAccess extends MolajoACL {}

class JApplicationHelper extends MolajoApplicationHelper {}
class JMenu extends MolajoMenu {}
class JPathway extends MolajoPathway {}
class JRouter extends MolajoRouter {}
class JSession extends MolajoSession {}
abstract class JSessionStorage extends MolajoSessionStorage {}

abstract class JModuleHelper extends MolajoModuleHelper {}
class JComponentHelper extends MolajoComponentHelper {}

class JTableAsset extends MolajoTableAsset {}
class JTableCategory extends MolajoTableCategory {}
class JTableExtension extends MolajoTableExtension {}
class JTableLanguage extends MolajoTableLanguage {}
class JTableMenu extends MolajoTableMenuitem {}
class JTableMenuType extends MolajoTableMenu {}
class JTableModule extends MolajoTableModule {}
class JTableSession extends MolajoTableSession {}
abstract class JTable extends MolajoTable {}
class JTableNested extends MolajoTableNested {}
class JTableUpdate extends MolajoTableUpdate {}
class JTableUser extends MolajoTableUser {}
class JTableUsergroup extends MolajoTableGroup {}

class JDocument extends MolajoDocument {}
//class JDocumentError extends MolajoDocumentError {}
//class JDocumentFeed extends MolajoDocumentFeed {}
class JDocumentRenderer extends MolajoDocumentRenderer {}
class JDocumentHTML extends MolajoDocumentHTML {}

//class JDocumentRendererComponent extends MolajoDocumentRendererComponent {}
//class JDocumentRendererHead extends MolajoDocumentRendererHead {}
//class JDocumentRendererMessage extends MolajoDocumentRendererMessage {}
//class JDocumentRendererModule extends MolajoDocumentRendererModule {}
//class JDocumentRendererModules extends MolajoDocumentRendererModules {}
//class JDocumentJSON extends MolajoDocumentJSON {}
//class JDocumentOpensearch extends MolajoDocumentOpensearch {}
//class JDocumentRAW extends MolajoDocumentRAW {}
//class JDocumentXML extends MolajoDocumentXML {}
class JForm extends MolajoForm {}
abstract class JFormField extends MolajoFormField {}
class JFormRule extends MolajoFormRule {}
class JFormHelper extends MolajoFormHelper {}

//abstract class MolajoToolbarHelper extends MolajoRenderToolbarHelper {}

//class JHelp extends MolajoHelp {}
class JLanguageHelper extends MolajoLanguageHelper {}
abstract class JMailHelper extends MolajoMailHelper {}

abstract class JPluginHelper extends MolajoPluginHelper {}
abstract class JPlugin extends MolajoPlugin {}

class JAuthentication extends MolajoAuthentication {}
class JAuthenticationResponse extends MolajoAuthenticationResponse {}
abstract class JUserHelper extends MolajoUserHelper {}
class JUser extends MolajoUser {}

class JRoute extends MolajoRoute {}
class JUtility extends MolajoUtility {}
/**
JFTP -
https://github.com/AmyStephen/joomla-platform/commit/0f5dfc4c6f68fcffde62a2f44217a8934644c5a0
*/

class JExtension extends MolajoExtension {}
abstract class JInstallerHelper extends MolajoInstallerHelper {}
class JInstaller extends MolajoInstaller {}
//class JAdapterInstance extends MolajoAdapterInstance {}

//class JInstallerComponent extends MolajoInstallerComponent {}
//class JInstallerFile extends MolajoInstallerFile {}
//class JInstallerLanguage extends MolajoInstallerLanguage {}
//class JInstallerLibrary extends MolajoInstallerLibrary {}
//class JInstallerModule extends MolajoInstallerModule {}
//class JInstallerPackage extends MolajoInstallerPackage {}
//class JInstallerPlugin extends MolajoInstallerPlugin {}
//class JInstallerTemplate extends MolajoInstallerTemplate {}

class JLibraryManifest extends MolajoLibraryManifest {}
class JPackageManifest extends MolajoPackageManifest {}

class JUpdate extends MolajoUpdate {}
class JUpdateAdapter extends MolajoUpdateAdapter {}
class JUpdater extends MolajoUpdater {}
//class JUpdaterCollection extends MolajoUpdaterCollection {}
//class JUpdaterExtension extends MolajoUpdaterExtension {}



/** legacy support */
jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.modelform');
jimport('joomla.application.component.modelitem');
jimport('joomla.application.component.modellist');
 
