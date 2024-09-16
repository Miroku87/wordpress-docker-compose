<?php

/**
 * Fired during plugin activation
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Stwidget
 * @subpackage Stwidget/includes
 */
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Stwidget
 * @subpackage Stwidget/includes
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Stwidget_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		Stwidget_Activator::generate_redeemable_codes_table();
		Stwidget_Activator::generate_allowed_origins_table();
	}

	private static function generate_redeemable_codes_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "test_1";

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			code varchar(7) NOT NULL,
			speedtale_id mediumint(9) NOT NULL,
			item_to_redeem varchar(255) DEFAULT '' NOT NULL,
			created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			expiration_date timestamp DEFAULT NULL,
			redeemed_at timestamp DEFAULT NULL,
			redeemed_by varchar(255) DEFAULT '' NOT NULL,
			redeemed_ip varchar(255) DEFAULT '' NOT NULL,
			redeemed_user_agent varchar(255) DEFAULT '' NOT NULL,
			is_valid tinyint(1) DEFAULT 1 NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY code (code)
		) $charset_collate;";

		dbDelta($sql);
	}

	private static function generate_allowed_origins_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "test_2";

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			origin varchar(255) NOT NULL,
			created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			expiration_date timestamp DEFAULT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY origin (origin)
		) $charset_collate;";

		dbDelta($sql);
	}
}
