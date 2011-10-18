<?php
/**
 * @package     Molajo
 * @subpackage  Overrides
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

class JAccess extends MolajoACL {}

class JApplication extends MolajoApplication {}
class ApplicationException extends MolajoException {}
class JCategories extends MolajoCategories {}
class JApplicationHelper extends MolajoApplicationHelper {}
class JMenu extends MolajoMenu {}
class JPathway extends MolajoPathway {}
class JRouter extends MolajoRouter {}
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
//class JDocumentRendererAtom extends MolajoDocumentRendererAtom {}
//class JDocumentRendererRSS extends MolajoDocumentRendererRSS {}
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

class JRegistry extends MolajoRegistry {}
class JAuthentication {
	public function __construct()
	{
		JError::raiseError('500', JText::_('MOLAJO_AUTHENTICIAN_IN_MOLAJOCONTROLLERLOGIN'));
	}
	public static function getInstance() {}
	public function authenticate($credentials, $options) {}
}
class JAuthenticationResponse extends MolajoAuthentication {}
abstract class JUserHelper extends MolajoUserHelper {}
class JUser extends MolajoUser {}

class JRoute extends MolajoRoute {}
class JText extends MolajoText {}

/**
JFTP -
https://github.com/AmyStephen/joomla-platform/commit/0f5dfc4c6f68fcffde62a2f44217a8934644c5a0

*/