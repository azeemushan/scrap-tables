<?php

register_activation_hook(__FILE__, 'scrap_metal_prices_activate');
register_deactivation_hook(__FILE__, 'scrap_metal_prices_deactivate');

function scrap_metal_prices_activate() {
    schedule_scraping_jobs();
}

function scrap_metal_prices_deactivate() {
    $jobs = get_option('scrap_metal_prices_jobs', []);
    foreach ($jobs as $id => $job) {
        $hook = 'scrap_metal_prices_cron_' . $id;
        wp_clear_scheduled_hook($hook);
    }
}

function schedule_scraping_jobs() {
    $jobs = get_option('scrap_metal_prices_jobs', []);

    foreach ($jobs as $id => $job) {
        $hook = 'scrap_metal_prices_cron_' . $id;
        if (!wp_next_scheduled($hook)) {
            wp_schedule_event(time(), 'hourly', $hook);
        }
    }
}

add_action('init', 'register_custom_cron_intervals');

function register_custom_cron_intervals() {
    add_filter('cron_schedules', 'add_custom_cron_intervals');
}

function add_custom_cron_intervals($schedules) {
    $jobs = get_option('scrap_metal_prices_jobs', []);

    foreach ($jobs as $id => $job) {
        $interval = intval($job['frequency']) * HOUR_IN_SECONDS;
        $schedules['scrap_metal_prices_interval_' . $id] = [
            'interval' => $interval,
            'display' => 'Every ' . $job['frequency'] . ' Hours'
        ];
    }

    return $schedules;
}

$jobs = get_option('scrap_metal_prices_jobs', []);
foreach ($jobs as $id => $job) {
    $hook = 'scrap_metal_prices_cron_' . $id;
    add_action($hook, function() use ($job) {
        $scraped_data = fetch_metal_prices($job['url']);
        create_post_from_scraped_data($id, $scraped_data);
    });
}
