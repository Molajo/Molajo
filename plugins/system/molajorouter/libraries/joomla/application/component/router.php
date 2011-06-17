<?php
class JComponentRouter
{	
	/**
	 * Array of buildrules
	 * 
	 * @var array
	 */
	protected $buildrules = array();
	
	/**
	 * Array of parserules
	 * 
	 * @var array
	 */
	protected $parserules = array();
		
	protected $name;
	
	protected $views = array();
	
	protected $lookup;
	
	function __construct()
	{
		$app = JFactory::getApplication();
		$this->attachBuildRule(array($this, 'findItemid'));
		$app->triggerEvent('onComponentRouterRules', array($this));
	}
	
	function register($name, $view, $id = false, $parent = false, $parent_id = false, $nestable = false, $layouts = false)
	{
		$viewobj = new stdClass();
		$viewobj->name = $view;
		$viewobj->title = $name;
		$viewobj->id = $id;
		if($parent) {
			$viewobj->parent = $this->views[$parent];
			$this->views[$parent]->children[] = &$viewobj;
			$viewobj->path = $this->views[$parent]->path;
		} else {
			$viewobj->parent = false;
			$viewobj->path = array();
		}
		$viewobj->path[] = $name;
		$viewobj->parent_id = $parent_id;
		if($parent_id) {
			$this->views[$parent]->child_id = $parent_id;
		}
		$viewobj->child_id = false;
		$viewobj->nestable = $nestable;
		$viewobj->layouts = $layouts;
		
		$this->views[$view] = $viewobj;
	}
	
	function getViews()
	{
		return $this->views;
	}
	
	function getPath($query)
	{
		$views = $this->getViews();
		$result = array();
		$id = false;
		if(isset($query['view']) && isset($views[$query['view']])) {
			$path = array_reverse($views[$query['view']]->path);
			$start = true;
			foreach($path as $element) {
				$view = $views[$element];
				$id = $view->child_id;
				if($start) {
					$id = $view->id;
					$start = false;
				}
				if($id) {
					if(isset($query[$id])) {
						$result[$view->name] = array($query[$id]);
						if($view->nestable) {
							$nestable = call_user_func_array(array($this, 'get'.ucfirst($view->name)), array($query[$id]));
							if($nestable) {
								$result[$view->name] = array_reverse($nestable->getPath());
							}			
						}
					} else {
						return $result;
					}
				} else {
					$result[$view->name] = true;
				}
			}
		}
		return $result;
	}
	
	function setRules($rules)
	{
		foreach($rules['build'] as $rule) {
			$this->attachBuildRule($rule);
		}
		foreach($rules['parse'] as $rule) {
			$this->attachParseRule($rule);
		}
	}
	
	/**
	 * Attach a build rule
	 *
	 * @param	callback	The function to be called.
	 * @param	position	The position where this 
	 * 						function is supposed to be executed.
	 * 						Valid values: 'first', 'last'
	 */
	public function attachBuildRule($callback, $position = 'last')
	{
		if($position == 'last')	{
			$this->buildrules[] = $callback;
		} elseif ($position == 'first') {
			array_unshift($this->buildrules, $callback);
		}
	}

	/**
	 * Attach a parse rule
	 *
	 * @param	callback	The function to be called.
	 * @param	position	The position where this 
	 * 						function is supposed to be executed.
	 * 						Valid values: 'first', 'last'
	 */
	public function attachParseRule($callback, $position = 'last')
	{
		if($position == 'last')	{
			$this->parserules[] = $callback;
		} elseif ($position == 'first') {
			array_unshift($this->parserules, $callback);
		}
	}
	
	function build($query)
	{
		$segments = array();
		// Process the parsed variables based on custom defined rules
		foreach($this->buildrules as $rule) {
			call_user_func_array($rule, array(&$this, &$query, &$segments));
		}
		return $segments;
	}
	
	function parse($segments)
	{
		$vars = array();
		// Process the parsed variables based on custom defined rules
		foreach($this->parserules as $rule) {
			call_user_func_array($rule, array(&$this, &$segments, &$vars));
		}
		return $vars;
	}
	
	function getName()
	{
		if (empty($this->name)) {
			$r = null;
			if (!preg_match('/(.*)Router/i', get_class($this), $r)) {
				JError::raiseError (500, 'JLIB_APPLICATION_ERROR_ROUTER_GET_NAME');
			}
			$this->name = strtolower($r[1]);
		}

		return $this->name;
	}
	
	function getCategory($id)
	{
		$category = JCategories::getInstance($this->getName())->get($id);
		return $category;
	}
	
	public function findItemid($crouter, $query, $segments)
	{
		if(isset($query['Itemid'])) {
			return $query;
		}
		
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if ($this->lookup === null)
		{
			$this->lookup = array();

			$component	= JComponentHelper::getComponent('com_'.$this->getName(true));
			$items		= $menus->getItems('component_id', $component->id);
			$views		= $this->getViews();
			foreach ($items as $item)
			{
				if (isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset($this->lookup[$view])) {
						$this->lookup[$view] = array();
					}
					if ($views[$view]->id && isset($item->query[$views[$view]->id])) {
						$this->lookup[$view][$item->query[$views[$view]->id]] = $item->id;
					} else {
						$this->lookup[$view] = $item->id;
					}
				}
			}
		}

		$needles = $this->getPath($query);

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset($this->lookup[$view]))
				{
					if(is_bool($ids)) {
						$query['Itemid'] = $this->lookup[$view];
						return;
					}
					foreach($ids as $id)
					{
						if (isset($this->lookup[$view][(int)$id])) {
							$query['Itemid'] = $this->lookup[$view][(int)$id];
							return;
						}
					}
				}
			}
		} else {
			$active = $menus->getActive();
			if ($active && $active->component == $this->getName(true)) {
				$query['Itemid'] = $active->id;
				return;
			}
		}
		return null;
	}
}