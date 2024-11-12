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
require_once plugin_dir_path(__FILE__) . '../../../includes/multiverso-leaderboard-constants.php';

global $wpdb;
$table_name = $wpdb->prefix . MULTIVERSO_LB_LEADERBOARD_TABLE_NAME;

foreach ($wpdb->get_col("DESC " . $table_name, 0) as $column_name) {
    $field_names[] = $column_name;
}

$title = ! empty($widget_instance['title']) ? $widget_instance['title'] : '';
$timeframe = ! empty($widget_instance['timeframe']) ? $widget_instance['timeframe'] : esc_html__('Completa', $this->plugin_name);
$speedtale_id = ! empty($widget_instance['speedtale_id']) ? $widget_instance['speedtale_id'] : 'multiverso';
$entry_id = ! empty($widget_instance['entry_id']) ? $widget_instance['entry_id'] : '';
$default_order_by_1 = ! empty($widget_instance['default_order_by_1']) ? $widget_instance['default_order_by_1'] : 'nessuno';
$default_order_by_2 = ! empty($widget_instance['default_order_by_2']) ? $widget_instance['default_order_by_2'] : 'nessuno';
$default_order_by_3 = ! empty($widget_instance['default_order_by_3']) ? $widget_instance['default_order_by_3'] : 'nessuno';
?>

<p>
    <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Titolo:', $this->plugin_name); ?></label>
    <input
        class="widefat"
        id="<?php echo esc_attr($this->get_field_id('title')); ?>"
        name="<?php echo esc_attr($this->get_field_name('title')); ?>"
        type="text"
        value="<?php echo esc_attr($title); ?>"
        autocomplete="off">
</p>

<p>
    <label for="<?php echo esc_attr($this->get_field_id('timeframe')); ?>"><?php esc_attr_e('Timeframe:', $this->plugin_name); ?></label>
    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('timeframe')); ?>" name="<?php echo esc_attr($this->get_field_name('timeframe')); ?>">
        <option value="complete" <?php selected($timeframe, 'complete'); ?>><?php esc_html_e('Completa', $this->plugin_name); ?></option>
        <option value="today" <?php selected($timeframe, 'today'); ?>><?php esc_html_e('Oggi', $this->plugin_name); ?></option>
    </select>
</p>

<p>
    <label for="<?php echo esc_attr($this->get_field_id('speedtale_id')); ?>"><?php esc_attr_e('Speedtale:', $this->plugin_name); ?></label>
    <input
        class="widefat"
        id="<?php echo esc_attr($this->get_field_id('speedtale_id')); ?>"
        name="<?php echo esc_attr($this->get_field_name('speedtale_id')); ?>"
        type="text"
        value="<?php echo esc_attr($speedtale_id); ?>"
        autocomplete="off">
</p>

<p>
    <label for="<?php echo esc_attr($this->get_field_id('entry_id')); ?>"><?php esc_attr_e('ID Voce Classifica:', $this->plugin_name); ?></label>
    <input
        class="widefat"
        id="<?php echo esc_attr($this->get_field_id('entry_id')); ?>"
        name="<?php echo esc_attr($this->get_field_name('entry_id')); ?>"
        type="text"
        value="<?php echo esc_attr($entry_id); ?>"
        autocomplete="off">
</p>

<p>
    <label for="<?php echo esc_attr($this->get_field_id('default_order_by_1')); ?>"><?php esc_attr_e('Ordinamento di default 1:', $this->plugin_name); ?></label>
    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('default_order_by_1')); ?>" name="<?php echo esc_attr($this->get_field_name('default_order_by_1')); ?>">
        <option value="nessuno" <?php selected($default_order_by_1, $fn); ?>>Nessuno</option>
        <?php
        foreach ($field_names as $fn) : ?>
            <option value="<?php echo $fn . ":asc"; ?>" <?php selected($default_order_by_1, $fn); ?>><?php echo $fn . " crescente"; ?></option>
            <option value="<?php echo $fn . ":desc"; ?>" <?php selected($default_order_by_1, $fn); ?>><?php echo $fn . " decrescente"; ?></option>
        <?php endforeach; ?>
        <option value="elapsed_time:asc" <?php selected($default_order_by_1, $fn); ?>>elapsed_time crescente</option>
        <option value="elapsed_time:desc" <?php selected($default_order_by_1, $fn); ?>>elapsed_time decrescente</option>
    </select>
</p>

<p>
    <label for="<?php echo esc_attr($this->get_field_id('default_order_by_2')); ?>"><?php esc_attr_e('Ordinamento di default 2:', $this->plugin_name); ?></label>
    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('default_order_by_2')); ?>" name="<?php echo esc_attr($this->get_field_name('default_order_by_2')); ?>">
        <option value="nessuno" <?php selected($default_order_by_2, $fn); ?>>Nessuno</option>
        <?php
        foreach ($field_names as $fn) : ?>
            <option value="<?php echo $fn . ":asc"; ?>" <?php selected($default_order_by_2, $fn); ?>><?php echo $fn . " crescente"; ?></option>
            <option value="<?php echo $fn . ":desc"; ?>" <?php selected($default_order_by_2, $fn); ?>><?php echo $fn . " decrescente"; ?></option>
        <?php endforeach; ?>
        <option value="elapsed_time:asc" <?php selected($default_order_by_2, $fn); ?>>elapsed_time crescente</option>
        <option value="elapsed_time:desc" <?php selected($default_order_by_2, $fn); ?>>elapsed_time decrescente</option>
    </select>
</p>

<p>
    <label for="<?php echo esc_attr($this->get_field_id('default_order_by_3')); ?>"><?php esc_attr_e('Ordinamento di default 3:', $this->plugin_name); ?></label>
    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('default_order_by_3')); ?>" name="<?php echo esc_attr($this->get_field_name('default_order_by_3')); ?>">
        <option value="nessuno" <?php selected($default_order_by_3, $fn); ?>>Nessuno</option>
        <?php
        foreach ($field_names as $fn) : ?>
            <option value="<?php echo $fn . ":asc"; ?>" <?php selected($default_order_by_3, $fn); ?>><?php echo $fn . " crescente"; ?></option>
            <option value="<?php echo $fn . ":desc"; ?>" <?php selected($default_order_by_3, $fn); ?>><?php echo $fn . " decrescente"; ?></option>
        <?php endforeach; ?>
        <option value="elapsed_time:asc" <?php selected($default_order_by_3, $fn); ?>>elapsed_time crescente</option>
        <option value="elapsed_time:desc" <?php selected($default_order_by_3, $fn); ?>>elapsed_time decrescente</option>
    </select>
</p>