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
?>

<?php settings_errors('multiverso-leaderboard-cors-notices'); ?>

<!-- Form to add new origins -->
<form method="post" action="">
    <?php wp_nonce_field('multiverso_leaderboard_generate_action', 'multiverso_leaderboard_nonce_field'); ?>
    <h2><?php esc_html_e('Aggiungi Origine', $this->plugin_name); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="number_of_codes"><?php esc_html_e('Origine', $this->plugin_name); ?></label></th>
            <td><input name="origin" type="text" id="origin" value="*" class="regular-text" autocomplete="off"></td>
        </tr>
        <tr>
            <th scope="row"><label for="expiration_days"><?php esc_html_e('Data di scadenza (Giorni)', $this->plugin_name); ?></label></th>
            <td><input name="expiration_days" type="number" id="expiration_days" value="<?php echo MULTIVERSO_LB_EXPIRATION_DAYS; ?>" class="small-text" autocomplete="off"></td>
            <td>
                <?php esc_html_e('La data di scadenza è relativa alla data corrente.', $this->plugin_name); ?></br>
                <?php esc_html_e('Imposta 0 per non avere una scadenza.', $this->plugin_name); ?>
            </td>
        </tr>
    </table>
    <?php submit_button(__('Aggiungi Origine', $this->plugin_name)); ?>
</form>