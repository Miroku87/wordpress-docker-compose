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

$title = ! empty( $widget_instance['title'] ) ? $widget_instance['title'] : '';
$timeframe = ! empty( $widget_instance['timeframe'] ) ? $widget_instance['timeframe'] : esc_html__( 'Completa', $this->plugin_name );
$speedtale_id = ! empty( $widget_instance['speedtale_id'] ) ? $widget_instance['speedtale_id'] : 'multiverso';
$entry_id = ! empty( $widget_instance['entry_id'] ) ? $widget_instance['entry_id'] : '';
?>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Titolo:', $this->plugin_name ); ?></label> 
    <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $title ); ?>"
        autocomplete="off">
</p>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>"><?php esc_attr_e( 'Timeframe:', $this->plugin_name ); ?></label>
    <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'timeframe' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'timeframe' ) ); ?>">
        <option value="complete" <?php selected( $timeframe, 'complete' ); ?>><?php esc_html_e( 'Completa', $this->plugin_name ); ?></option>
        <option value="today" <?php selected( $timeframe, 'today' ); ?>><?php esc_html_e( 'Oggi', $this->plugin_name ); ?></option>
    </select>
</p>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'speedtale_id' ) ); ?>"><?php esc_attr_e( 'Speedtale:', $this->plugin_name ); ?></label>
    <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'speedtale_id' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'speedtale_id' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $speedtale_id ); ?>"
        autocomplete="off">
</p>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'entry_id' ) ); ?>"><?php esc_attr_e( 'ID Voce Classifica:', $this->plugin_name ); ?></label>
    <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'entry_id' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'entry_id' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $entry_id ); ?>"
        autocomplete="off">
</p>