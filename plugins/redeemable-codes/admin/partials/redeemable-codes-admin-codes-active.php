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
require_once plugin_dir_path(__FILE__) . '../../includes/redeemable-codes-constants.php';

global $wpdb;
$table_name = $wpdb->prefix . REDEEMABLE_CODE_CODES_TABLE_NAME;
$active_codes = $wpdb->get_results(
    "SELECT * FROM $table_name 
        WHERE is_valid = 1 
            AND (expiration_date IS NULL OR expiration_date > NOW()) 
        ORDER BY id DESC"
);
?>
<!-- Active codes list -->
<h2><?php esc_html_e('Active Codes', $this->plugin_name); ?></h2>

<?php if (count($active_codes) == 0) : ?>
    <p><?php esc_html_e('No active code.', $this->plugin_name); ?></p>
<?php else : ?>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e('Code', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Speedtale ID', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Item to Redeem', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Score Offset', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Target Page', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Unique', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Expiration Date', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Created At', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Actions', $this->plugin_name); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($active_codes as $code) : ?>
                <tr>
                    <td><?php echo $code->code; ?></td>
                    <td><?php echo $code->speedtale_id; ?></td>
                    <td><?php echo $code->item_to_redeem; ?></td>
                    <td><?php echo $code->score_offset; ?></td>
                    <td><?php echo $code->target_page; ?></td>
                    <td><?php echo $code->is_unique ? "unique" : "not unique"; ?></td>
                    <td><?php echo $code->expiration_date; ?></td>
                    <td><?php echo $code->created_at; ?></td>
                    <td class="actions">
                        <form method="post" action="">
                            <?php wp_nonce_field('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'); ?>
                            <input type="hidden" name="code_id" value="<?php echo $code->id; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="button-small"><span class="dashicons dashicons-trash"></span></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>