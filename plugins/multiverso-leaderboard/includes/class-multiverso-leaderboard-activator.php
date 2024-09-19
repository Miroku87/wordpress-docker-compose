<?php

/**
 * Fired during plugin activation
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Multiverso_Leaderboard
 * @subpackage Multiverso_Leaderboard/includes
 */

/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . 'multiverso-leaderboard-constants.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Multiverso_Leaderboard
 * @subpackage Multiverso_Leaderboard/includes
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Multiverso_Leaderboard_Activator
{

	/**
	 * Activation of the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		Multiverso_Leaderboard_Activator::generate_leaderboard_table();
		Multiverso_Leaderboard_Activator::generate_allowed_origins_table();
	}

	/**
	 * Creates the leaderboard table.
	 *
	 * @since    1.0.0
	 */
	private static function generate_leaderboard_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_LEADERBOARD_TABLE_NAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			school_name varchar(255) DEFAULT '' NOT NULL,
			class_name varchar(255) DEFAULT '' NOT NULL,
			group_name varchar(255) DEFAULT '' NOT NULL,
			speedtale_id varchar(255) NOT NULL,
			created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			elapsed_time_seconds int DEFAULT NULL,			
			total_score int DEFAULT NULL,
			time_bonus int DEFAULT NULL,
			crystals_num int DEFAULT NULL,
			hidden_crystals_num int DEFAULT NULL,
			side_missions int DEFAULT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta($sql);
	}

	/**
	 * Creates the allowed origins table.
	 *
	 * @since    1.0.0
	 */
	private static function generate_allowed_origins_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_ORIGINS_TABLE_NAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			origin varchar(255) DEFAULT '' NOT NULL,
			created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			expiration_date timestamp DEFAULT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY origin (origin)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
