<?php

/**
 * Fired during plugin activation
 *
 * @link       https://javothemes.com
 * @since      1.0.0
 *
 * @package    Javo_Chat
 * @subpackage Javo_Chat/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Javo_Chat
 * @subpackage Javo_Chat/includes
 * @author     Javo <javothemes@gmail.com>
 */
class Javo_Chat_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        // Create tables for chat and history
        self::create_chat_and_history_tables();
	}

    private static function create_chat_and_history_tables() {
		// Check if the tables exist, if not, create them
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		
		$core_conversations_table_name = $wpdb->prefix . 'javo_core_conversations';
		$core_conversations_meta_table_name = $wpdb->prefix . 'javo_core_conversations_meta';
		$history_table_name = $wpdb->prefix . 'javo_history';
		$history_meta_table_name = $wpdb->prefix . 'javo_history_meta';
		
		// Define SQL queries for table creation
		$core_conversations_sql = "CREATE TABLE $core_conversations_table_name (
			id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			sender_id VARCHAR(255) NOT NULL,
			receiver_id VARCHAR(255) NOT NULL,
			message TEXT NOT NULL,
			submit_date DATETIME NOT NULL,
			read_status TINYINT(1) DEFAULT 0
		) $charset_collate";

		$core_conversations_meta_sql = "CREATE TABLE $core_conversations_meta_table_name (
			meta_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			conversation_id BIGINT(20) UNSIGNED NOT NULL,
			meta_key VARCHAR(255),
			meta_value LONGTEXT
		) $charset_collate";

		$history_sql = "CREATE TABLE $history_table_name (
			id INT AUTO_INCREMENT PRIMARY KEY,
			user_id INT NOT NULL,
			action_type VARCHAR(100) NOT NULL,
			target_id INT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			INDEX action_type_index (action_type),
			INDEX target_id_index (target_id),
			INDEX created_at_index (created_at)
		) $charset_collate";

		$history_meta_sql = "CREATE TABLE $history_meta_table_name (
			meta_id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			history_id BIGINT(20) UNSIGNED NOT NULL,
			meta_key VARCHAR(255),
			meta_value LONGTEXT
		) $charset_collate";

		// Include upgrade.php for dbDelta()
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// Execute the SQL queries
		dbDelta($core_conversations_sql);
		dbDelta($core_conversations_meta_sql);
		dbDelta($history_sql);
		dbDelta($history_meta_sql);
	}

}
