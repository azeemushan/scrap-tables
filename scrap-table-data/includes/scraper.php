<?php
function std_scrape_table($url, $selector = '') {
    $tables = [];
    $response = std_fetch_url($url);

    if (empty($response)) {
        return $tables; // Return empty array if no response
    }

    // Parse and add tables from the current page
    $tables = array_merge($tables, std_parse_tables($response, $selector));

    // Check for pagination and recursively scrape additional pages
    $nextPageUrl = std_find_next_page($response);
    while ($nextPageUrl) {
        $nextPageResponse = std_fetch_url($nextPageUrl);
        if (empty($nextPageResponse)) {
            break;
        }

        $tables = array_merge($tables, std_parse_tables($nextPageResponse, $selector));
        $nextPageUrl = std_find_next_page($nextPageResponse);
    }

    return $tables;
}

function std_fetch_url($url) {
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return ''; // Return empty string on error
    }

    return wp_remote_retrieve_body($response);
}

function std_parse_tables($html, $selector) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $query = $selector ? "//table[contains(@class, '$selector')]" : '//table';
    $nodes = $xpath->query($query);
    $tables = [];

    foreach ($nodes as $node) {
        $tables[] = $dom->saveHTML($node);
    }

    return $tables;
}

function std_find_next_page($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $paginationLink = $xpath->query("//a[contains(@href, 'page_start=')]");
    if ($paginationLink->length > 0) {
        return $paginationLink->item(0)->getAttribute('href');
    }

    return ''; // Return empty string if no next page
}
