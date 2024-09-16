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
 * @subpackage Multiverso_Leaderboard/admin/partials/leaderboard
 */


/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . '../../../includes/multiverso-leaderboard-constants.php';

global $wpdb;
$table_name = $wpdb->prefix . MULTIVERSO_LB_LEADERBOARD_TABLE_NAME;
$leaderboard = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>
<!-- Record della Classifica -->
<h2><?php esc_html_e('Voci della Classifica', $this->plugin_name); ?></h2>

<?php if (count($leaderboard) == 0) : ?>
    <p>
        <?php esc_html_e('Nessuna voce inserita.', $this->plugin_name); ?>
    </p>
<?php else : ?>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e('ID', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Scuola', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Classe', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Gruppo', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Speedtale', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Data Creazione', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Punteggio Totale', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Tempo Trascorso', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Actions', $this->plugin_name); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($leaderboard as $lb) : ?>
                <tr>
                    <td><?php echo $lb->id; ?></td>
                    <td><?php echo $lb->school_name; ?></td>
                    <td><?php echo $lb->class_name; ?></td>
                    <td><?php echo $lb->group_name; ?></td>
                    <td><?php echo $lb->speedtale_id; ?></td>
                    <td><?php echo $lb->created_at; ?></td>
                    <td><?php echo $lb->total_score; ?></td>
                    <td><?php echo $lb->elapsed_time_seconds; ?></td>
                    <td class="actions">
                        <form method="post" action="">
                            <?php wp_nonce_field('multiverso_leaderboard_generate_action', 'multiverso_leaderboard_nonce_field'); ?>
                            <input type="hidden" name="entry_id" value="<?php echo $lb->id; ?>">
                            <input type="hidden" name="namespace" value="leaderboard">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="button-small"><span class="dashicons dashicons-trash"></span></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>