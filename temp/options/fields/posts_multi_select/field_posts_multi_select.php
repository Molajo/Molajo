<?php
class NHP_Options_posts_multi_select extends NHP_Options{	
	
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
		
		$class = (isset($this->row['class']))?'class="'.$this->row['class'].'" ':'';
		
		echo '<select id="'.$this->row['id'].'" name="'.$this->args['opt_name'].'['.$this->row['id'].'][]" '.$class.'multiple="multiple" >';

		$args = wp_parse_args($this->row['args'], array('numberposts' => '-1'));
		
		$posts = get_posts($args); 
		foreach ( $posts as $post ) {
			$selected = (is_array($this->value) && in_array($post->ID, $this->value))?' selected="selected"':'';
			echo '<option value="'.$post->ID.'"'.$selected.'>'.$post->post_title.'</option>';
		}

		echo '</select>';

		echo (isset($this->row['desc']) && !empty($this->row['desc']))?'<br/><span class="description">'.$this->row['desc'].'</span>':'';
		
	}//function
	
}//class
?>