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

$entry_id = !empty( $_POST['entry_id'] ) ? $_POST['entry_id'] : 0;
$entry_id = $entry_id <= 0 && !empty( $widget_instance['entry_id'] ) ? $widget_instance['entry_id'] : 0;

if (empty($entry_id)) {
    esc_html_e('Nessun risultato da visualizzare', $this->plugin_name);
    return;
}

$results = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_name WHERE id = %d",
    $entry_id,
));

if ( is_null($results) ) {
    esc_html_e('Nessun risultato da visualizzare', $this->plugin_name);
    return;
}

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
    <?php printf(__('Hai accumulato %s punti.', $this->plugin_name), $results->total_score); ?> <br>
    <?php printf(__('In %d minuti e %d secondi.', $this->plugin_name), $minutes, $seconds); ?> <br>
    <?php printf(__('Hai trovato %d cristalli, di cui %d nascosti.', $this->plugin_name), $results->crystals_num, $results->hidden_crystals_num); ?> <br>
    <?php printf(__('Hai completato %d missioni secondarie.', $this->plugin_name), $results->side_missions); ?> <br>
    <?php if ($results->time_bonus > 0) : ?>
        <?php printf(__('Visto che hai completato il gioco prima del tempo previsto hai ricevuto %d punti bonus.', $this->plugin_name), $results->time_bonus); ?>
    <?php endif; ?>
</p>

<?php echo $widget_args['after_widget']; ?>