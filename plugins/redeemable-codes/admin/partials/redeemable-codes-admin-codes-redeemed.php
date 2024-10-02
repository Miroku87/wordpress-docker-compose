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
$redeemed_codes = $wpdb->get_results("SELECT * FROM $table_name WHERE is_valid = 0 ORDER BY redeemed_at DESC");
?>
<!-- Redeemed codes list -->
<h2><?php esc_html_e('Redeemed Codes', $this->plugin_name); ?></h2>

<?php if (count($redeemed_codes) == 0) : ?>
    <p><?php esc_html_e('No redeemed code.', $this->plugin_name); ?></p>
<?php else : ?>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e('Code', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Speedtale ID', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Item to Redeem', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Score Offset', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Target Page', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Expiration Date', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Created At', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Redeemed At', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Redeemed By', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Redeemed IP', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Redeemed User Agent', $this->plugin_name); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($redeemed_codes as $code) : ?>
                <tr>
                    <td><?php echo $code->code; ?></td>
                    <td><?php echo $code->speedtale_id; ?></td>
                    <td><?php echo $code->item_to_redeem; ?></td>
                    <td><?php echo $code->score_offset; ?></td>
                    <td><?php echo $code->target_page; ?></td>
                    <td><?php echo $code->expiration_date; ?></td>
                    <td><?php echo $code->created_at; ?></td>
                    <td><?php echo $code->redeemed_at; ?></td>
                    <td><?php echo $code->redeemed_by; ?></td>
                    <td><?php echo $code->redeemed_ip; ?></td>
                    <td><?php echo $code->redeemed_user_agent; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>