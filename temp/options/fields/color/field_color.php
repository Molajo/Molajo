<?php
class NHP_Options_color extends NHP_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since NHP_Options 1.0
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
	 * @since NHP_Options 1.0
	*/
	function render(){
		
		$class = (isset($this->row['class']))?$this->row['class']:'';
		
		echo '<div class="farb-popup-wrapper">';
		
		echo '<input type="text" id="'.$this->row['id'].'" name="'.$this->args['opt_name'].'['.$this->row['id'].']" value="'.$this->value.'" class="'.$class.' popup-colorpicker" style="width:70px;"/>';
		echo '<div class="farb-popup"><div class="farb-popup-inside"><div id="'.$this->row['id'].'picker" class="color-picker"></div></div></div>';
		
		echo (isset($this->row['desc']) && !empty($this->row['desc']))?' <span class="description">'.$this->row['desc'].'</span>':'';
		
		echo '</div>';
		
	}//function
	
	
	/**
	 * Enqueue Function.
	 *
	 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
	 *
	 * @since NHP_Options 1.0
	*/
	function enqueue(){
		
		wp_enqueue_script(
			'nhp-opts-field-color-js', 
			NHP_OPTIONS_URL.'fields/color/field_color.js', 
			array('jquery', 'farbtastic'),
			time(),
			true
		);
		
	}//function
	
}//class
?>