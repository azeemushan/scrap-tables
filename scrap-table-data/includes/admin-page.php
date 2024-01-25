<?php
function std_admin_menu() {
    add_menu_page('Scrap Table Data Settings', 'Scrap Table Data', 'manage_options', 'scrap-table-data', 'std_settings_page');
}
add_action('admin_menu', 'std_admin_menu');

function std_settings_page() {
    ?>
    <div class="wrap">
        <h1>Scrap Table Data Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('std-settings-group'); ?>
            <?php do_settings_sections('std-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">New Job URL</th>
                    <td><input type="text" name="std_new_job_url" value="" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Selector (Optional)</th>
                    <td><input type="text" name="std_new_job_selector" value="" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Frequency</th>
                    <td>
                        <select name="std_new_job_frequency">
                            <option value="hourly">Hourly</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button('Add New Job'); ?>
        </form>
    </div>
    <?php
}

function register_std_settings() {
    register_setting('std-settings-group', 'std_new_job_url');
    register_setting('std-settings-group', 'std_new_job_selector');
    register_setting('std-settings-group', 'std_new_job_frequency');
    // Handle saving new job data here
}
add_action('admin_init', 'register_std_settings');
