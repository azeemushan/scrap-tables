<?php

function create_post_from_scraped_data($job_id, $scraped_data) {
    foreach ($scraped_data as $data) {
        $post_data = [
            'post_title'    => wp_strip_all_tags($data['name']),
            'post_content'  => $data['value'],
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_category' => [get_option('default_category')],
        ];

        wp_insert_post($post_data);
    }
}
