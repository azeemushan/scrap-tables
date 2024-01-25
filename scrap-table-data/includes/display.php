<?php
function std_display_shortcode($atts) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'scrap_table_data';

    $query = "SELECT * FROM $table_name ORDER BY last_updated DESC";
    $rows = $wpdb->get_results($query);

    if (empty($rows)) {
        return 'No data available.';
    }

    $output = '<div class="std-table-container">';
    foreach ($rows as $row) {
        $output .= '<div class="std-job">';
        $output .= '<h3>Job ID: ' . esc_html($row->job_id) . '</h3>';
        $output .= '<div>' . $row->content . '</div>';
        $output .= '<p>Last Updated: ' . esc_html($row->last_updated) . '</p>';
        $output .= '</div>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('std_display', 'std_display_shortcode');
