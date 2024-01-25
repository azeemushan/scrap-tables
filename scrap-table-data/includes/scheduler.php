<?php
function std_schedule_event() {
    if (!wp_next_scheduled('std_update_event')) {
        wp_schedule_event(time(), 'hourly', 'std_update_event');
    }
}

function std_update_data() {
    global $wpdb;
    $jobs_table_name = $wpdb->prefix . 'scrap_table_jobs';
    $data_table_name = $wpdb->prefix . 'scrap_table_data';

    $jobs = $wpdb->get_results("SELECT * FROM $jobs_table_name WHERE last_run <= NOW() - INTERVAL 1 HOUR");

    foreach ($jobs as $job) {
        $tables = std_scrape_table($job->url, $job->selector);
        foreach ($tables as $table) {
            $wpdb->replace(
                $data_table_name,
                array(
                    'job_id' => $job->id,
                    'content' => $table,
                    'last_updated' => current_time('mysql')
                ),
                array('%d', '%s', '%s')
            );
        }
        $wpdb->update($jobs_table_name, array('last_run' => current_time('mysql')), array('id' => $job->id));
    }
}
add_action('std_update_event', 'std_update_data');
