<?php

/**
 * Provide a view for the widget
 *
 * This file is used to markup the user-facing aspects of the widget.
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

$entry_id = !empty( $_GET['entry_id'] ) ? intval($_GET['entry_id']) : 0;
$entry_id = $entry_id <= 0 && !empty( $widget_instance['entry_id'] ) ? $widget_instance['entry_id'] : $entry_id;

$speedtale_id = !empty( $_GET['speedtale_id'] ) ? $_GET['speedtale_id'] : '';
$speedtale_id = empty( $speedtale_id ) && !empty( $widget_instance['speedtale_id'] ) ? $widget_instance['speedtale_id'] : $speedtale_id;

$timeframe = ! empty( $widget_instance['timeframe'] ) ? $widget_instance['timeframe'] : 'complete';

if ( $timeframe === 'today' ) {
    $leaderboard =  $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE DATE(updated_at) = CURDATE() AND elapsed_time_seconds > 0 AND speedtale_id = %s ORDER BY total_score DESC, elapsed_time_seconds DESC",
        $speedtale_id
    ));
} else {
    $leaderboard =  $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE updated_at IS NOT NULL AND speedtale_id = %s ORDER BY total_score DESC, elapsed_time_seconds DESC",
        $speedtale_id
    ));
}
?>

<?php echo $widget_args['before_widget']; ?>

<!-- Record della Classifica -->
<?php if ( ! empty( $widget_instance['title'] ) ) : ?>
    <h2><?php echo $widget_args['before_title'] . apply_filters( 'widget_title', $widget_instance['title'] ) . $widget_args['after_title']; ?></h2>
<?php endif ?>

<?php if (count($leaderboard) == 0) : ?>
    <p>
        <?php esc_html_e('Nessuna voce inserita.', $this->plugin_name); ?>
    </p>
<?php else : ?>

    <table class="wp-list-table widefat fixed striped posts leaderboard">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e('Gruppo', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Punti', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Classe', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Scuola', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Tempo', $this->plugin_name); ?></th>
                <th scope="col"><?php esc_html_e('Data', $this->plugin_name); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($leaderboard as $lb) : ?>
                <?php $minutes = floor($lb->elapsed_time_seconds / 60); ?>
                <?php $seconds = $lb->elapsed_time_seconds % 60; ?>
                <?php $date = date('d/m/Y', strtotime($lb->updated_at)); ?>
                <tr <?php if ($lb->id == $entry_id) : ?> class="current-score" <?php endif; ?>>
                    <td><?php echo urldecode($lb->group_name); ?></td>
                    <td><?php echo $lb->total_score; ?></td>
                    <td><?php echo urldecode($lb->class_name); ?></td>
                    <td><?php echo urldecode($lb->school_name); ?></td>
                    <td><?php printf(__('%d minuti e %d secondi', $this->plugin_name), $minutes, $seconds); ?></td>
                    <td><?php echo $date; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?php echo $widget_args['after_widget']; ?>