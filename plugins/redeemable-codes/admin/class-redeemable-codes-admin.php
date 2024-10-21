<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Redeemable_Codes
 * @subpackage Redeemable_Codes/admin
 */

/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . '../includes/redeemable-codes-constants.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Redeemable_Codes
 * @subpackage Redeemable_Codes/admin
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Redeemable_Codes_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The CORS allowed origins.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Array<string>    $origins    The CORS allowed origins.
	 */
	private $origins;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->origins = array();

		$cors_origins = $this->get_allowed_origins();

		if (count($cors_origins) > 0) {
			foreach ($cors_origins as $cors_origin) {
				$this->origins[] = $cors_origin->origin;
			}
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/redeemable-codes-admin.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugins_url('css/admin-style.css', __FILE__), array(), $this->version, 'all');
		wp_enqueue_style('dashicons');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/redeemable-codes-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Initializes admin panel specific features.
	 *
	 * @since    1.0.0
	 */
	public function admin_init()
	{
		$this->handle_codes_form_submission();
		$this->handle_code_delete_request();
		$this->handle_redeemed_code_delete_request();
		$this->handle_origin_form_submission();
		$this->handle_origin_delete_request();
	}

	/**
	 * Initializes the admin panel menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu()
	{
		$this->add_admin_menu();
	}

	/**
	 * Initializes the REST API hooks.
	 *
	 * @since    1.0.0
	 */
	public function rest_api_init()
	{
		$this->add_rest_api_hooks();
	}

	/**
	 * Sets pre serve request rules up.
	 *
	 * @since    1.0.0
	 */
	public function rest_pre_serve_request($value)
	{
		return $this->add_rest_pre_serve_request($value);
	}

	/**
	 * Function to redeem the code.
	 *
	 * @since    1.0.0
	 */
	public function redeem_redeemable_code_patch($data)
	{
		global $wpdb;
		$input_code = $data['code'];
		$target_page = urldecode($data['target_page']);
		$table_name_all = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;
		$table_name_redeemed = $wpdb->prefix . REDEEMABLE_CODE_REDEEMED_CODES_TABLE_NAME;

		$rate_limit = $this->check_rate_limit();
		if (is_wp_error($rate_limit)) {
			return $rate_limit;
		}

		$codeData = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_name_all
				WHERE code = %s AND 
					target_page = %s AND 
					( expiration_date > NOW() OR expiration_date IS NULL )",
			$input_code,
			$target_page
		));

		if(!$codeData) {
			return new WP_Error('code_not_found', "Codice '$input_code' non trovato.", array('status' => 404));
		}

		$redeemData = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM $table_name_redeemed WHERE redeemed_id = %d",
			$codeData->id
		));
		
		if (count($redeemData) > 0 && $codeData->is_unique === "1") {
			return new WP_Error('code_already_redeemed', 'Il codice può essere usato solo una volta.', array('status' => 400));
		}

		$redeemData = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM $table_name_redeemed WHERE redeemed_id = %d AND redeemed_by = %s",
			$codeData->id,
			$data['redeemer']
		));

		if (count($redeemData) > 0) {
			return new WP_Error('code_already_redeemed', 'Il codice può essere usato solo una volta dallo stesso gruppo.', array('status' => 400));
		}

		$this->reset_rate_limit();

		$resp = $wpdb->insert(
			$table_name_redeemed,
			array(
				'redeemed_id' => intval($codeData->id),
				'redeemed_at' => current_time('mysql'),
				'redeemed_by' => $data['redeemer'],
				'redeemed_ip' => $_SERVER['REMOTE_ADDR'],
				'redeemed_user_agent' => $_SERVER['HTTP_USER_AGENT']
			),
			array('%d', '%s', '%s', '%s', '%s'),
		);

		$codeData = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_name_all WHERE code = %s",
			$input_code
		));

		$codeData->score_offset = intval($codeData->score_offset);
		$codeData->is_valid = $codeData->is_valid === "1" ? true : false;
		$codeData->is_unique = $codeData->is_unique === "1" ? true : false;

		return new WP_REST_Response($codeData, 200);
	}

	/**
	 * Ensures that the browser gets CORS information.
	 *
	 * @since    1.0.0
	 */
	public function rest_api_options()
	{
		if (count($this->origins) > 0) {
			header('Access-Control-Allow-Origin: ' . implode(', ', $this->origins));
		}

		header('Access-Control-Allow-Methods: ' . REDEEMABLE_CODE_CORS_ALLOWED_METHODS);
	}

	/**
	 * Prints the admin page.
	 *
	 * @since    1.0.0
	 */
	public function admin_page()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/redeemable-codes-admin-display-codes.php';
	}

	/**
	 * Prints the CORS origin admin page.
	 *
	 * @since    1.0.0
	 */
	public function cors_origin_page()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/redeemable-codes-admin-display-cors.php';
	}

	/**
	 * Adds the menu for the plugin.
	 *
	 * @since    1.0.0
	 */
	private function add_admin_menu()
	{
		add_menu_page(
			__('Redeemable Codes', $this->plugin_name), // Page title
			__('Redeemable Codes', $this->plugin_name), // Menu title
			'manage_options', // Capability
			$this->plugin_name, // Menu slug
			array($this, 'admin_page'), // Callback function
			'dashicons-tickets-alt', // Icon
			25 // Position
		);

		add_submenu_page(
			$this->plugin_name, // Parent slug
			__('Manage Codes', $this->plugin_name), // Page title
			__('Manage Codes', $this->plugin_name), // Menu title
			'manage_options', // Capability
			$this->plugin_name, // Menu slug
			array($this, 'admin_page'), // Callback function
		);

		add_submenu_page(
			$this->plugin_name, // Parent slug
			__('Manage CORS Origins', $this->plugin_name), // Page title
			__('Manage CORS Origins', $this->plugin_name), // Menu title
			'manage_options', // Capability
			$this->plugin_name . '-cors', // Menu slug
			array($this, 'cors_origin_page') // Callback function
		);
	}

	/**
	 * Handle the submission of the redeemable codes form.
	 *
	 * @since    1.0.0
	 */
	private function handle_codes_form_submission()
	{
		if (!isset($_POST['redeemable_codes_nonce_field']) || !isset($_POST['code_type']))
			return;

		if (!check_admin_referer('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'))
			wp_die(__('You cannot access this page.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('You do not have sufficient permissions to access this page.', $this->plugin_name));

		$code_type = $_POST['code_type'];

		if (isset($_POST['speedtale_id']) && $_POST['speedtale_id'] != '') {
			$speedtale_id = $_POST['speedtale_id'];
		} else {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Missing or invalid SpeedTale ID.', $this->plugin_name), 'error');
			return;
		}

		if (isset($_POST['item']) && $_POST['item'] != '') {
			$item = $_POST['item'];
		}

		if (isset($_POST['score_offset']) && $_POST['score_offset'] != 0) {
			$score_offset = $_POST['score_offset'];
		}
		
		if (!isset($item) && !isset($score_offset)) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Item name or Score Offset must be set.', $this->plugin_name), 'error');
			return;
		}

		if (isset($_POST['target_page']) && $_POST['target_page'] != 0) {
			$target_page = $_POST['target_page'];
		}

		if (isset($_POST['expiration_days']) && intval($_POST['expiration_days']) > 0) {
			$expiration_days = intval($_POST['expiration_days']);
		} else {
			$expiration_days = NULL;
		}

		switch ($code_type) {
			case "random":
				$this->handle_random_code_submission($speedtale_id, $item, $target_page, $expiration_days);
				break;
			case "custom":
				$this->handle_custom_code_submission($speedtale_id, $item, $score_offset, $target_page, $expiration_days);
				break;
			default:
				$message = sprintf(__('Invalid code type `%s`.', $this->plugin_name), $code_type);
				add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', $message, 'error');
		}
	}

	/**
	 * Handle the submission of a random code.
	 *
	 * @since    1.0.0
	 */
	private function handle_random_code_submission($speedtale_id, $item, $target_page, $expiration_days)
	{
		if (isset($_POST['number_of_codes']) && intval($_POST['number_of_codes']) > 0) {
			$number_of_codes = intval($_POST['number_of_codes']);
		} else {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Invalid number of codes.', $this->plugin_name), 'error');
			return;
		}

		$generated_codes = $this->generate_and_store_multiple_redeemable_codes($number_of_codes, $speedtale_id, $item, $target_page, $expiration_days);

		// Provide admin notice or other feedback
		if (count($generated_codes) > 0) {
			$message = sprintf(
				_n('%d code generated.', '%d codes generated.', $number_of_codes, $this->plugin_name),
				$number_of_codes
			);
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', $message, 'updated');
			return;
		}

		add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('No codes generated.', $this->plugin_name), 'error');
	}

	/**
	 * Handle the submission of a custom code.
	 *
	 * @since    1.0.0
	 */
	private function handle_custom_code_submission($speedtale_id, $item, $score_offset, $target_page, $expiration_days)
	{
		if (isset($_POST['custom_code']) && $_POST['custom_code'] != '') {
			$custom_code = $_POST['custom_code'];
		} else {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Missing or invalid code.', $this->plugin_name), 'error');
			return;
		}

		$is_unique = False;
		if (isset($_POST['is_unique'])) {
			$is_unique = True;
		}

		$codeInDb = $this->get_redeemable_code($speedtale_id, $custom_code);

		if ($is_unique && !is_wp_error($codeInDb)) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Code should be unique but already exists in the database.', $this->plugin_name), 'error');
			return;
		}

		if (!$is_unique && !is_wp_error($codeInDb) && $codeInDb->is_unique == 1) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Code already exists in the database and is set as unique.', $this->plugin_name), 'error');
			return;
		}

		$resp = $this->store_redeemable_code($custom_code, $speedtale_id, $item, $score_offset, $target_page, $expiration_days, $is_unique);
		if (is_wp_error($resp)) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', $resp->get_error_message(), 'error');
			return;
		}

		$message = sprintf(
			_n('%d code generated.', '%d codes generated.', 1, $this->plugin_name),
			1
		);
		add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', $message, 'updated');
	}

	/**
	 * Handle the an active code delete request.
	 *
	 * @since    1.0.0
	 */
	private function handle_code_delete_request()
	{
		if (!isset($_POST['action']) || !isset($_POST['code_id']))
			return;

		if ($_POST['action'] != 'delete')
			return;

		if (!check_admin_referer('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'))
			wp_die(__('You cannot access this page.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('You do not have sufficient permissions to access this page.', $this->plugin_name));

		$code_id = intval($_POST['code_id']);

		if ($code_id <= 0) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Invalid code ID.', $this->plugin_name), 'error');
		}

		$ok = $this->delete_redeemable_code($code_id);

		if (is_wp_error($ok)) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', $ok->get_error_message(), 'error');
			return;
		}

		add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Code deleted.', $this->plugin_name), 'updated');
	}

	/**
	 * Handle the a redeemed code delete request.
	 *
	 * @since    1.0.0
	 */
	private function handle_redeemed_code_delete_request()
	{
		if (!isset($_POST['action']) || !isset($_POST['code_id']))
			return;

		if ($_POST['action'] != 'delete-redeemed')
			return;

		if (!check_admin_referer('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'))
			wp_die(__('You cannot access this page.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('You do not have sufficient permissions to access this page.', $this->plugin_name));

		$code_id = intval($_POST['code_id']);

		if ($code_id <= 0) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Invalid code ID.', $this->plugin_name), 'error');
		}

		$ok = $this->delete_redeemed_code($code_id);

		if (is_wp_error($ok)) {
			add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', $ok->get_error_message(), 'error');
			return;
		}

		add_settings_error('redeemable-codes-notices', 'redeemable-codes-notices', __('Code deleted.', $this->plugin_name), 'updated');
	}

	/**
	 * Handle the submission of a new CORS origin.
	 *
	 * @since    1.0.0
	 */
	private function handle_origin_form_submission()
	{
		if (!isset($_POST['redeemable_codes_nonce_field']) || !isset($_POST['origin']))
			return;

		if (!check_admin_referer('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'))
			wp_die(__('You cannot access this page.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('You do not have sufficient permissions to access this page.', $this->plugin_name));

		$origin = $_POST['origin'];

		if (isset($_POST['expiration_days']) && intval($_POST['expiration_days']) > 0) {
			$expiration_days = intval($_POST['expiration_days']);
		} else {
			$expiration_days = NULL;
		}

		// Generate and store the codes
		$insert_ok = $this->add_allowed_origin($origin, $expiration_days);

		if (is_wp_error($insert_ok)) {
			$message = $insert_ok->get_error_message();

			if (strpos($message, 'Duplicate entry') !== false) {
				$message = sprintf(__('Origin %s already exists.', $this->plugin_name), $origin);
			}

			add_settings_error('redeemable-codes-cors-notices', 'redeemable-codes-cors-notices', $message, 'error');
			return;
		}

		$message = sprintf(__('Origin %s added.', $this->plugin_name), $origin);
		add_settings_error('redeemable-codes-cors-notices', 'redeemable-codes-cors-notices', $message, 'updated');
	}

	/**
	 * Handle the a CORS origin delete request.
	 *
	 * @since    1.0.0
	 */
	private function handle_origin_delete_request()
	{
		if (!isset($_POST['action']) || !isset($_POST['origin_id']))
			return;

		if ($_POST['action'] != 'delete')
			return;

		if (!check_admin_referer('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'))
			wp_die(__('You cannot access this page.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('You do not have sufficient permissions to access this page.', $this->plugin_name));

		$origin_id = intval($_POST['origin_id']);

		if ($origin_id <= 0) {
			add_settings_error('redeemable-codes-cors-notices', 'redeemable-codes-cors-notices', __('Invalid origin ID.', $this->plugin_name), 'error');
		}

		$ok = $this->delete_allowed_origin($origin_id);

		if (is_wp_error($ok)) {
			add_settings_error('redeemable-codes-cors-notices', 'redeemable-codes-cors-notices', $ok->get_error_message(), 'error');
			return;
		}

		add_settings_error('redeemable-codes-cors-notices', 'redeemable-codes-cors-notices', __('Origin deleted.', $this->plugin_name), 'updated');
	}

	/**
	 * Function to generate and store multiple redeemable codes.
	 *
	 * @since    1.0.0
	 */
	private function generate_and_store_multiple_redeemable_codes($number_of_codes, $speedtale_id, $item, $target_page, $expirationDateDays = NULL)
	{
		$codes = array();
		$retries = 0;

		while (count($codes) < $number_of_codes && $retries < REDEEMABLE_CODE_CODE_GEN_MAX_RETRIES) {
			$code = $this->generate_redeemable_code();
			if ($this->redeemable_code_already_in_use($code, $speedtale_id)) {
				$retries++;
				continue;
			}

			$resp = $this->store_redeemable_code($code, $speedtale_id, $item, 0, $target_page, $expirationDateDays, True);
			if (is_wp_error($resp)) {
				$retries++;
				continue;
			}

			$codes[] = $code;
		}

		return $codes;
	}

	/**
	 * Function to generate a redeemable code in the format "ABC-123".
	 *
	 * @since    1.0.0
	 */
	private function generate_redeemable_code()
	{
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$segments = [];

		for ($j = 0; $j < 2; $j++) {
			$segment = '';
			for ($i = 0; $i < 3; $i++) {
				$segment .= $characters[rand(0, strlen($characters) - 1)];
			}
			$segments[] = $segment;
		}

		return implode('-', $segments);
	}

	/**
	 * Gets the information about the redeemable code.
	 *
	 * @since    1.0.0
	 */
	private function get_redeemable_code_info($request)
	{
		global $wpdb;
		$code = $request['code'];
		$speedtale_id = $request['speedtale_id'];

		if (!isset($code) || !isset($speedtale_id)) {
			return new WP_Error('redeemable-codes-error', __('Invalid request.', $this->plugin_name), array('status' => 400));
		}

		$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;

		$rate_limit = $this->check_rate_limit();
		if (is_wp_error($rate_limit)) {
			return $rate_limit;
		}

		$codeData = $this->get_redeemable_code($speedtale_id, $code);

		if (!is_wp_error($codeData)) {
			$this->reset_rate_limit();
			return new WP_REST_Response($codeData, 200);
		}

		$this->increment_rate_limit();

		return $codeData;
	}

	/**
	 * Gets the redeemable code record from DB.
	 *
	 * @since    1.0.0
	 */
	private function get_redeemable_code($speedtale_id, $code)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;

		$codeData = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_name WHERE code = %s AND speedtale_id = %s",
			array($code, $speedtale_id)
		));

		if ($codeData) {
			return $codeData;
		}

		return new WP_Error('code_not_found', 'Code not found', array('status' => 404));
	}

	/**
	 * Check if the redeemable code is already in use.
	 *
	 * @since    1.0.0
	 */
	private function redeemable_code_already_in_use($code, $speedtale_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;

		$codeData = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $table_name WHERE 
				code = %s 
				AND speedtale_id = %s
				AND expiration_date NOT NULL 
				AND expiration_date > NOW()",
			array($code, $speedtale_id)
		));

		if (is_null($codeData)) {
			return false;
		}

		return true;
	}

	/**
	 * Registers the REST API hooks.
	 *
	 * @since    1.0.0
	 */
	private function add_rest_api_hooks()
	{
		register_rest_route('redeemable-codes/v1', '/codes/redeem', array(
			'methods' => 'PATCH',
			'callback' => array($this, 'redeem_redeemable_code_patch'),
		));

		register_rest_route('redeemable-codes/v1', '/codes/redeem', array(
			'methods' => 'OPTIONS',
			'callback' => array($this, 'rest_api_options'),
		));
	}

	/**
	 * Adds REST pre serve request rules.
	 *
	 * @since    1.0.0
	 */
	private function add_rest_pre_serve_request($value)
	{
		$this->rest_api_options();

		return $value;
	}

	/**
	 * Checks the rate limit of an IP.
	 *
	 * @since    1.0.0
	 */
	private function check_rate_limit()
	{
		$user_ip = $_SERVER['REMOTE_ADDR']; // Get the IP address of the client.
		$rate_limit_key = 'redeemable_code_rate_limit_' . $user_ip;
		$rate_limit = get_transient($rate_limit_key);

		// Check if the current IP has hit the rate limit.
		if ($rate_limit >= REDEEMABLE_CODE_RATE_LIMIT_REQUESTS) {
			return new WP_Error('rate_limit_exceeded', 'Too many requests', array('status' => 429));
		}

		return true;
	}

	/**
	 * Increments the rate limit of an IP.
	 *
	 * @since    1.0.0
	 */
	private function increment_rate_limit()
	{
		$user_ip = $_SERVER['REMOTE_ADDR']; // Get the IP address of the client.
		$rate_limit_key = 'redeemable_code_rate_limit_' . $user_ip;
		$rate_limit = get_transient($rate_limit_key);

		// Increment the rate limit counter.
		if ($rate_limit) {
			set_transient($rate_limit_key, $rate_limit + 1, REDEEMABLE_CODE_RATE_LIMIT_SECONDS); // Increment the count and set a 1-minute expiration.
		} else {
			set_transient($rate_limit_key, 1, REDEEMABLE_CODE_RATE_LIMIT_SECONDS); // Start the count with a 1-minute expiration.
		}
	}

	/**
	 * Resets the rate limit of an IP.
	 *
	 * @since    1.0.0
	 */
	private function reset_rate_limit()
	{
		$user_ip = $_SERVER['REMOTE_ADDR']; // Get the IP address of the client.
		$rate_limit_key = 'redeemable_code_rate_limit_' . $user_ip;

		delete_transient($rate_limit_key);
	}

	/**
	 * Gets the allowed origins from the database.
	 *
	 * @since    1.0.0
	 */
	private function get_allowed_origins()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_ORIGINS_TABLE_NAME;

		$allowed_origins = $wpdb->get_col("SELECT origin FROM $table_name WHERE expiration_date IS NULL OR expiration_date > NOW()");
		return $allowed_origins;
	}

	/**
	 * Adds an allowed origins to the database.
	 *
	 * @since    1.0.0
	 */
	private function add_allowed_origin($origin, $expiration_days = NULL)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_ORIGINS_TABLE_NAME;
		$resp = $wpdb->insert(
			$table_name,
			array(
				'origin' => $origin,
				'expiration_date' => $expiration_days ? date('Y-m-d H:i:s', strtotime('+' . $expiration_days . ' days')) : NULL,
				'created_at' => date('Y-m-d H:i:s'),
			),
			array('%s', '%s', '%s'),
		);

		if (!$resp) {
			return new WP_Error('add_allowed_origin_error', $wpdb->last_error);
		}

		return intval($wpdb->insert_id);
	}

	/**
	 * Deletes an allowed origins to the database.
	 *
	 * @since    1.0.0
	 */
	private function delete_allowed_origin($origin_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_ORIGINS_TABLE_NAME;
		$resp = $wpdb->delete(
			$table_name,
			array('id' => $origin_id),
			array('%d'),
		);

		if (!$resp) {
			return new WP_Error('delete_allowed_origin_error', $wpdb->last_error);
		}

		return True;
	}

	/**
	 * Function to store the generated code in the database.
	 *
	 * @since    1.0.0
	 */
	private function store_redeemable_code($code, $speedtale_id, $item = "", $score_offset = 0, $target_page = "", $expirationDateDays = NULL, $is_unique = False)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;

		$item = !empty($item) ? $item : NULL;
		$score_offset = $score_offset > 0 ? intval($score_offset) : NULL;
		$expirationDate = !is_null($expirationDateDays) ? date('Y-m-d H:i:s', strtotime("+$expirationDateDays days")) : NULL;

		$resp = $wpdb->insert(
			$table_name,
			array(
				'code' => $code,
				'speedtale_id' => $speedtale_id,
				'item_to_redeem' => $item,
				'score_offset' => $score_offset,
				'target_page' => $target_page,
				'created_at' => current_time('mysql'),
				'expiration_date' => $expirationDate,
				'is_valid' => 1,
				'is_unique' => $is_unique ? 1 : 0,
			),
			array('%s', '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%d')
		);

		if (!$resp) {
			return new WP_Error('code_not_stored', $wpdb->last_error);
		}

		return intval($wpdb->insert_id);
	}

	/**
	 * Function to delete an active code.
	 *
	 * @since    1.0.0
	 */
	private function delete_redeemable_code($code_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;
		$resp = $wpdb->delete(
			$table_name,
			array('id' => $code_id),
			array('%d'),
		);

		if (!$resp) {
			return new WP_Error('delete_redeemable_code_error', $wpdb->last_error);
		}

		return True;
	}

	/**
	 * Function to delete a redeemed code
	 *
	 * @since    1.0.0
	 */
	private function delete_redeemed_code($code_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . REDEEMABLE_CODE_REDEEMED_CODES_TABLE_NAME;
		$resp = $wpdb->delete(
			$table_name,
			array('id' => $code_id),
			array('%d'),
		);

		if (!$resp) {
			return new WP_Error('delete_redeemable_code_error', $wpdb->last_error);
		}

		return True;
	}
}
