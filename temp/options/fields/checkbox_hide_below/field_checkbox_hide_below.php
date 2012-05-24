<?php
class NHP_Options_checkbox_hide_below extends NHP_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since NHP_Options 1.0.1
	*/
	function __construct($field = array(), $value ='', $parent){
		
		parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;
		//$this->render();
		
	}//function
	
	
	
	/**
	 * Field Render Function.
	 *
	 * Takes the vars and outputs the HTML for the field in the settings
	 *
	 * @since NHP_Options 1.0.1
	*/
	function render(){
		
		$class = (isset($this->row['class']))?$this->row['class']:'';
		
		echo ($this->row['desc'] != '')?' <label for="'.$this->row['id'].'">':'';
		
		echo '<input type="checkbox" id="'.$this->row['id'].'" name="'.$this->args['opt_name'].'['.$this->row['id'].']" value="1" class="'.$class.' nhp-opts-checkbox-hide-below" '.checked($this->value, '1', false).' />';
		
		echo (isset($this->row['desc']) && !empty($this->row['desc']))?' '.$this->row['desc'].'</label>':'';
		
	}//function
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since NHP_Options 1.0.1
	*/
	function enqueue(){
		
		wp_enqueue_script(
			'nhp-opts-checkbox-hide-below-js', 
			NHP_OPTIONS_URL.'fields/checkbox_hide_below/field_checkbox_hide_below.js', 
			array('jquery'),
			time(),
			true
		);
		
	}//function
	
}//class
?>