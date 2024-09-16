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

<?php settings_errors('multiverso-leaderboard-notices'); ?>

<form method="post" action="" id="codes_form">
    <?php wp_nonce_field('redeemable_codes_generate_action', 'redeemable_codes_nonce_field'); ?>
    <h2><?php esc_html_e('Generate Codes', $this->plugin_name); ?></h2>
    <table class="form-table code-type-custom" id="form_table">
        <tr>
            <th scope="row"><label for="speedtale_id"><?php esc_html_e('Speedtale ID', $this->plugin_name); ?></label></th>
            <td><input name="speedtale_id" type="text" id="speedtale_id" value="" class="small-text" autocomplete="off"></td>
            <td><?php esc_html_e('The speedtale ID associated with the item.', $this->plugin_name); ?></td>
        </tr>
        <tr>
            <th scope="row"><label for="item"><?php esc_html_e('Item', $this->plugin_name); ?></label></th>
            <td><input name="item" type="text" id="item" value="" class="regular-text" autocomplete="off"></td>
            <td><?php esc_html_e('The item to be redeemed.', $this->plugin_name); ?></td>
        </tr>
        <tr>
            <th scope="row"><label for="expiration_days"><?php esc_html_e('Expiration Date (Days)', $this->plugin_name); ?></label></th>
            <td><input name="expiration_days" type="number" id="expiration_days" value="<?php echo REDEEMABLE_CODE_EXPIRATION_DAYS; ?>" class="small-text" autocomplete="off"></td>
            <td>
                <?php esc_html_e('The expiration date is relative to the current date.', $this->plugin_name); ?></br>
                <?php esc_html_e('Set to 0 for no expiration.', $this->plugin_name); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="code_type"><?php esc_html_e('Code Type', $this->plugin_name); ?></label></th>
            <td><select name="code_type" id="code_type" autocomplete="off">
                    <option value="custom" selected><?php esc_html_e('Custom', $this->plugin_name); ?></option>
                    <option value="random"><?php esc_html_e('Random', $this->plugin_name); ?></option>
                </select></td>
            <td><?php esc_html_e('Whether you want to generate random codes or insert a custom one.', $this->plugin_name); ?></td>
        </tr>
        <tr class="code-type random">
            <th scope="row"><label for="number_of_codes"><?php esc_html_e('Quantity of Codes', $this->plugin_name); ?></label></th>
            <td><input name="number_of_codes" type="number" id="number_of_codes" value="1" class="small-text" autocomplete="off"></td>
            <td><?php esc_html_e('Quantity of codes to generate with a single request.', $this->plugin_name); ?></td>
        </tr>
        <tr class="code-type custom">
            <th scope="row"><label for="custom_code"><?php esc_html_e('Code', $this->plugin_name); ?></label></th>
            <td><input name="custom_code" type="text" id="custom_code" placeholder="<?php esc_html_e('Write custom code here', $this->plugin_name); ?>" class="regular-text" autocomplete="off"></td>
            <td>
                <?php esc_html_e('Custom code.', $this->plugin_name); ?>
            </td>
        </tr>
        <tr class="code-type custom">
            <th scope="row"><label for="is_unique"><?php esc_html_e('Is Unique', $this->plugin_name); ?></label></th>
            <td><input name="is_unique" type="checkbox" id="is_unique" value="1" autocomplete="off"></td>
            <td>
                <?php esc_html_e('Check this if this code should be unique for this Speedtale.', $this->plugin_name); ?></br>
                <?php esc_html_e('Just one user will be able to redeem it.', $this->plugin_name); ?>
            </td>
        </tr>
    </table>
    <?php submit_button(__('Generate Codes', $this->plugin_name)); ?>
</form>