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

$title = ! empty( $widget_instance['title'] ) ? $widget_instance['title'] : esc_html__( 'Classifica', 'text_domain' );
?>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Titolo:', 'text_domain' ); ?></label> 
    <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>"><?php esc_attr_e( 'Timeframe:', 'text_domain' ); ?></label>
    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'timeframe' ) ); ?>">
        <option value="complete" <?php selected( $timeframe, 'complete' ); ?>><?php esc_html_e( 'Completa', 'text_domain' ); ?></option>
        <option value="today" <?php selected( $timeframe, 'today' ); ?>><?php esc_html_e( 'Oggi', 'text_domain' ); ?></option>
    </select>
</p>