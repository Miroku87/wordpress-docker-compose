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
    <h1><?php esc_html_e('Manage CORS Origins', $this->plugin_name); ?></h1>

    <!-- add a short and simple explanation on what are CORS origins -->
    <p>
        <?php esc_html_e('CORS origins are the domains that are allowed to access the API to redeem codes.', $this->plugin_name); ?><br>
        <?php esc_html_e('E.g. the websites where the SpeedTale Widget will reside in.', $this->plugin_name); ?>
    </p>

    <!-- CORS origins form -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-cors-form.php'; ?>

    <!-- CORS origins list -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-cors-list.php'; ?>
</div>