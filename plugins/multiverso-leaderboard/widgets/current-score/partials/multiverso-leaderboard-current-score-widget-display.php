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

$entry_id = ! empty( $instance['entry_id'] ) ? $instance['entry_id'] : $_POST['entry_id'];

$results = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_name WHERE id = %d",
    $entry_id,
));

$minutes = floor($results->elapsed_time_seconds / 60);
$seconds = $results->elapsed_time_seconds % 60;

?>

<?php echo $widget_args['before_widget']; ?>

<!-- Record della Classifica -->
<?php if ( ! empty( $widget_instance['title'] ) ) : ?>
    <h2><?php echo $widget_args['before_title'] . apply_filters( 'widget_title', $widget_instance['title'] ) . $widget_args['after_title']; ?></h2>
<?php endif ?>

<h2><?php esc_html_e('Risultato Ottenuto', $this->plugin_name); ?></h2>
<p>
<?php printf(__('Hai recuperato %s gemme.', $this->plugin_name), $results->total_score); ?>
<?php printf(__('In %d minuti e %d secondi.', $this->plugin_name), $minutes, $seconds); ?>
</p>
<table class="wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <th scope="col"><?php esc_html_e('Scuola', $this->plugin_name); ?></th>
            <th scope="col"><?php esc_html_e('Classe', $this->plugin_name); ?></th>
            <th scope="col"><?php esc_html_e('Gruppo', $this->plugin_name); ?></th>
            <th scope="col"><?php esc_html_e('Data Creazione', $this->plugin_name); ?></th>
            <th scope="col"><?php esc_html_e('Punteggio Totale', $this->plugin_name); ?></th>
            <th scope="col"><?php esc_html_e('Tempo Trascorso', $this->plugin_name); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($leaderboard as $lb) : ?>
            <tr>
                <td><?php echo $lb->school_name; ?></td>
                <td><?php echo $lb->class_name; ?></td>
                <td><?php echo $lb->group_name; ?></td>
                <td><?php echo $lb->created_at; ?></td>
                <td><?php echo $lb->total_score; ?></td>
                <td><?php echo $lb->elapsed_time_seconds; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php echo $widget_args['after_widget']; ?>