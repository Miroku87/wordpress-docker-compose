<?php

/**
 * Fired during plugin deactivation
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

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Redeemable_Codes
 * @subpackage Redeemable_Codes/includes
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Redeemable_Codes_Deactivator
{

	/**
	 * Deactivation of the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		Redeemable_Codes_Deactivator::drop_redeemable_codes_table();
		Redeemable_Codes_Deactivator::drop_allowed_origins_table();
	}

	/**
	 * Drops the redeemable codes table.
	 *
	 * @since    1.0.0
	 */
	private static function drop_redeemable_codes_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;

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
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_ORIGINS_TABLE_NAME;

		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		delete_transient('redeemable_code_rate_limit_' . $_SERVER['REMOTE_ADDR']);
	}
}
