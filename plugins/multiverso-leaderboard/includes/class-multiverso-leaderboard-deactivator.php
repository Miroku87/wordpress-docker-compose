<?php

/**
 * Fired during plugin deactivation
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

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Multiverso_Leaderboard
 * @subpackage Multiverso_Leaderboard/includes
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Multiverso_Leaderboard_Deactivator
{

	/**
	 * Deactivation of the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		Multiverso_Leaderboard_Deactivator::drop_multiverso_leaderboard_table();
		Multiverso_Leaderboard_Deactivator::drop_allowed_origins_table();
	}

	/**
	 * Drops the leaderboard table.
	 *
	 * @since    1.0.0
	 */
	private static function drop_multiverso_leaderboard_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_LEADERBOARD_TABLE_NAME;

		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		delete_transient('multiverso_leaderboard_rate_limit_' . $_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Drops the allowed origins table.
	 *
	 * @since    1.0.0
	 */
	private static function drop_allowed_origins_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_ORIGINS_TABLE_NAME;

		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		delete_transient('multiverso_leaderboard_rate_limit_' . $_SERVER['REMOTE_ADDR']);
	}
}
