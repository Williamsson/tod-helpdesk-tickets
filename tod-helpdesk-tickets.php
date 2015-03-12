<?php
/**
 * Plugin Name: ToD Helpdesk Tickets
 * Description: A ticketing system initially created for the ToD website.
 * Version: 1.0
 * Author: Simon Williamsson
 * License: GPLv2
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class todHelpdeskTickets {
	
	public function __construct(){
		if(is_admin()){
			require_once("dashboard.class.php");
			$dashboard = new todHelpdeskDashboard();
		}else{
			require_once("public.class.php");
			$public = new toDHelpdeskPublic();
		}
	}

	static function todHelpdesk_install(){
		global $wpdb, $todHelpdeskDbVersion;
		$todHelpdeskDbVersion = "1.0";
		$charset_collate = $wpdb->get_charset_collate();
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$table = $wpdb->prefix . "tod_tickets";
		$query = "CREATE TABLE IF NOT EXISTS $table (
				  `t_id` int(11) NOT NULL AUTO_INCREMENT,
				  `author_id` int(11) NOT NULL,
				  `agent_id` int(11) DEFAULT NULL,
				  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `updated` timestamp DEFAULT NULL,
				  `title` varchar(65) COLLATE utf8_bin NOT NULL,
				  `content` text COLLATE utf8_bin NOT NULL,
				  `state` int(11) NOT NULL DEFAULT '1',
				  `category` int(11) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`t_id`)
				) $charset_collate AUTO_INCREMENT=1;";
		dbDelta($query);
		
		$table = $wpdb->prefix . "tod_ticket_states";
		$query = "CREATE TABLE IF NOT EXISTS $table (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `title` varchar(65) COLLATE utf8_bin NOT NULL,
					  PRIMARY KEY (`id`)
					) $charset_collate AUTO_INCREMENT=1 ;";
		dbDelta($query);

		$count = $wpdb->get_var("SELECT COUNT('id')
				FROM $table WHERE 'id' IS NOT NULL");
		if($count == 0){
			$wpdb->query("INSERT INTO $table
			            (title)
			            VALUES
			            ('Pending'),
			            ('Open'),
			            ('Closed'),
			            ('Completed')"
					);
		}
		
		$table = $wpdb->prefix . "tod_ticket_categories";
		$query = "CREATE TABLE IF NOT EXISTS $table (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`title` varchar(65) COLLATE utf8_bin NOT NULL,
					PRIMARY KEY (`id`)
					) $charset_collate AUTO_INCREMENT=1 ;";
		dbDelta($query);
		
		$count = $wpdb->get_var("SELECT COUNT('id')
				FROM $table WHERE 'id' IS NOT NULL");
		if($count == 0){
			$wpdb->insert(
				$table,
					array(
						'title'	=> 'General'
					)
			);
		}
		
		$table = $wpdb->prefix . "tod_ticket_comments";
		$query = "CREATE TABLE IF NOT EXISTS $table (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `t_id` int(11) NOT NULL,
					  `author` varchar(65) COLLATE utf8_bin NOT NULL,
					  `title` varchar(65) COLLATE utf8_bin NOT NULL,
					  `content` text COLLATE utf8_bin NOT NULL,
					  PRIMARY KEY (`id`)
					) $charset_collate;";
		dbDelta($query);
		
		add_option( 'tod_helpdesk_db_version', $todHelpdeskDbVersion);
	}
	
}
$ToDHelpdesk = new todHelpdeskTickets();
register_activation_hook( __FILE__, array( 'todHelpdeskTickets', 'todHelpdesk_install' ) );