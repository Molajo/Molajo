<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo;

use Molajo\Service\Services;
use Molajo\MVC\Controller\DisplayController;

defined('MOLAJO') or die;

/**
 * Includer
 *
 * @package     Molajo
 * @subpackage  Extension
 * @since       1.0
 */
class Includer
{
    /**
     * $name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * $type
     * Examples: head, message, tag, request, resource, defer
     *
     * @var    string
     * @since  1.0
     */
    protected $type = null;

    /**
     * $tag
     *
     * @var    string
     * @since  1.0
     */
    protected $tag = null;

    /**
     * $attributes
     *
     * Extracted in Parser Class from Theme/Rendered output
     *
     * <include:extension statement attr1=x attr2=y attrN="and-so-on" />
     * template, template_view_css_id, template_view_css_class
     * wrap, wrap_view_css_id, wrap_view_css_class
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * @param string $name
     * @param string $type
     *
     * @return null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->name = $name;
        $this->type = $type;

        Services::Registry()->createRegistry('Include');

        Services::Registry()->set('Parameters', 'includer_name', $this->name);
        Services::Registry()->set('Parameters', 'includer_type', $this->type);
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Criteria*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Enable*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Request*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Theme*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Page*');
        Services::Registry()->copy('RouteParameters', 'Parameters', 'Ui*');

        return;
    }

    /**
     * process
     *
     * - Loads Metadata (only Theme Includer)
     * - Loads Language files for Extension
     * - Loads Assets for Extension
     * - Activates Controller for Task
     * - Returns Rendered Output to Parse for <include:type /> replacement
     *
     * @param   $attributes <include:type attr1=x attr2=y attr3=z ... />
     *
     * @return mixed
     * @since   1.0
     */
    public function process($attributes = array())
    {
        /** attributes from <include:type */
        $this->attributes = $attributes;
		$exists = Services::Registry()->exists('Tempattributes');
		if ($exists === true) {
			Services::Registry()->deleteRegistry('Tempattributes');
		}
		Services::Registry()->createRegistry('Tempattributes');

		$this->getAttributes();

		/** retrieve the extension that will be used to generate the MVC request */
        $this->getExtension();

        /** initialises and populates the MVC request */
        $results = $this->setRenderCriteria();
        if ($results === false) {
            return false;
        }

        $this->loadPlugins();

        $rendered_output = $this->invokeMVC();

        /** only load media if there was rendered output */
        if ($rendered_output == ''
            && Services::Registry()->get('Parameters', 'criteria_display_view_on_no_results') == 0
        ) {
        } else {
            $this->loadMedia();
            $this->loadViewMedia();
        }

        return $rendered_output;
    }

