<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Stwidget
 * @subpackage Stwidget/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Stwidget
 * @subpackage Stwidget/includes
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Stwidget_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		Stwidget_Deactivator::drop_redeemable_codes_table();
		Stwidget_Deactivator::drop_allowed_origins_table();
	}

	/**
	 * Drops the redeemable codes table.
	 *
	 * @since    1.0.0
	 */
	private static function drop_redeemable_codes_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "test_1";

		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		delete_transient('redeemable_code_rate_limit_' . $_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Drops the allowed origins table.
	 *
	 * @since    1.0.0
	 */
	private static function drop_allowed_origins_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "test_2";

		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		delete_transient('redeemable_code_rate_limit_' . $_SERVER['REMOTE_ADDR']);
	}
}
