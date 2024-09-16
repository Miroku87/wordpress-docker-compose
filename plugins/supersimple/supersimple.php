<?php

/**
 * @link              https://andreasilvestri.dev
 * @since             1.0.0
 * @package           Supersimple
 *
 * @wordpress-plugin
 * Plugin Name:       Super Simple Plugin
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            Andrea Silvestri
 * Text Domain:       supersimple
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function supersimple_admin_page()
{
    echo "<h1>Welcome to Super Simple</h1>";
}

function add_menu()
{
    add_menu_page(
        __('Super Simple', 'supersimple'), // Page title
        __('Super Simple', 'supersimple'), // Menu title
        'manage_options', // Capability
        'supersimple', // Menu slug
        'supersimple_admin_page', // Callback function
        'dashicons-tickets-alt', // Icon
        25 // Position
    );
}

add_action('admin_menu', 'add_menu');
