<?php 

class todHelpdeskDashboard{
	
	public function __construct(){
		add_action('admin_menu', array(&$this, 'todHelpdesk_addAdminMenuItem'));
		add_action( 'admin_enqueue_scripts', array(&$this,'loadAdminScripts'));
	}
	
	public function loadAdminScripts($hook_suffix){
		wp_enqueue_script( "dataTables", "//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js" );
		wp_enqueue_style('datatablesCSS', "//cdn.datatables.net/1.10.5/css/jquery.dataTables.min.css");
		wp_enqueue_script( "tod-helpdesk-scripts", "/wp-content/plugins/tod-helpdesk-tickets/js/adminScript.js" );
		wp_enqueue_style('tod-helpdesk-css', "/wp-content/plugins/tod-helpdesk-tickets/css/adminStyle.css");
	}
	
	public function todHelpdesk_addAdminMenuItem(){
		add_menu_page('ToD Helpdesk Tickets', 'ToD Helpdesk Tickets', 'manage_options', 'tod-helpdesk-tickets',
				array(&$this, 'todHelpdesk_adminPageTicketsContent'), "dashicons-index-card");
		add_submenu_page('tod-helpdesk-tickets', 'ToD Helpdesk Settings 1', 'Settings', 'manage_options', 
				'tod-helpdesk-settings', array(&$this, 'todHelpdesk_adminPageSettingsContent'));
	
	}
	
	public function todHelpdesk_adminPageTicketsContent(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$tickets = $this->getAllTickets();
		
		$page = '<div id="todHelpdeskAdminTicketsWrapper">';
			$page .= '<h1>ToD Helpdesk Tickets</h1><br/>';
			$page .= '<table id="ticketsTable">';
				$page .= '<thead>';
					$page .= '<tr role="row">';
						$page .= '<th>Title</th>';
						$page .= '<th>Created by</th>';
						$page .= '<th>Created at</th>';
						$page .= '<th>Updated at</th>';
						$page .= '<th>Category</th>';
						$page .= '<th>Agent responsible</th>';
						$page .= '<th>State</th>';
					$page .= '</tr>';
				$page .= '</thead>';
				$page .= '<tbody>';
					foreach($tickets as $ticket){
						$page .= "<tr>";
							$page .= "<td>" . $ticket->ticketTitle . "</td>";
							$page .= "<td>" . $ticket->author . "</td>";
							$page .= "<td>" . $ticket->created . "</td>";
							$page .= "<td>" . $ticket->updated . "</td>";
							$page .= "<td>" . $ticket->category . "</td>";
							$page .= "<td>" . $ticket->agent . "</td>";
							$page .= "<td>" . $ticket->state . "</td>";
						$page .= "</tr>";
					}
				$page .= '</tbody>';
			$page .= '</table>';
		$page .= '</div>';
		echo $page;
		
	}
	
	public function todHelpdesk_adminPageSettingsContent(){
		
	}
	
	private function getAllTickets(){
		global $wpdb;
		$prefix = $wpdb->prefix;
		$tickets = $wpdb->get_results("SELECT t.t_id, t.created, t.updated, t.title AS ticketTitle,
												wp.display_name AS author, wp2.display_name AS agent, 
												ts.title AS state, tcat.title AS category
												FROM " . $prefix . "tod_tickets AS t
												LEFT JOIN " . $prefix . "users AS wp
												ON wp.ID = t.author_id
												LEFT JOIN " . $prefix . "users AS wp2
												ON wp2.ID = t.agent_id
												LEFT JOIN " . $prefix . "tod_ticket_states AS ts
												ON ts.id = t.state
												LEFT JOIN " . $prefix . "tod_ticket_categories AS tcat
												ON tcat.id = t.category");
		return $tickets;
	}
	
}