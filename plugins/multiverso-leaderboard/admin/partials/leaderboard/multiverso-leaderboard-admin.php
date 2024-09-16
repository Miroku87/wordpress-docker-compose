<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Multiverso_Leaderboard
 * @subpackage Multiverso_Leaderboard/admin/partials
 */
?>

<div class="wrap">
    <h1><?php esc_html_e('Visualizza Classifica', $this->plugin_name); ?></h1>

    <!-- Leaderboard records -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-list.php'; ?>
</div>