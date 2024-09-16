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


/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . '../../includes/multiverso-leaderboard-constants.php';

global $wpdb;
$table_name = $wpdb->prefix . REDEEMABLE_CODE_ORIGINS_TABLE_NAME;
$cors_origins = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>
<!-- CORS Origin list -->
<h2><?php esc_html_e('CORS Origin List', $this->plugin_name); ?></h2>

<?php if (count($cors_origins) == 0) : ?>
    <p>
        <?php esc_html_e('No origins added.', $this->plugin_name); ?>
    </p>
<?php else : ?>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e('Origin', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Expiration Date', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Created At', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Actions', $this->plugin_name); ?></th>
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
                            <?php wp_nonce_field('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'); ?>
                            <input type="hidden" name="origin_id" value="<?php echo $origin->id; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="button-small"><span class="dashicons dashicons-trash"></span></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>