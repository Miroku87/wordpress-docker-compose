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
 * @subpackage Multiverso_Leaderboard/admin/partials
 */
?>

<?php settings_errors('multiverso-leaderboard-notices'); ?>

<div class="wrap">
    <h1><?php esc_html_e('Visualizza Classifica', $this->plugin_name); ?></h1>

    <p>
        Per aggiungere una voce alla classifica è necessario utilizzare il seguente endpoint REST:
        <code>POST https://viaggionelmultiverso.it/wp-json/multiverso-leaderboard/v1/entry</code><br>
        I parametri da inviare in formato <code>x-www-form-urlencoded</code> sono i seguenti:
            <ul>
				<li><code>school_name</code> - il nome della scuola a cui il gruppo appartiene</li>
				<li><code>class_name</code> - la classe a cui il gruppo appartiene</li>
				<li><code>group_name</code> - nome del gruppo che ha partecipato</li>
				<li><code>speedtale_id</code> - id numerico o nome della speedtale a cui il gruppo a partecipato</li>
				<li><code>total_score</code> - valore numerico del punteggio ottenuto</li>
				<li><code>elapsed_time_seconds</code> - valore numerico dei secondi impiegati per finire la speedtale</li>
            </ul>
    </p>

    <!-- Leaderboard records -->
    <?php require plugin_dir_path(__FILE__) . 'multiverso-leaderboard-admin-list.php'; ?>
</div>