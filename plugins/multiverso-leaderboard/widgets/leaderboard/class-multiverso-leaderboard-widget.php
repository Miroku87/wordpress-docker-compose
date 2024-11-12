<?php

/**
 * Adds Multiverso_Leaderboard_Widget widget.
 */
class Multiverso_Leaderboard_Widget extends WP_Widget
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Register widget with WordPress.
     */
    function __construct($plugin_name, $version)
    {
        parent::__construct(
            'multiverso_leaderboard_widget', // Base ID
            esc_html__('Classifica', $plugin_name), // Name
            array('description' => esc_html__('Un widget per mostrare la classifica', $plugin_name),) // Args
        );

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($widget_args, $widget_instance)
    { // Enqueue the custom CSS for the widget
        wp_enqueue_style('multiverso-leaderboard-widget-main', plugin_dir_url(__FILE__) . 'css/multiverso-leaderboard-widget.css');
        wp_enqueue_style('vanilla-datatable', plugin_dir_url(__FILE__) . 'css/vanilla-datatable.css');
        wp_enqueue_script_module('multiverso-leaderboard-widget-main', plugin_dir_url(__FILE__) . 'js/multiverso-leaderboard-widget.js', array(), $this->version, true);

        require plugin_dir_path(__FILE__) . 'partials/multiverso-leaderboard-widget-list.php';
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($widget_instance)
    {
        require plugin_dir_path(__FILE__) . 'partials/multiverso-leaderboard-widget-form.php';
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (! empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['timeframe'] = (! empty($new_instance['timeframe'])) ? sanitize_text_field($new_instance['timeframe']) : 'complete';
        $instance['entry_id'] = (! empty($new_instance['entry_id'])) ? sanitize_text_field($new_instance['entry_id']) : '';
        $instance['speedtale_id'] = (! empty($new_instance['speedtale_id'])) ? sanitize_text_field($new_instance['speedtale_id']) : '';
        $instance['default_order_by_1'] = (! empty($new_instance['default_order_by_1'])) ? sanitize_text_field($new_instance['default_order_by_1']) : '';
        $instance['default_order_by_2'] = (! empty($new_instance['default_order_by_2'])) ? sanitize_text_field($new_instance['default_order_by_2']) : '';
        $instance['default_order_by_3'] = (! empty($new_instance['default_order_by_3'])) ? sanitize_text_field($new_instance['default_order_by_3']) : '';

        return $instance;
    }
}
