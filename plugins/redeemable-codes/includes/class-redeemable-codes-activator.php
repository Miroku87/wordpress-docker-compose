<?php

/**
 * Fired during plugin activation
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Redeemable_Codes
 * @subpackage Redeemable_Codes/includes
 */

/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . 'redeemable-codes-constants.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Redeemable_Codes
 * @subpackage Redeemable_Codes/includes
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Redeemable_Codes_Activator
{

	/**
	 * Activation of the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		Redeemable_Codes_Activator::generate_redeemable_codes_table();
		Redeemable_Codes_Activator::generate_redeemable_codes_redeemed_table();
		Redeemable_Codes_Activator::generate_allowed_origins_table();
	}

	/**
	 * Creates the redeemable codes table.
	 *
	 * @since    1.0.0
	 */
	private static function generate_redeemable_codes_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			code varchar(7) NOT NULL,
			speedtale_id varchar(255) NOT NULL,
			item_to_redeem varchar(255) DEFAULT '',
			score_offset int DEFAULT 0,
			target_page varchar(255) DEFAULT '',
			created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			expiration_date timestamp NULL DEFAULT NULL,
			is_unique tinyint(1) DEFAULT 0 NOT NULL,
			is_valid tinyint(1) DEFAULT 1 NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta($sql);
	}

	/**
	 * Creates the redeemable codes redeemed_table.
	 *
	 * @since    1.0.0
	 */
	private static function generate_redeemable_codes_redeemed_table()
	{
		global $wpdb;
		$table_name_redeemed = $wpdb->prefix . REDEEMABLE_CODE_REDEEMED_CODES_TABLE_NAME;
		$table_name_all = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name_redeemed (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			redeemed_id mediumint(9) NOT NULL,
			redeemed_at timestamp DEFAULT NULL,
			redeemed_by varchar(255) DEFAULT NULL,
			redeemed_ip varchar(255) DEFAULT NULL,
			redeemed_user_agent varchar(255) DEFAULT NULL,
			PRIMARY KEY  (id),
			FOREIGN KEY (redeemed_id) REFERENCES $table_name_all(id)
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
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_ORIGINS_TABLE_NAME;

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
