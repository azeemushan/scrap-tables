<?php

add_action('admin_menu', 'scrap_metal_prices_menu');

function scrap_metal_prices_menu() {
    add_menu_page('Scrap Metal Prices', 'Scrap Metal Prices', 'manage_options', 'scrap-metal-prices', 'scrap_metal_prices_page', 'dashicons-admin-generic');
}

function scrap_metal_prices_page() {
    $jobs_option_name = 'scrap_metal_prices_jobs';
    $jobs = get_option($jobs_option_name, []);

    if (isset($_POST['action'])) {
        $job_data = [
            'url' => $_POST['url'],
            'frequency' => $_POST['frequency']
        ];

        if ($_POST['action'] == 'add') {
            $jobs[] = $job_data;
            update_option($jobs_option_name, $jobs);

            // Perform initial scraping and post creation for the new job
            $scraped_data = fetch_metal_prices($job_data['url']);
            create_post_from_scraped_data(count($jobs) - 1, $scraped_data);
        } elseif ($_POST['action'] == 'update') {
            $jobs[intval($_POST['id'])] = $job_data;
            update_option($jobs_option_name, $jobs);
        } elseif ($_POST['action'] == 'delete') {
            array_splice($jobs, intval($_POST['id']), 1);
            update_option($jobs_option_name, $jobs);
        }
    }


    <div class="wrap">
        <h1>Scrap Metal Prices - Job Management</h1>
        <form method="post">
            <input type="hidden" name="action" value="add">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">URL:</th>
                    <td><input type="text" name="url" required></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Frequency (in hours):</th>
                    <td><input type="number" name="frequency" required></td>
                </tr>
            </table>
            <input type="submit" class="button-primary" value="Add New Job">
        </form>
        <h2>Existing Jobs</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Frequency</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $id => $job) : ?>
                    <tr>
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <td><?php echo $id; ?></td>
                            <td><input type="text" name="url" value="<?php echo $job['url']; ?>"></td>
                            <td><input type="number" name="frequency" value="<?php echo $job['frequency']; ?>"></td>
                            <td>
                                <input type="submit" name="action" class="button" value="update">
                                <input type="submit" name="action" class="button" value="delete">
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
