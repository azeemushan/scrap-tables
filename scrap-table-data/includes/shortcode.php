<?php


function scrap_table_data_admin_menu() {
    add_menu_page('Scrap Table Data Settings', 'Scrap Table Data', 'manage_options', 'scrap-table-data', 'scrap_table_data_settings_page');
}
add_action('admin_menu', 'scrap_table_data_admin_menu');

function scrap_table_data_settings_page() {
    // Admin settings page HTML
}

function register_scrap_table_data_settings() {
    // Register settings
}
add_action('admin_init', 'register_scrap_table_data_settings');
