<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Redeemable_Codes
 * @subpackage Redeemable_Codes/admin/partials
 */
?>

<div class="wrap">
    <h1><?php esc_html_e('Manage Redeemable Codes', $this->plugin_name); ?></h1>

    <!-- Form to generate new codes -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-codes-form.php'; ?>

    <!-- Active codes list -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-codes-active.php'; ?>

    <!-- Redeemed codes list -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-codes-redeemed.php'; ?>
</div>