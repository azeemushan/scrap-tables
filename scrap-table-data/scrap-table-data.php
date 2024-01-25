<?php
/*
Plugin Name: Scrap Table Data
Description: Scrapes tables from specified URLs with pagination.
Version: 4.0
Author: Azeem
*/

defined('ABSPATH') or die;

require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/scraper.php';
require_once plugin_dir_path(__FILE__) . 'includes/display.php';
require_once plugin_dir_path(__FILE__) . 'includes/scheduler.php';

register_activation_hook(__FILE__, 'std_activate');
register_deactivation_hook(__FILE__, 'std_deactivate');

function std_enqueue_styles() {
    wp_enqueue_style('std-style', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'std_enqueue_styles');

function std_activate() {
    std_create_jobs_table();
    std_create_data_table();
    std_schedule_event();
}

function std_deactivate() {
    wp_clear_scheduled_hook('std_update_event');
}

function std_create_jobs_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'scrap_table_jobs';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        url varchar(255) NOT NULL,
        selector varchar(255) NULL,
        frequency varchar(20) NOT NULL,
        last_run datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function std_create_data_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'scrap_table_data';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        job_id mediumint(9) NOT NULL,
        content longtext NOT NULL,
        last_updated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