    /**
     * getAttributes
     *
     * Use the view and/or wrap criteria ife specified on the <include statement
     *
     * @return null
     * @since   1.0
     */
    protected function getAttributes()
    {
        if (count($this->attributes) > 0) {
        } else {
            return;
        }

        //todo filter input appropriately
        //todo case statements
        foreach ($this->attributes as $name => $value) {

            /** Name used by includer for extension */
            if ($name == 'name' || $name == 'title') {

                if ($this->name == 'template') {

                    if ((int) $value > 0) {
                        $template_id = (int) $value;
                        $template_title = Helpers::Extension()->getExtensionNode($template_id);
                    } else {
                        $template_title = ucfirst(strtolower(trim($value)));
                        $template_id = Helpers::Extension()
                            ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
                    }

                    Services::Registry()->set('Parameters', 'template_view_id', $template_id);
                    Services::Registry()->set('Parameters', 'template_view_path_node', $template_title);
                    Services::Registry()->set('Parameters', 'extension_title', $template_title);
                    Services::Registry()->set('Parameters', 'template_view_title', $template_title);

                } else {
                    $value = ucfirst(strtolower(trim($value)));
                    Services::Registry()->set('Parameters', 'extension_title', $value);
                }

            /** Used to extract a list of extensions for inclusion */
            } elseif ($name == 'tag') {
                $this->tag = $value;

            /** Template */
            } elseif ($name == 'template' || $name == 'template_view_title'
                || $name == 'template_view' || $name == 'template_view') {
                $value = ucfirst(strtolower(trim($value)));

                $template_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $value);

                if ((int) $template_id == 0) {
                } else {
                    Services::Registry()->set('Parameters', 'template_view_id', $template_id);
                    Services::Registry()->set('Parameters', 'template_view_path_node', $value);
                    Services::Registry()->set('Parameters', 'extension_title', $value);
                    Services::Registry()->set('Parameters', 'template_view_title', $value);
                }

            } elseif ($name == 'template_view_css_id' || $name == 'template_css_id' || $name == 'template_id' || $name == 'id') {
                Services::Registry()->set('Parameters', 'template_view_css_id', $value);

            } elseif ($name == 'template_view_css_class' || $name == 'template_css_class' || $name == 'template_class'
                || $name == 'class') {
                Services::Registry()->set('Parameters', 'template_view_css_class', str_replace(',', ' ', $value));

                /** Wrap */
            } elseif ($name == 'wrap' || $name == 'wrap_view_title' || $name == 'wrap_view' || $name == 'wrap_title') {

                $value = ucfirst(strtolower(trim($value)));
                $wrap_id = Helpers::Extension()
                    ->getInstanceID(CATALOG_TYPE_WRAP_VIEW, $value);

                if ((int) $wrap_id == 0) {
                } else {
                    Services::Registry()->set('Parameters', 'wrap_view_path_node', $value);
                    Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_id);
                }

            } elseif ($name == 'wrap_view_css_id' || $name == 'wrap_css_id' || $name == 'wrap_id') {
                Services::Registry()->set('Parameters', 'wrap_view_css_id', $value);

            } elseif ($name == 'wrap_view_css_class' || $name == 'wrap_css_class' || $name == 'wrap_class') {
                Services::Registry()->set('Parameters', 'wrap_view_css_class', str_replace(',', ' ', $value));

            } elseif ($name == 'wrap_view_role' || $name == 'wrap_role' || $name == 'role') {
                Services::Registry()->set('Parameters', 'wrap_view_role', str_replace(',', ' ', $value));

            } elseif ($name == 'wrap_view_property' || $name == 'wrap_property' || $name == 'property') {
                Services::Registry()->set('Parameters', 'wrap_view_property', str_replace(',', ' ', $value));

                /** Model */
            } elseif ($name == 'datalist') {
                Services::Registry()->set('Parameters', 'datalist', $value);
                Services::Registry()->set('Parameters', 'model_type', 'dbo');
                Services::Registry()->set('Parameters', 'model_name', 'Parameters');
                Services::Registry()->set('Parameters', 'model_query_object', 'getParameters');

            } elseif ($name == 'model_name') {
                Services::Registry()->set('Parameters', 'model_name', $value);

            } elseif ($name == 'model_type') {
                Services::Registry()->set('Parameters', 'model_type', $value);

            } elseif ($name == 'model_parameter' || $name == 'parameter' || $name == 'value') {
                Services::Registry()->set('Parameters', 'model_parameter', $value);
                Services::Registry()->set('Parameters', 'model_type', 'dbo');
                Services::Registry()->set('Parameters', 'model_name', 'Plugindata');
                Services::Registry()->set('Parameters', 'model_query_object', 'getPlugindata');

            } elseif ($name == 'model_parameter_np' || $name == 'parameter_np') {
                Services::Registry()->set('Parameters', 'model_parameter', $value);
                Services::Registry()->set('Parameters', 'model_type', 'dbo');
                Services::Registry()->set('Parameters', 'model_name', 'Plugindatanoplugins');
                Services::Registry()->set('Parameters', 'model_query_object', 'getPlugindata');

            } elseif ($name == 'model_query_object' || $name == 'query_object') {
                Services::Registry()->set('Parameters', 'model_query_object', $value);

            } else {
                /** Todo: For security reasons: match field to table registry and filter first */
				Services::Registry()->set('Tempattributes', $name, $value);
            }
        }
    }

    /**
     * getExtension
     *
     * Retrieve extension information after looking up the ID in the extension-specific includer
     *
     * @return bool
     * @since 1.0
     */
    protected function getExtension()
    {
        return;
    }

    /**
     * setRenderCriteria
     *
     * Uses Request and attributes (overrides) defined on the <include statement
     * to retrieve Template and Wrap information
     *
     * @return bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        /** Save Template Overrides */
		$template_id = 0;
		$template_title = '';

		$saveTemplate = array();
		$temp = Services::Registry()->get('Parameters', 'template*');
		if (is_array($temp) && count($temp) > 0) {
			foreach ($temp as $key => $value) {

				if ($key == 'template_view_id' || $key == 'template_view_path_node' || $key == 'template_view_title') {

				} elseif (is_array($value)) {
					$saveTemplate[$key] = $value;

				} elseif ($value === 0 || trim($value) == '' || $value === null) {

				} else {
					$saveTemplate[$key] = $value;
				}
			}
		}

		/** Save Wrap Overrides */
		$saveWrap = array();
		$temp = Services::Registry()->get('Parameters', 'wrap*');
		$temp2 = Services::Registry()->get('Parameters', 'model*');
		$temp3 = array_merge($temp, $temp2);
		$temp2 = Services::Registry()->get('Parameters', 'data*');
		$temp = array_merge($temp2, $temp3);

		if (is_array($temp) && count($temp) > 0) {
			foreach ($temp as $key => $value) {

				if (is_array($value)) {
					$saveWrap[$key] = $value;

				} elseif ($value === 0 || trim($value) == '' || $value === null) {

				} else {
					$saveWrap[$key] = $value;
				}
			}
		}

		if ($this->type == 'wrap') {
		} else {
			$results = $this->setTemplateRenderCriteria($saveTemplate);
			if ($results === false) {
				return false;
			}
		}

		$results = $this->setWrapRenderCriteria($saveWrap);
		if ($results === false) {
			return false;
		}

	    Services::Registry()->delete('Parameters', 'item*');
        Services::Registry()->delete('Parameters', 'list*');
        Services::Registry()->delete('Parameters', 'form*');
        Services::Registry()->delete('Parameters', 'menuitem*');

        Services::Registry()->sort('Parameters');

        /** Copy some configuration data */
        $fields = Services::Registry()->get('Configuration', 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('Parameters', $key, $value);
            }
        }

		$fields = Services::Registry()->getArray('Tempattributes');
		if (count($fields) === 0 || $fields === false) {
		} else {
			foreach ($fields as $key => $value) {
				Services::Registry()->set('Parameters', $key, $value);
			}
		}

        return true;
    }

	/**
	 * Process Template Options
	 *
	 * @param $saveTemplate
	 * @return bool
	 */
	protected function setTemplateRenderCriteria($saveTemplate)
	{
		/** Process Template */
		$template_id = (int) Services::Registry()->get('Parameters', 'template_view_id');

		if ((int) $template_id == 0) {
			$template_title = Services::Registry()->get('Parameters', 'template_view_path_node');
			if (trim($template_title) == '') {
			} else {
				$template_id = Helpers::Extension()
					->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
				Services::Registry()->set('Parameters', 'template_view_id', $template_id);
			}
		}

		if ((int) $template_id == 0) {
			$template_id = Helpers::View()->getDefault('Template');
			Services::Registry()->set('Parameters', 'template_view_id', $template_id);
		}

		if ((int) $template_id == 0) {
			return false;
		}

		Helpers::View()->get($template_id, 'Template');

		if (is_array($saveTemplate) && count($saveTemplate) > 0) {
			foreach ($saveTemplate as $key => $value) {
				Services::Registry()->set('Parameters', $key, $value);
			}
		}

		return true;
	}

	/**
	 * Process Wrap Options
	 *
	 * @param $saveWrap
	 * @return bool
	 */
	protected function setWrapRenderCriteria($saveWrap)
	{
		/** Process Wrap - Replace Overrides (If Template overlaid them) */
		if (is_array($saveWrap) && count($saveWrap) > 0) {
			foreach ($saveWrap as $key => $value) {
				if (is_array($value)) {
					$saveWrap[$key] = $value;

				} elseif ($value === 0 || trim($value) == '' || $value === null) {

				} else {
					Services::Registry()->set('Parameters', $key, $value);
				}
			}
		}

		$wrap_id = 0;
		$wrap_title = '';

		$wrap_id = (int) Services::Registry()->get('Parameters', 'wrap_view_id');

		if ((int) $wrap_id == 0) {
			$wrap_title = Services::Registry()->get('Parameters', 'wrap_view_path_node', '');
			if (trim($wrap_title) == '') {
				$wrap_title = 'None';
			}
			$wrap_id = Helpers::Extension()
				->getInstanceID(CATALOG_TYPE_WRAP_VIEW, $wrap_title);
			Services::Registry()->set('Parameters', 'wrap_view_id', $wrap_id);
		}

		/** Save New Wrap Values from Template Read - and Overrides */
		if (is_array($saveWrap) && count($saveWrap) > 0) {
			foreach ($saveWrap as $key => $value) {
				if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
				} else {
					Services::Registry()->set('Parameters', $key, $value);
				}
			}
		}

		$saveWrap = array();
		$temp = Services::Registry()->get('Parameters', 'wrap*');
		$temp2 = Services::Registry()->get('Parameters', 'model*');
		$temp3 = array_merge($temp, $temp2);
		$temp2 = Services::Registry()->get('Parameters', 'data*');
		$temp = array_merge($temp2, $temp3);

		if (is_array($temp) && count($temp) > 0) {
			foreach ($temp as $key => $value) {

				if (is_array($value)) {
					$saveWrap[$key] = $value;

				} elseif ($value === 0 || trim($value) == '' || $value === null) {

				} else {
					$saveWrap[$key] = $value;
				}
			}
		}

		Helpers::View()->get($wrap_id, 'Wrap');

		if (is_array($saveWrap) && count($saveWrap) > 0) {
			foreach ($saveWrap as $key => $value) {
				if ($key == 'wrap_view_id' || $key == 'wrap_view_path_node' || $key == 'wrap_view_title') {
				} else {
					Services::Registry()->set('Parameters', $key, $value);
				}
			}
		}

		if (Services::Registry()->exists('Parameters', 'wrap_view_role')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_role', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_property')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_property', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_header_level')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_header_level', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_show_title')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_show_title', '');
		}
		if (Services::Registry()->exists('Parameters', 'wrap_view_show_subtitle')) {
		} else {
			Services::Registry()->set('Parameters', 'wrap_view_show_subtitle', '');
		}

		return true;
	}

    /**
     * 	loadPlugins overrides (or initially loads) Plugins from the Template and/or Wrap View folders
     *
     *  @return  void
     *  @since   1.0
     */
    protected function loadPlugins()
    {
        $templatePlugins = Services::Filesystem()->folderFolders(
            Services::Registry()->get('Parameters', 'template_view_path') . '/' . 'Plugin'
        );

        if (count($templatePlugins) == 0 || $templatePlugins === false) {
        } else {
            $this->processPlugins(
                $templatePlugins,
                Services::Registry()->get('Parameters', 'template_view_namespace')
            );
        }

        $wrapPlugins = Services::Filesystem()->folderFolders(
            Services::Registry()->get('Parameters', 'wrap_view_path') . '/' . 'Plugin'
        );

        if (count($wrapPlugins) == 0 || $wrapPlugins === false) {
        } else {
            $this->processPlugins(
                $wrapPlugins,
                Services::Registry()->get('Parameters', 'wrap_view_namespace')
            );
        }

        return;
    }

    /**
     * processPlugins for Theme, Page, and Request Extension (overrides Core and Plugin folder)
     *
     * @param  $plugins array of folder names
     * @param  $path
     *
     * @return void
     * @since  1.0
     */
    protected function processPlugins($plugins, $path)
    {
        foreach ($plugins as $folder) {
            Services::Event()->process_events(
                $folder . 'Plugin',
                $path . '\\Plugin\\' . $folder . '\\' . $folder . 'Plugin'
            );
        }
    }

    /**
     * loadMedia
     *
     * Loads Media CSS and JS files for extension and related content
     *
     * @return null
     * @since   1.0
     */
    protected function loadMedia()
    {
        return $this;
    }

    /**
     * loadViewMedia
     *
     * Loads Media CSS and JS files for Template and Wrap Views
     *
     * @return null
     * @since   1.0
     */
    protected function loadViewMedia()
    {
        $priority = Services::Registry()->get('Parameters', 'criteria_media_priority_other_extension', 400);

        $file_path = Services::Registry()->get('Parameters', 'template_view_path');
        $url_path = Services::Registry()->get('Parameters', 'template_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        $file_path = Services::Registry()->get('Parameters', 'wrap_view_path');
        $url_path = Services::Registry()->get('Parameters', 'wrap_view_path_url');

        $css = Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        $js = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        $defer = Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return $this;
    }

    /**
     * invokeMVC
     *
     * Instantiate the Controller and fire off the action, returns rendered output
     *
     * @return mixed
     */
    protected function invokeMVC()
    {
        Services::Registry()->sort('Parameters');

        $message = 'Includer->invokeMVC ' . 'Name ' . $this->name . ' Type: ' . $this->type . ' Template: ' . Services::Registry()->get('Parameters', 'template_view_title');
        $message .= ' Parameters:<br />';
        ob_start();
        $message .= Services::Registry()->get('Parameters', '*');
        $message .= ob_get_contents();
        ob_end_clean();

//		if (strtolower( Services::Registry()->get('Parameters', 'template_view_title')) == 'toolbar') {
//			echo $message;
//		}

        Services::Profiler()->set($message, LOG_OUTPUT_RENDERING, VERBOSE);

        $controller = new DisplayController();
        $controller->set('id', (int) Services::Registry()->get('Parameters', 'source_id'));

        /** Set Parameters */
        $parms = Services::Registry()->getArray('Parameters');
        $cached_output = Services::Cache()->get('Template', implode('', $parms));

        if ($cached_output === false) {
            if (count($parms) > 0) {
                foreach ($parms as $key => $value) {
                    $controller->set($key, $value);
                }
            }

            $results = $controller->execute();
            Services::Cache()->set('Template', implode('', $parms), $results);
        } else {
            $results = $cached_output;
        }

		//if (strtolower( Services::Registry()->get('Parameters', 'template_view_title')) == 'toolbar') {
//		echo $results;
		//	die;
		//}

        return $results;
    }
}
