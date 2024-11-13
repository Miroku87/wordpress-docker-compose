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

$entry_id = !empty($_GET['entry_id']) ? intval($_GET['entry_id']) : 0;
$entry_id = $entry_id <= 0 && !empty($widget_instance['entry_id']) ? $widget_instance['entry_id'] : $entry_id;

if (empty($entry_id)) {
    esc_html_e('Nessun risultato da visualizzare', $this->plugin_name);
    return;
}

$results = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_name WHERE id = %d",
    $entry_id,
));

if (is_null($results)) {
    esc_html_e('Nessun risultato da visualizzare', $this->plugin_name);
    return;
}

$minutes = floor($results->elapsed_time_seconds / 60);
$seconds = $results->elapsed_time_seconds % 60;

?>

<?php echo $widget_args['before_widget']; ?>

<!-- Record della Classifica -->
<?php if (! empty($widget_instance['title'])) : ?>
    <h2><?php echo $widget_args['before_title'] . apply_filters('widget_title', $widget_instance['title']) . $widget_args['after_title']; ?></h2>
<?php endif ?>

<p class="lb-summary">
    Hai accumulato <span><?php echo $results->total_score; ?></span> punti totali su 1015:<br><br>
    Hai trovato <span><?php echo $results->crystals_num; ?></span> cristalli su <span>14</span><br><br>
    Hai trovato <span><?php echo $results->hidden_crystals_num; ?></span> cristalli nascosti su <span>30</span><br><br>
    Hai sbagliato <span><?php echo $results->errors_num; ?></span> volte. <br><br>
</p>

<?php echo $widget_args['after_widget']; ?>