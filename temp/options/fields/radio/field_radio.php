<?php
class NHP_Options_radio extends NHP_Options{	
	
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
		
		$class = (isset($this->row['class']))?'class="'.$this->row['class'].'" ':'';
		
		echo '<fieldset>';
			
			foreach($this->row['options'] as $k => $v){
				
				//echo '<option value="'.$k.'" '.selected($this->value, $k, false).'>'.$v.'</option>';
				echo '<label for="'.$this->row['id'].'_'.array_search($k,array_keys($this->row['options'])).'">';
				echo '<input type="radio" id="'.$this->row['id'].'_'.array_search($k,array_keys($this->row['options'])).'" name="'.$this->args['opt_name'].'['.$this->row['id'].']" '.$class.' value="'.$k.'" '.checked($this->value, $k, false).'/>';
				echo ' <span>'.$v.'</span>';
				echo '</label><br/>';
				
			}//foreach

		echo (isset($this->row['desc']) && !empty($this->row['desc']))?'<span class="description">'.$this->row['desc'].'</span>':'';
		
		echo '</fieldset>';
		
	}//function
	
}//class
?>