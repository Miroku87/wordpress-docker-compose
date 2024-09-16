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
 * @subpackage Multiverso_Leaderboard/admin/partials/cors
 */
?>

<div class="wrap">
    <h1><?php esc_html_e('Gestisci Origini CORS', $this->plugin_name); ?></h1>

    <!-- add a short and simple explanation on what are CORS origins -->
    <p>
        <?php esc_html_e('Le origini CORS sono domini dal quale è consentito accedere alle API per inserire punteggi.', $this->plugin_name); ?><br>
        <?php esc_html_e('Esempio: i siti web dove verrà ospitato il Widget delle SpeedTale.', $this->plugin_name); ?>
    </p>

    <!-- CORS origins form -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-form.php'; ?>

    <!-- CORS origins list -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-list.php'; ?>
</div>