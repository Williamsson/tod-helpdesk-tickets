<?php 


class todHelpdeskPublic{
	
	public function __construct(){
		add_shortcode( 'tod-helpdesk', array(&$this, 'todHelpdesk_shortcode' ) );
	}
	
	public function todHelpdesk_shortcode($atts, $content = ""){
		$return = "";
		if(isset($_POST['todHelpdeskSubmit'])){
			$return .= $this->todHelpdesk_handlePostData();
		}
		$return .= $this->todHelpdesk_frontendPage();
		
		return $return;
	}
	
	public function todHelpdesk_frontendPage(){
		$form = "<form action='' method='POST'>";
			$form .= "<label for='title'>Ticket title:</label><br/>";
				$form .= "<input required type='text' name='title'>";
			$form .= "<label for='category'>Category:</label><br/>";
				$form .= "<select name='category'>";
				$categories = $this->todHelpdesk_getCategories();
				foreach($categories as $category){
					$form .= "<option value='$category->id'>$category->title</option>";
				}
			$form .= "</select><br/>";
			$form .= "<label for='content'>Describe the issue:</label><br/>";
				$form .= "<textarea required name='content'></textarea><br/><br/>";
			
			$form .= "<input type='submit' name='todHelpdeskSubmit' value='Send'>";
		$form .= "</form>";
		return $form;
	}
	
	private function todHelpdesk_getCategories(){
		global $wpdb;
		$table = $wpdb->prefix . 'tod_ticket_categories';
		$data = $wpdb->get_results("SELECT * FROM $table");
		return $data;
	}
	
	private function todHelpdesk_handlePostData(){
		
	}
	
}