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
?>

<?php settings_errors('redeemable-codes-cors-notices'); ?>

<!-- Form to add new origins -->
<form method="post" action="">
    <?php wp_nonce_field('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'); ?>
    <h2><?php esc_html_e('Add Origin', $this->plugin_name); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="number_of_codes"><?php esc_html_e('Origin', $this->plugin_name); ?></label></th>
            <td><input name="origin" type="text" id="origin" value="*" class="regular-text" autocomplete="off"></td>
        </tr>
        <tr>
            <th scope="row"><label for="expiration_days"><?php esc_html_e('Expiration Date (Days)', $this->plugin_name); ?></label></th>
            <td><input name="expiration_days" type="number" id="expiration_days" value="<?php echo REDEEMABLE_CODE_EXPIRATION_DAYS; ?>" class="small-text" autocomplete="off"></td>
            <td>
                <?php esc_html_e('The expiration date is relative to the current date.', $this->plugin_name); ?></br>
                <?php esc_html_e('Set to 0 for no expiration.', $this->plugin_name); ?>
            </td>
        </tr>
    </table>
    <?php submit_button(__('Add Origin', $this->plugin_name)); ?>
</form>