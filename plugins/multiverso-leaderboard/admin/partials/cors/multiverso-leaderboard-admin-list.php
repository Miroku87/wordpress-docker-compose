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


/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . '../../../includes/multiverso-leaderboard-constants.php';

global $wpdb;
$table_name = $wpdb->prefix . MULTIVERSO_LB_ORIGINS_TABLE_NAME;
$cors_origins = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>
<!-- Lista Origini CORS -->
<h2><?php esc_html_e('Lista Origini CORS', $this->plugin_name); ?></h2>

<?php if (count($cors_origins) == 0) : ?>
    <p>
        <?php esc_html_e('Nessuna voce inserita.', $this->plugin_name); ?>
    </p>
<?php else : ?>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e('Origine', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Data di Scadenza', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Creato il', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Azioni', $this->plugin_name); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($cors_origins as $origin) : ?>
                <tr>
                    <td><?php echo $origin->origin; ?></td>
                    <td><?php echo $origin->expiration_date; ?></td>
                    <td><?php echo $origin->created_at; ?></td>
                    <td class="actions">
                        <form method="post" action="">
                            <?php wp_nonce_field('multiverso_leaderboard_generate_action', 'multiverso_leaderboard_nonce_field'); ?>
                            <input type="hidden" name="origin_id" value="<?php echo $origin->id; ?>">
                            <input type="hidden" name="namespace" value="cors">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="button-small"><span class="dashicons dashicons-trash"></span></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>