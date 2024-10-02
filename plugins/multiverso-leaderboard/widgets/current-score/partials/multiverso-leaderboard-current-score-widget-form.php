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
$entry_id = ! empty( $widget_instance['entry_id'] ) ? $widget_instance['entry_id'] : "";
?>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Titolo:', $this->plugin_name ); ?></label> 
    <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'entry_id' ) ); ?>"><?php esc_attr_e( 'ID Voce Classifica:', $this->plugin_name ); ?></label>
    <input 
        class="widefat" 
        id="<?php echo esc_attr( $this->get_field_id( 'entry_id' ) ); ?>" 
        name="<?php echo esc_attr( $this->get_field_name( 'entry_id' ) ); ?>" 
        type="text" 
        value="<?php echo esc_attr( $entry_id ); ?>">
</p>